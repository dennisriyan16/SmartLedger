<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Services\ExchangeRateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected ExchangeRateService $exchangeService
    ) {}

    public function index(Request $request): View
    {
        $baseCurrency = $request->query('base_currency', 'USD');

        // 1. Core Summary Metrics
        $totalSales = (float) SalesInvoice::sum('grand_total');
        $totalPurchases = (float) PurchaseInvoice::sum('grand_total');

        $receivables = (float) SalesInvoice::where('status', '!=', 'paid')
            ->selectRaw('SUM(grand_total - paid_amount) as due')
            ->value('due') ?? 0.00;

        $payables = (float) PurchaseInvoice::where('status', '!=', 'paid')
            ->selectRaw('SUM(grand_total - paid_amount) as due')
            ->value('due') ?? 0.00;

        $overdueReceivables = (float) SalesInvoice::where('status', 'overdue')
            ->selectRaw('SUM(grand_total - paid_amount) as due')
            ->value('due') ?? 0.00;

        $overduePayables = (float) PurchaseInvoice::where('status', 'overdue')
            ->selectRaw('SUM(grand_total - paid_amount) as due')
            ->value('due') ?? 0.00;

        $overdueTotal = $overdueReceivables + $overduePayables;

        $currentYear = (int) date('Y');
        $totalBudget = (float) Budget::where('year', $currentYear)->sum('amount');
        $totalExpenses = (float) Expense::whereYear('expense_date', $currentYear)->sum('amount');

        // Net profit estimate = Sales - Purchases - Expenses
        $netProfit = $totalSales - $totalPurchases - $totalExpenses;

        // 2. Chart 1: Monthly Sales vs Purchases (Last 6 months)
        $monthlyLabels = [];
        $monthlySalesData = [];
        $monthlyPurchasesData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::today()->subMonths($i);
            $y = $monthDate->year;
            $m = $monthDate->month;
            $monthlyLabels[] = $monthDate->format('M Y');

            $mSales = (float) SalesInvoice::whereYear('invoice_date', $y)
                ->whereMonth('invoice_date', $m)
                ->sum('grand_total');

            $mPurchases = (float) PurchaseInvoice::whereYear('invoice_date', $y)
                ->whereMonth('invoice_date', $m)
                ->sum('grand_total');

            $monthlySalesData[] = round($mSales, 2);
            $monthlyPurchasesData[] = round($mPurchases, 2);
        }

        // 3. Chart 2: Expenses by Category
        $expensesByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $expenseCategories = $expensesByCategory->pluck('category')->toArray();
        $expenseCategoryAmounts = $expensesByCategory->pluck('total')->map(fn ($v) => round((float) $v, 2))->toArray();

        // 4. Chart 3: Invoice Status Distribution
        $salesStatusCounts = [
            'paid' => SalesInvoice::where('status', 'paid')->count(),
            'pending' => SalesInvoice::where('status', 'pending')->count(),
            'overdue' => SalesInvoice::where('status', 'overdue')->count(),
        ];

        // 5. Recent Invoices (Latest 5 Sales & 5 Purchases)
        $recentSales = SalesInvoice::with('customer')->latest('invoice_date')->take(5)->get();
        $recentPurchases = PurchaseInvoice::with('supplier')->latest('invoice_date')->take(5)->get();

        // 6. Overdue Invoices Alert List
        $overdueInvoices = SalesInvoice::with('customer')
            ->where('status', 'overdue')
            ->latest('due_date')
            ->take(5)
            ->get();

        // 7. Live Exchange Rates from Frankfurter API
        $exchangeData = $this->exchangeService->getLatestRates($baseCurrency);

        return view('dashboard', compact(
            'totalSales',
            'totalPurchases',
            'receivables',
            'payables',
            'overdueTotal',
            'overdueReceivables',
            'overduePayables',
            'totalBudget',
            'totalExpenses',
            'netProfit',
            'monthlyLabels',
            'monthlySalesData',
            'monthlyPurchasesData',
            'expenseCategories',
            'expenseCategoryAmounts',
            'salesStatusCounts',
            'recentSales',
            'recentPurchases',
            'overdueInvoices',
            'exchangeData',
            'baseCurrency'
        ));
    }
}
