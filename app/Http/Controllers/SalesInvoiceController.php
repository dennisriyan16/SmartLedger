<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesInvoiceRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Services\InvoiceCalculatorService;
use App\Services\PaymentStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function __construct(
        protected InvoiceCalculatorService $calculator,
        protected PaymentStatusService $statusService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $customerId = $request->query('customer_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        $invoices = SalesInvoice::with('customer')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->when($startDate, fn ($q) => $q->whereDate('invoice_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('invoice_date', '<=', $endDate))
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('invoice_date')
            ->paginate(15)
            ->withQueryString();

        $customers = Customer::orderBy('name')->get();

        return view('sales-invoices.index', compact('invoices', 'customers', 'status', 'customerId', 'startDate', 'endDate', 'search'));
    }

    public function create(Request $request): View
    {
        $selectedCustomerId = $request->query('customer_id');
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        $year = date('Y');
        $lastInv = SalesInvoice::whereYear('created_at', $year)->latest('id')->first();
        $nextNum = $lastInv ? ((int) substr($lastInv->invoice_no, -4)) + 1 : 1;
        $suggestedNo = sprintf('SI-%s-%04d', $year, $nextNum);

        return view('sales-invoices.create', compact('customers', 'products', 'selectedCustomerId', 'suggestedNo'));
    }

    public function store(SalesInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $calc = $this->calculator->calculateInvoice($validated['items']);

        $invoice = DB::transaction(function () use ($validated, $calc) {
            $year = date('Y');
            $count = SalesInvoice::whereYear('created_at', $year)->count() + 1;
            $invoiceNo = sprintf('SI-%s-%04d', $year, $count);

            while (SalesInvoice::where('invoice_no', $invoiceNo)->exists()) {
                $count++;
                $invoiceNo = sprintf('SI-%s-%04d', $year, $count);
            }

            $salesInvoice = SalesInvoice::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $calc['subtotal'],
                'discount_total' => $calc['discount_total'],
                'tax_total' => $calc['tax_total'],
                'grand_total' => $calc['grand_total'],
                'paid_amount' => '0.00',
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($calc['items'] as $item) {
                $salesInvoice->items()->create([
                    'product_id' => $item['product_id'] ?: null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'],
                    'tax_percent' => $item['tax_percent'],
                    'line_total' => $item['line_total'],
                ]);
            }

            $this->statusService->syncInvoicePayment($salesInvoice);

            return $salesInvoice;
        });

        return redirect()->route('sales-invoices.show', $invoice)
            ->with('success', "Sales invoice '{$invoice->invoice_no}' created successfully.");
    }

    public function show(SalesInvoice $salesInvoice): View
    {
        $salesInvoice->load(['customer', 'items.product', 'payments' => fn ($q) => $q->latest('payment_date')]);

        return view('sales-invoices.show', compact('salesInvoice'));
    }

    public function edit(SalesInvoice $salesInvoice): View
    {
        $salesInvoice->load(['items.product']);
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('sales-invoices.edit', compact('salesInvoice', 'customers', 'products'));
    }

    public function update(SalesInvoiceRequest $request, SalesInvoice $salesInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $calc = $this->calculator->calculateInvoice($validated['items']);

        DB::transaction(function () use ($salesInvoice, $validated, $calc) {
            $salesInvoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $calc['subtotal'],
                'discount_total' => $calc['discount_total'],
                'tax_total' => $calc['tax_total'],
                'grand_total' => $calc['grand_total'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $salesInvoice->items()->delete();
            foreach ($calc['items'] as $item) {
                $salesInvoice->items()->create([
                    'product_id' => $item['product_id'] ?: null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'],
                    'tax_percent' => $item['tax_percent'],
                    'line_total' => $item['line_total'],
                ]);
            }

            $this->statusService->syncInvoicePayment($salesInvoice);
        });

        return redirect()->route('sales-invoices.show', $salesInvoice)
            ->with('success', "Sales invoice '{$salesInvoice->invoice_no}' updated successfully.");
    }

    public function destroy(SalesInvoice $salesInvoice): RedirectResponse
    {
        $no = $salesInvoice->invoice_no;
        $salesInvoice->delete();

        return redirect()->route('sales-invoices.index')
            ->with('success', "Sales invoice '{$no}' moved to trash.");
    }
}
