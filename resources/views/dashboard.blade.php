@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Financial summary & cashflow')

@section('content')
<!-- Core Summary Stat Cards -->
<div class="row g-2 mb-3">
    <!-- Total Sales -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="kpi-title">Total Sales</div>
                <div class="kpi-value">${{ number_format($totalSales, 2) }}</div>
            </div>
            <div class="kpi-icon bg-primary-subtle text-primary">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
        </div>
    </div>

    <!-- Total Purchases -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="kpi-title">Total Purchases</div>
                <div class="kpi-value text-secondary">${{ number_format($totalPurchases, 2) }}</div>
            </div>
            <div class="kpi-icon bg-warning-subtle text-warning">
                <i class="bi bi-cart3"></i>
            </div>
        </div>
    </div>

    <!-- Outstanding Receivables -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="kpi-title">Pending Receivables</div>
                <div class="kpi-value text-dark">${{ number_format($receivables, 2) }}</div>
            </div>
            <div class="kpi-icon bg-info-subtle text-info">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card h-100 d-flex justify-content-between align-items-center">
            <div>
                <div class="kpi-title">Estimated Profit</div>
                <div class="kpi-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    ${{ number_format($netProfit, 2) }}
                </div>
            </div>
            <div class="kpi-icon {{ $netProfit >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                <i class="bi bi-wallet2"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <!-- Overdue Amount -->
    <div class="col-md-6 col-xl-4">
        <div class="kpi-card h-100 border-start border-3 border-danger">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="kpi-title text-danger mb-0"><i class="bi bi-exclamation-circle me-1"></i> Overdue Balance</span>
                <span class="badge bg-danger-subtle text-danger">Action Required</span>
            </div>
            <div class="kpi-value text-danger">${{ number_format($overdueTotal, 2) }}</div>
            <span class="text-muted" style="font-size: 0.73rem;">Uncollected customer bills: ${{ number_format($overdueReceivables, 2) }}</span>
        </div>
    </div>

    <!-- Annual Budget vs Expenses -->
    <div class="col-md-6 col-xl-4">
        <div class="kpi-card h-100 border-start border-3 border-primary">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="kpi-title text-primary mb-0"><i class="bi bi-pie-chart me-1"></i> Budget Utilization</span>
                <span class="badge bg-light text-secondary border">{{ date('Y') }}</span>
            </div>
            <div class="kpi-value text-dark">${{ number_format($totalExpenses, 2) }} <span class="fw-normal text-muted" style="font-size: 0.82rem;">/ ${{ number_format($totalBudget, 2) }}</span></div>
            <span class="text-muted" style="font-size: 0.73rem;">Cap utilization: {{ $totalBudget > 0 ? round(($totalExpenses / $totalBudget) * 100, 1) : 0 }}%</span>
        </div>
    </div>

    <!-- Frankfurter Live API Widget -->
    <div class="col-md-12 col-xl-4">
        <div class="kpi-card h-100 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-1.5">
                <span class="kpi-title mb-0"><i class="bi bi-currency-exchange me-1"></i> Exchange Rates</span>
                <span class="text-muted" style="font-size: 0.7rem;">{{ $exchangeData['date'] }}</span>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-2">
                <form action="{{ route('dashboard') }}" method="GET" class="d-flex align-items-center gap-1">
                    <span class="text-muted" style="font-size: 0.74rem;">Base:</span>
                    <select name="base_currency" class="form-select form-select-sm py-0 ps-2 pe-4" style="font-size: 0.75rem; width: auto;" onchange="this.form.submit()">
                        @foreach (['USD', 'EUR', 'GBP', 'INR', 'CAD', 'AUD'] as $curr)
                            <option value="{{ $curr }}" {{ $baseCurrency === $curr ? 'selected' : '' }}>{{ $curr }}</option>
                        @endforeach
                    </select>
                </form>
                <span class="text-muted" style="font-size: 0.68rem;">Frankfurter API</span>
            </div>

            <div class="row g-1 text-center font-monospace" style="font-size: 0.74rem;">
                @php
                    $keyCurrencies = ['USD', 'EUR', 'GBP', 'INR', 'JPY', 'CAD'];
                    $displayRates = array_filter($exchangeData['rates'], fn($k) => in_array($k, $keyCurrencies) && $k !== $baseCurrency, ARRAY_FILTER_USE_KEY);
                @endphp
                @foreach ($displayRates as $currCode => $rate)
                    <div class="col-4">
                        <div class="bg-white px-1.5 py-1 rounded border">
                            <span class="text-muted">{{ $currCode }}:</span> <strong>{{ number_format($rate, 2) }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-3 mb-3">
    <!-- Chart 1: Sales vs Purchases -->
    <div class="col-lg-8">
        <x-card title="Sales vs Purchases (6 Months)">
            <div style="height: 230px;">
                <canvas id="salesPurchasesChart"></canvas>
            </div>
        </x-card>
    </div>

    <!-- Chart 2: Expenses by Category -->
    <div class="col-lg-4">
        <x-card title="Expenses by Category">
            <div style="height: 230px;">
                <canvas id="expensesChart"></canvas>
            </div>
        </x-card>
    </div>
</div>

<!-- Tables Section: Recent Invoices & Overdue Alerts -->
<div class="row g-3">
    <!-- Overdue Invoices Alert List -->
    <div class="col-lg-6">
        <x-card title="Overdue Sales Invoices">
            <x-slot:actions>
                <a href="{{ route('sales-invoices.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-outline-danger">
                    View All
                </a>
            </x-slot:actions>

            @if ($overdueInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Due Date</th>
                                <th class="text-end">Balance</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overdueInvoices as $inv)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales-invoices.show', $inv) }}" class="fw-semibold text-decoration-none">
                                            {{ $inv->invoice_no }}
                                        </a>
                                    </td>
                                    <td>{{ $inv->customer?->name ?? '—' }}</td>
                                    <td><span class="text-danger fw-semibold">{{ $inv->due_date->format('M d, Y') }}</span></td>
                                    <td class="text-end fw-semibold text-danger">${{ number_format($inv->balance_due, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('sales-invoices.show', $inv) }}" class="btn btn-sm btn-outline-danger py-0">
                                            Collect
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small text-center py-3 mb-0">No overdue sales invoices. All accounts are in good standing.</p>
            @endif
        </x-card>
    </div>

    <!-- Recent Sales Invoices -->
    <div class="col-lg-6">
        <x-card title="Recent Invoices">
            <x-slot:actions>
                <a href="{{ route('sales-invoices.index') }}" class="btn btn-sm btn-outline-secondary">
                    View All
                </a>
            </x-slot:actions>

            @if ($recentSales->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th class="text-end">Grand Total</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentSales as $inv)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales-invoices.show', $inv) }}" class="fw-semibold text-decoration-none">
                                            {{ $inv->invoice_no }}
                                        </a>
                                    </td>
                                    <td>{{ $inv->customer?->name ?? '—' }}</td>
                                    <td>{{ $inv->invoice_date->format('M d, Y') }}</td>
                                    <td class="text-end fw-semibold">${{ number_format($inv->grand_total, 2) }}</td>
                                    <td class="text-center">
                                        <x-status-badge :status="$inv->status" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted small text-center py-3 mb-0">No invoices generated yet.</p>
            @endif
        </x-card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Sales vs Purchases
        const ctx1 = document.getElementById('salesPurchasesChart');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [
                        {
                            label: 'Sales ($)',
                            data: @json($monthlySalesData),
                            backgroundColor: '#3b82f6',
                            borderRadius: 3,
                        },
                        {
                            label: 'Purchases ($)',
                            data: @json($monthlyPurchasesData),
                            backgroundColor: '#94a3b8',
                            borderRadius: 3,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 10, font: { size: 11 } } },
                    },
                    scales: {
                        x: { ticks: { font: { size: 10 } } },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                font: { size: 10 },
                                callback: val => '$' + (val >= 1000 ? (val/1000).toFixed(1) + 'k' : val)
                            }
                        }
                    }
                }
            });
        }

        // Chart 2: Expenses by Category
        const ctx2 = document.getElementById('expensesChart');
        if (ctx2) {
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: @json($expenseCategories),
                    datasets: [{
                        data: @json($expenseCategoryAmounts),
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#64748b'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
