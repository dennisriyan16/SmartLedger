<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Services\PaymentStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentStatusService $statusService
    ) {}

    public function storePurchasePayment(PaymentRequest $request, PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $balance = (float) $purchaseInvoice->balance_due;

        if ($validated['amount'] > $balance) {
            return back()->with('error', 'Payment amount ($'.number_format($validated['amount'], 2).') cannot exceed the remaining balance ($'.number_format($balance, 2).').');
        }

        DB::transaction(function () use ($purchaseInvoice, $validated) {
            $purchaseInvoice->payments()->create($validated);
            $this->statusService->syncInvoicePayment($purchaseInvoice);
        });

        return back()->with('success', 'Payment of $'.number_format($validated['amount'], 2).' recorded successfully.');
    }

    public function storeSalesPayment(PaymentRequest $request, SalesInvoice $salesInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $balance = (float) $salesInvoice->balance_due;

        if ($validated['amount'] > $balance) {
            return back()->with('error', 'Payment amount ($'.number_format($validated['amount'], 2).') cannot exceed the outstanding balance ($'.number_format($balance, 2).').');
        }

        DB::transaction(function () use ($salesInvoice, $validated) {
            $salesInvoice->payments()->create($validated);
            $this->statusService->syncInvoicePayment($salesInvoice);
        });

        return back()->with('success', 'Payment of $'.number_format($validated['amount'], 2).' recorded successfully.');
    }
}
