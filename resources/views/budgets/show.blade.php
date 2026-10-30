@extends('layouts.app')

@section('title', $budget->name)
@section('page_title', $budget->name)
@section('page_subtitle', 'Budget analysis and linked expense transactions')

@section('content')
@php
    $pct = min(100, $metrics['percentage']);
    $barColor = match ($metrics['status']) {
        'On track' => 'bg-success',
        'Near limit' => 'bg-warning',
        'Exceeded' => 'bg-danger',
        default => 'bg-primary',
    };
@endphp

<div class="row g-4">
    <!-- Analytics Header Card -->
    <div class="col-12">
        <x-card>
            <x-slot:actions>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('expenses.create', ['budget_id' => $budget->id]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Expense
                    </a>
                    <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Budget
                    </a>
                    <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> All Budgets
                    </a>
                </div>
            </x-slot:actions>

            <div class="row g-4 align-items-center">
                <div class="col-md-4 border-end">
                    <span class="badge bg-light text-secondary border font-monospace mb-2">
                        {{ strtoupper($budget->type) }} {{ $budget->type === 'monthly' ? '('.date('F', mktime(0, 0, 0, $budget->month, 1)).' '.$budget->year.')' : '('.$budget->year.')' }}
                    </span>
                    <h3 class="fw-bold text-dark mb-1">{{ $budget->name }}</h3>
                    <p class="text-muted small mb-0"><i class="bi bi-tag me-1"></i> Category: <strong>{{ $budget->category }}</strong></p>
                </div>

                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold text-secondary">Utilization Progress</span>
                        <div>
                            <x-status-badge :status="$metrics['status']" />
                            <strong class="ms-2 fs-5 text-dark">{{ $metrics['percentage'] }}%</strong>
                        </div>
                    </div>

                    <div class="progress mb-3" style="height: 12px;">
                        <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $pct }}%;"></div>
                    </div>

                    <div class="row g-3 text-center">
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Allocated Budget</small>
                                <span class="fw-bold fs-6 text-dark">${{ number_format($metrics['allocated'], 2) }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Actual Expenses</small>
                                <span class="fw-bold fs-6 text-danger">${{ number_format($metrics['spent'], 2) }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Remaining Balance</small>
                                <span class="fw-bold fs-6 {{ bccomp($metrics['remaining'], '0', 2) < 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($metrics['remaining'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Linked Expenses Table -->
    <div class="col-12">
        <x-card title="Recorded Expenses Against this Budget">
            @if ($expenses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Expense Title</th>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                                <th>Notes</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenses as $e)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $e->title }}</div>
                                    </td>
                                    <td>{{ $e->expense_date->format('M d, Y') }}</td>
                                    <td class="text-end fw-bold text-danger">
                                        ${{ number_format($e->amount, 2) }}
                                    </td>
                                    <td>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 300px;">
                                            {{ $e->notes ?: '—' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('expenses.edit', $e) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteConfirmModal"
                                                    data-action="{{ route('expenses.destroy', $e) }}"
                                                    data-name="Expense: {{ $e->title }}"
                                                    data-message="Are you sure you want to delete this expense entry?">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 pt-3 border-top">
                    {{ $expenses->links('pagination::bootstrap-5') }}
                </div>
            @else
                <x-empty-state 
                    title="No expenses logged yet"
                    message="No expenditures have been deducted from this budget yet."
                    :action="'<a href=&quot;'.route('expenses.create', ['budget_id' => $budget->id]).'&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Add First Expense</a>'"
                />
            @endif
        </x-card>
    </div>
</div>
@endsection

