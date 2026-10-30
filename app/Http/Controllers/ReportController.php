<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date', Carbon::today()->startOfYear()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());
        $activeTab = $request->query('tab', 'profit-loss');

        // 1. Sales Summary
        $salesSummary = SalesInvoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total_count, SUM(subtotal) as subtotal, SUM(discount_total) as discount, SUM(tax_total) as tax, SUM(grand_total) as grand_total, SUM(paid_amount) as paid')
            ->first();

        // 2. Purchase Summary
        $purchaseSummary = PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total_count, SUM(subtotal) as subtotal, SUM(discount_total) as discount, SUM(tax_total) as tax, SUM(grand_total) as grand_total, SUM(paid_amount) as paid')
            ->first();

        // 3. Profit & Loss: Sales - Purchases - Expenses
        $totalSales = (float) ($salesSummary->grand_total ?? 0);
        $totalPurchases = (float) ($purchaseSummary->grand_total ?? 0);
        $totalExpenses = (float) Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
        $netProfit = $totalSales - $totalPurchases - $totalExpenses;

        // 4. Tax Collected vs Paid
        $taxCollected = (float) ($salesSummary->tax ?? 0);
        $taxPaid = (float) ($purchaseSummary->tax ?? 0);
        $netTaxPayable = $taxCollected - $taxPaid;

        // 5. Budget vs Actual
        $budgets = Budget::where('year', Carbon::parse($startDate)->year)->get();
        $budgetVsActual = [];
        foreach ($budgets as $b) {
            $spent = (float) Expense::where('category', $b->category)
                ->whereBetween('expense_date', [$startDate, $endDate])
                ->sum('amount');
            $allocated = (float) $b->amount;
            $variance = $allocated - $spent;

            $budgetVsActual[] = [
                'name' => $b->name,
                'category' => $b->category,
                'type' => $b->type,
                'allocated' => $allocated,
                'spent' => $spent,
                'variance' => $variance,
                'pct' => $allocated > 0 ? round(($spent / $allocated) * 100, 1) : 0,
            ];
        }

        // 6. Outstanding Receivables & Payables
        $receivables = SalesInvoice::with('customer')
            ->where('status', '!=', 'paid')
            ->selectRaw('*, (grand_total - paid_amount) as balance')
            ->orderBy('due_date')
            ->take(20)
            ->get();

        $payables = PurchaseInvoice::with('supplier')
            ->where('status', '!=', 'paid')
            ->selectRaw('*, (grand_total - paid_amount) as balance')
            ->orderBy('due_date')
            ->take(20)
            ->get();

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'activeTab',
            'salesSummary',
            'purchaseSummary',
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'netProfit',
            'taxCollected',
            'taxPaid',
            'netTaxPayable',
            'budgetVsActual',
            'receivables',
            'payables'
        ));
    }

    /**
     * Stream CSV download for reports.
     */
    public function export(Request $request): StreamedResponse
    {
        $reportType = $request->query('type', 'profit-loss');
        $startDate = $request->query('start_date', Carbon::today()->startOfYear()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $fileName = "report_{$reportType}_{$startDate}_to_{$endDate}.csv";

        return response()->streamDownload(function () use ($reportType, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');

            if ($reportType === 'sales') {
                fputcsv($handle, ['Invoice No', 'Customer', 'Date', 'Due Date', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Paid', 'Status']);
                $sales = SalesInvoice::with('customer')
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->cursor();
                foreach ($sales as $s) {
                    fputcsv($handle, [
                        $s->invoice_no,
                        $s->customer?->name ?? 'N/A',
                        $s->invoice_date->toDateString(),
                        $s->due_date->toDateString(),
                        $s->subtotal,
                        $s->discount_total,
                        $s->tax_total,
                        $s->grand_total,
                        $s->paid_amount,
                        $s->status,
                    ]);
                }
            } elseif ($reportType === 'purchases') {
                fputcsv($handle, ['Invoice No', 'Supplier', 'Date', 'Due Date', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Paid', 'Status']);
                $purchases = PurchaseInvoice::with('supplier')
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->cursor();
                foreach ($purchases as $p) {
                    fputcsv($handle, [
                        $p->invoice_no,
                        $p->supplier?->name ?? 'N/A',
                        $p->invoice_date->toDateString(),
                        $p->due_date->toDateString(),
                        $p->subtotal,
                        $p->discount_total,
                        $p->tax_total,
                        $p->grand_total,
                        $p->paid_amount,
                        $p->status,
                    ]);
                }
            } elseif ($reportType === 'tax') {
                fputcsv($handle, ['Tax Category', 'Total Amount ($)']);
                $salesTax = (float) SalesInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('tax_total');
                $purchaseTax = (float) PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('tax_total');
                fputcsv($handle, ['Tax Collected (Output GST/Sales Tax)', number_format($salesTax, 2, '.', '')]);
                fputcsv($handle, ['Tax Paid (Input Tax Credit/Purchases)', number_format($purchaseTax, 2, '.', '')]);
                fputcsv($handle, ['Net Tax Liability (Payable to Government)', number_format($salesTax - $purchaseTax, 2, '.', '')]);
            } elseif ($reportType === 'budget') {
                fputcsv($handle, ['Budget Name', 'Category', 'Period Type', 'Allocated Cap ($)', 'Actual Expenses ($)', 'Variance ($)']);
                $budgets = Budget::all();
                foreach ($budgets as $b) {
                    $spent = (float) Expense::where('category', $b->category)->whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
                    fputcsv($handle, [
                        $b->name,
                        $b->category,
                        $b->type,
                        $b->amount,
                        number_format($spent, 2, '.', ''),
                        number_format((float) $b->amount - $spent, 2, '.', ''),
                    ]);
                }
            } elseif ($reportType === 'receivables') {
                fputcsv($handle, ['Invoice No', 'Customer', 'Date', 'Due Date', 'Grand Total ($)', 'Paid ($)', 'Balance Due ($)', 'Status']);
                $receivables = SalesInvoice::with('customer')->where('status', '!=', 'paid')->get();
                foreach ($receivables as $r) {
                    fputcsv($handle, [
                        $r->invoice_no,
                        $r->customer?->name ?? 'N/A',
                        $r->invoice_date->toDateString(),
                        $r->due_date->toDateString(),
                        $r->grand_total,
                        $r->paid_amount,
                        $r->balance_due,
                        $r->status,
                    ]);
                }
            } else {
                // Profit / Loss summary
                fputcsv($handle, ['Metric', 'Amount ($)']);
                $totalSales = (float) SalesInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('grand_total');
                $totalPurchases = (float) PurchaseInvoice::whereBetween('invoice_date', [$startDate, $endDate])->sum('grand_total');
                $totalExpenses = (float) Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');
                $netProfit = $totalSales - $totalPurchases - $totalExpenses;

                fputcsv($handle, ['Total Gross Sales', number_format($totalSales, 2, '.', '')]);
                fputcsv($handle, ['Total Purchases / Cost of Goods', number_format($totalPurchases, 2, '.', '')]);
                fputcsv($handle, ['Total Operational Expenses', number_format($totalExpenses, 2, '.', '')]);
                fputcsv($handle, ['Net Estimated Profit / Loss', number_format($netProfit, 2, '.', '')]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
