<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseInvoiceRequest;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Services\InvoiceCalculatorService;
use App\Services\PaymentStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseInvoiceController extends Controller
{
    public function __construct(
        protected InvoiceCalculatorService $calculator,
        protected PaymentStatusService $statusService
    ) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $supplierId = $request->query('supplier_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        $invoices = PurchaseInvoice::with('supplier')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->when($startDate, fn ($q) => $q->whereDate('invoice_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('invoice_date', '<=', $endDate))
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('invoice_no', 'like', "%{$search}%")
                        ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('invoice_date')
            ->paginate(15)
            ->withQueryString();

        $suppliers = Supplier::orderBy('name')->get();

        return view('purchase-invoices.index', compact('invoices', 'suppliers', 'status', 'supplierId', 'startDate', 'endDate', 'search'));
    }

    public function create(Request $request): View
    {
        $selectedSupplierId = $request->query('supplier_id');
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        // Generate next invoice number
        $year = date('Y');
        $lastInv = PurchaseInvoice::whereYear('created_at', $year)->latest('id')->first();
        $nextNum = $lastInv ? ((int) substr($lastInv->invoice_no, -4)) + 1 : 1;
        $suggestedNo = sprintf('PI-%s-%04d', $year, $nextNum);

        return view('purchase-invoices.create', compact('suppliers', 'products', 'selectedSupplierId', 'suggestedNo'));
    }

    public function store(PurchaseInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $calc = $this->calculator->calculateInvoice($validated['items']);

        $invoice = DB::transaction(function () use ($validated, $calc) {
            $year = date('Y');
            $count = PurchaseInvoice::whereYear('created_at', $year)->count() + 1;
            $invoiceNo = sprintf('PI-%s-%04d', $year, $count);

            // Ensure unique invoice_no in case of race
            while (PurchaseInvoice::where('invoice_no', $invoiceNo)->exists()) {
                $count++;
                $invoiceNo = sprintf('PI-%s-%04d', $year, $count);
            }

            $purchaseInvoice = PurchaseInvoice::create([
                'invoice_no' => $invoiceNo,
                'supplier_id' => $validated['supplier_id'],
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
                $purchaseInvoice->items()->create([
                    'product_id' => $item['product_id'] ?: null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'],
                    'tax_percent' => $item['tax_percent'],
                    'line_total' => $item['line_total'],
                ]);
            }

            // Sync overdue status if applicable
            $this->statusService->syncInvoicePayment($purchaseInvoice);

            return $purchaseInvoice;
        });

        return redirect()->route('purchase-invoices.show', $invoice)
            ->with('success', "Purchase invoice '{$invoice->invoice_no}' created successfully.");
    }

    public function show(PurchaseInvoice $purchaseInvoice): View
    {
        $purchaseInvoice->load(['supplier', 'items.product', 'payments' => fn ($q) => $q->latest('payment_date')]);

        return view('purchase-invoices.show', compact('purchaseInvoice'));
    }

    public function edit(PurchaseInvoice $purchaseInvoice): View
    {
        $purchaseInvoice->load(['items.product']);
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchase-invoices.edit', compact('purchaseInvoice', 'suppliers', 'products'));
    }

    public function update(PurchaseInvoiceRequest $request, PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $calc = $this->calculator->calculateInvoice($validated['items']);

        DB::transaction(function () use ($purchaseInvoice, $validated, $calc) {
            $purchaseInvoice->update([
                'supplier_id' => $validated['supplier_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $calc['subtotal'],
                'discount_total' => $calc['discount_total'],
                'tax_total' => $calc['tax_total'],
                'grand_total' => $calc['grand_total'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Re-sync items
            $purchaseInvoice->items()->delete();
            foreach ($calc['items'] as $item) {
                $purchaseInvoice->items()->create([
                    'product_id' => $item['product_id'] ?: null,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => $item['discount_percent'],
                    'tax_percent' => $item['tax_percent'],
                    'line_total' => $item['line_total'],
                ]);
            }

            $this->statusService->syncInvoicePayment($purchaseInvoice);
        });

        return redirect()->route('purchase-invoices.show', $purchaseInvoice)
            ->with('success', "Purchase invoice '{$purchaseInvoice->invoice_no}' updated successfully.");
    }

    public function destroy(PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        $no = $purchaseInvoice->invoice_no;
        $purchaseInvoice->delete();

        return redirect()->route('purchase-invoices.index')
            ->with('success', "Purchase invoice '{$no}' moved to trash.");
    }
}
