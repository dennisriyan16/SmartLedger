@extends('layouts.app')

@section('title', 'Reports & Financial Analytics')
@section('page_title', 'Reports & Analytics')
@section('page_subtitle', 'Financial audits, statements and data exports')

@section('content')
<!-- Filter Bar -->
<div class="card mb-3">
    <div class="card-body py-2.5 px-3">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <div class="col-sm-4 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted">From</span>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
            </div>
            <div class="col-sm-4 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted">To</span>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
            </div>
            <div class="col-sm-4 col-md-3 d-flex gap-1.5">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i> Apply
                </button>
                <a href="{{ route('reports.export', ['type' => $activeTab, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-pills mb-3 gap-1.5" style="font-size: 0.78rem;">
    <li class="nav-item">
        <a class="nav-link py-1 px-2.5 {{ $activeTab === 'profit-loss' ? 'active' : 'bg-white border text-secondary' }}" href="{{ route('reports.index', ['tab' => 'profit-loss', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            Profit & Loss
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link py-1 px-2.5 {{ $activeTab === 'sales' ? 'active' : 'bg-white border text-secondary' }}" href="{{ route('reports.index', ['tab' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            Sales Summary
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link py-1 px-2.5 {{ $activeTab === 'purchases' ? 'active' : 'bg-white border text-secondary' }}" href="{{ route('reports.index', ['tab' => 'purchases', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            Purchase Summary
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link py-1 px-2.5 {{ $activeTab === 'tax' ? 'active' : 'bg-white border text-secondary' }}" href="{{ route('reports.index', ['tab' => 'tax', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            Tax (GST)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link py-1 px-2.5 {{ $activeTab === 'budget' ? 'active' : 'bg-white border text-secondary' }}" href="{{ route('reports.index', ['tab' => 'budget', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            Budget vs Actual
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link py-1 px-2.5 {{ $activeTab === 'receivables' ? 'active' : 'bg-white border text-secondary' }}" href="{{ route('reports.index', ['tab' => 'receivables', 'start_date' => $startDate, 'end_date' => $endDate]) }}">
            Outstanding
        </a>
    </li>
</ul>

<!-- Tab 1: Profit & Loss Statement -->
@if ($activeTab === 'profit-loss')
    <div class="row g-3">
        <div class="col-lg-8">
            <x-card title="Statement of Profit & Loss">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr class="table-light">
                                <th colspan="2" class="fw-semibold text-muted small">1. Operating Revenue</th>
                            </tr>
                            <tr>
                                <td class="ps-3">Gross Invoiced Revenue (Sales)</td>
                                <td class="text-end fw-semibold text-primary">${{ number_format($totalSales, 2) }}</td>
                            </tr>
                            <tr class="table-light">
                                <th colspan="2" class="fw-semibold text-muted small">2. Procurement & Direct Costs</th>
                            </tr>
                            <tr>
                                <td class="ps-3">Total Vendor Purchase Orders</td>
                                <td class="text-end fw-semibold text-secondary">-${{ number_format($totalPurchases, 2) }}</td>
                            </tr>
                            <tr class="table-light">
                                <th colspan="2" class="fw-semibold text-muted small">3. Operating Expenses</th>
                            </tr>
                            <tr>
                                <td class="ps-3">Departmental Expenses & Utilities</td>
                                <td class="text-end fw-semibold text-danger">-${{ number_format($totalExpenses, 2) }}</td>
                            </tr>
                            <tr class="table-light">
                                <th class="ps-3 fw-bold">Net Operating Margin (Estimated P&L)</th>
                                <th class="text-end fw-bold {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format($netProfit, 2) }}
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <div class="col-lg-4">
            <x-card title="Performance Ratios">
                <div class="mb-3">
                    <span class="text-muted small d-block">Operating Profit Margin</span>
                    <h5 class="fw-semibold mb-0 {{ $totalSales > 0 ? ($netProfit >= 0 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                        {{ $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 1) : 0 }}%
                    </h5>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Total Outflows (Purchases + Expenses)</span>
                    <h5 class="fw-semibold text-danger mb-0">${{ number_format($totalPurchases + $totalExpenses, 2) }}</h5>
                </div>
                <div class="p-2.5 bg-light rounded text-muted small border">
                    <i class="bi bi-shield-check text-primary me-1"></i> Exact monetary arithmetic using BCMath.
                </div>
            </x-card>
        </div>
    </div>

<!-- Tab 2: Sales Summary -->
@elseif ($activeTab === 'sales')
    <x-card title="Sales Summary">
        <div class="row g-3 text-center">
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Invoices Count</span>
                    <h5 class="fw-semibold mb-0 text-dark">{{ $salesSummary->total_count ?? 0 }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Taxable Subtotal</span>
                    <h5 class="fw-semibold mb-0 text-dark">${{ number_format((float)($salesSummary->subtotal ?? 0), 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Sales Tax Collected</span>
                    <h5 class="fw-semibold mb-0 text-primary">${{ number_format((float)($salesSummary->tax ?? 0), 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Grand Total Billed</span>
                    <h5 class="fw-semibold mb-0 text-success">${{ number_format((float)($salesSummary->grand_total ?? 0), 2) }}</h5>
                </div>
            </div>
        </div>
    </x-card>

<!-- Tab 3: Purchase Summary -->
@elseif ($activeTab === 'purchases')
    <x-card title="Purchase Summary">
        <div class="row g-3 text-center">
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Bills Count</span>
                    <h5 class="fw-semibold mb-0 text-dark">{{ $purchaseSummary->total_count ?? 0 }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Taxable Subtotal</span>
                    <h5 class="fw-semibold mb-0 text-dark">${{ number_format((float)($purchaseSummary->subtotal ?? 0), 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Input Tax Paid</span>
                    <h5 class="fw-semibold mb-0 text-secondary">${{ number_format((float)($purchaseSummary->tax ?? 0), 2) }}</h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-2.5 bg-light rounded border">
                    <span class="text-muted small d-block">Total Purchases</span>
                    <h5 class="fw-semibold mb-0 text-dark">${{ number_format((float)($purchaseSummary->grand_total ?? 0), 2) }}</h5>
                </div>
            </div>
        </div>
    </x-card>

<!-- Tab 4: Tax Audit -->
@elseif ($activeTab === 'tax')
    <x-card title="Tax Audit (GST)">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tax Ledger</th>
                        <th>Classification</th>
                        <th class="text-end">Amount ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-medium">Output Tax Collected (Sales)</td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">Liability</span></td>
                        <td class="text-end fw-semibold text-primary">${{ number_format($taxCollected, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Input Tax Credit Paid (Purchases)</td>
                        <td><span class="badge bg-success-subtle text-success border border-success-subtle">Credit</span></td>
                        <td class="text-end fw-semibold text-success">${{ number_format($taxPaid, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <th colspan="2">Net Tax Liability / (Credit)</th>
                        <th class="text-end fw-bold {{ $netTaxPayable >= 0 ? 'text-danger' : 'text-success' }}">
                            ${{ number_format($netTaxPayable, 2) }}
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-card>

<!-- Tab 5: Budget vs Actual -->
@elseif ($activeTab === 'budget')
    <x-card title="Budget Allocation vs Actual Spend">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Budget Name</th>
                        <th>Category</th>
                        <th>Period</th>
                        <th class="text-end">Allocated</th>
                        <th class="text-end">Actual</th>
                        <th class="text-end">Variance</th>
                        <th class="text-center">Usage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($budgetVsActual as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item['name'] }}</td>
                            <td><span class="badge bg-light text-secondary border">{{ $item['category'] }}</span></td>
                            <td><span class="badge bg-light text-secondary border font-monospace">{{ $item['type'] }}</span></td>
                            <td class="text-end">${{ number_format($item['allocated'], 2) }}</td>
                            <td class="text-end text-danger">${{ number_format($item['spent'], 2) }}</td>
                            <td class="text-end fw-semibold {{ $item['variance'] < 0 ? 'text-danger' : 'text-success' }}">
                                ${{ number_format($item['variance'], 2) }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $item['pct'] > 100 ? 'bg-danger-subtle text-danger border border-danger-subtle' : ($item['pct'] >= 80 ? 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' : 'bg-success-subtle text-success border border-success-subtle') }}">
                                    {{ $item['pct'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

<!-- Tab 6: Outstanding Balances -->
@elseif ($activeTab === 'receivables')
    <div class="row g-3">
        <div class="col-lg-6">
            <x-card title="Unpaid Receivables">
                @if ($receivables->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Customer</th>
                                    <th>Due Date</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($receivables as $r)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales-invoices.show', $r) }}" class="fw-medium text-decoration-none">
                                                {{ $r->invoice_no }}
                                            </a>
                                        </td>
                                        <td>{{ $r->customer?->name ?? '—' }}</td>
                                        <td><span class="{{ $r->status === 'overdue' ? 'text-danger fw-semibold' : '' }}">{{ $r->due_date->format('M d, Y') }}</span></td>
                                        <td class="text-end fw-semibold text-danger">${{ number_format($r->balance, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted small text-center py-2 mb-0">No pending receivables.</p>
                @endif
            </x-card>
        </div>

        <div class="col-lg-6">
            <x-card title="Unpaid Payables">
                @if ($payables->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Bill #</th>
                                    <th>Supplier</th>
                                    <th>Due Date</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payables as $p)
                                    <tr>
                                        <td>
                                            <a href="{{ route('purchase-invoices.show', $p) }}" class="fw-medium text-decoration-none">
                                                {{ $p->invoice_no }}
                                            </a>
                                        </td>
                                        <td>{{ $p->supplier?->name ?? '—' }}</td>
                                        <td><span class="{{ $p->status === 'overdue' ? 'text-danger fw-semibold' : '' }}">{{ $p->due_date->format('M d, Y') }}</span></td>
                                        <td class="text-end fw-semibold text-secondary">${{ number_format($p->balance, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted small text-center py-2 mb-0">No pending payables.</p>
                @endif
            </x-card>
        </div>
    </div>
@endif
@endsection
