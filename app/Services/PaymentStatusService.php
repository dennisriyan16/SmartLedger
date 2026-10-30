<?php

namespace App\Services;

use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Carbon\Carbon;

class PaymentStatusService
{
    /**
     * Determine invoice payment status based on paid amount, grand total and due date.
     *
     * Rules:
     * - Paid: paid_amount >= grand_total
     * - Overdue: paid_amount < grand_total AND due_date < today
     * - Pending: paid_amount < grand_total AND due_date >= today
     */
    public function determineStatus(string|float $grandTotal, string|float $paidAmount, Carbon|string $dueDate): string
    {
        $gt = number_format((float) $grandTotal, 2, '.', '');
        $paid = number_format((float) $paidAmount, 2, '.', '');
        $due = is_string($dueDate) ? Carbon::parse($dueDate)->startOfDay() : $dueDate->copy()->startOfDay();
        $today = Carbon::today();

        // If paid >= grand_total
        if (bccomp($paid, $gt, 2) >= 0 && bccomp($gt, '0', 2) > 0) {
            return 'paid';
        }

        if ($due->lt($today)) {
            return 'overdue';
        }

        return 'pending';
    }

    /**
     * Update status and paid_amount of a purchase or sales invoice.
     */
    public function syncInvoicePayment(PurchaseInvoice|SalesInvoice $invoice): void
    {
        $paidTotal = (string) $invoice->payments()->sum('amount');
        $paidTotalFormatted = number_format((float) $paidTotal, 2, '.', '');
        $status = $this->determineStatus($invoice->grand_total, $paidTotalFormatted, $invoice->due_date);

        $invoice->update([
            'paid_amount' => $paidTotalFormatted,
            'status' => $status,
        ]);
    }

    /**
     * Update overdue status for all non-paid invoices past their due date.
     * Returns count of updated invoices.
     */
    public function updateAllOverdueInvoices(): int
    {
        $today = Carbon::today()->toDateString();
        $count = 0;

        $overduePurchases = PurchaseInvoice::where('status', 'pending')
            ->where('due_date', '<', $today)
            ->whereColumn('paid_amount', '<', 'grand_total')
            ->update(['status' => 'overdue']);

        $overdueSales = SalesInvoice::where('status', 'pending')
            ->where('due_date', '<', $today)
            ->whereColumn('paid_amount', '<', 'grand_total')
            ->update(['status' => 'overdue']);

        return $overduePurchases + $overdueSales;
    }
}
