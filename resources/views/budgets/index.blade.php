@extends('layouts.app')

@section('title', 'Budgets')
@section('page_title', 'Budgets Management')
@section('page_subtitle', 'Plan and monitor monthly & annual departmental operational budgets')

@section('header_actions')
<a href="{{ route('budgets.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> New Budget
</a>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body p-3">
        <form action="{{ route('budgets.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-sm-4 col-md-3">
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">All Types (Monthly / Yearly)</option>
                    <option value="monthly" {{ $type === 'monthly' ? 'selected' : '' }}>Monthly Only</option>
                    <option value="yearly" {{ $type === 'yearly' ? 'selected' : '' }}>Yearly Only</option>
                </select>
            </div>
            <div class="col-sm-4 col-md-3">
                <select name="category" class="form-select" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                @if ($type || $category)
                    <a href="{{ route('budgets.index', ['year' => $year]) }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if ($budgets->count() > 0)
    <div class="row g-4">
        @foreach ($budgets as $b)
            @php
                $m = $b->metrics;
                $pct = min(100, $m['percentage']);
                $barColor = match ($m['status']) {
                    'On track' => 'bg-success',
                    'Near limit' => 'bg-warning',
                    'Exceeded' => 'bg-danger',
                    default => 'bg-primary',
                };
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-light text-secondary border font-monospace text-uppercase" style="font-size: 0.7rem;">
                                {{ $b->type }} {{ $b->type === 'monthly' ? '('.date('M', mktime(0, 0, 0, $b->month, 1)).' '.$b->year.')' : '('.$b->year.')' }}
                            </span>
                        </div>
                        <x-status-badge :status="$m['status']" />
                    </div>

                    <div class="card-body">
                        <h5 class="card-title fw-bold text-dark mb-1">{{ $b->name }}</h5>
                        <p class="text-muted small mb-3"><i class="bi bi-tag me-1"></i> {{ $b->category }}</p>

                        <!-- Progress Bar -->
                        <div class="mb-2 d-flex justify-content-between small">
                            <span class="text-muted">Spent: <strong>${{ number_format($m['spent'], 2) }}</strong></span>
                            <span class="fw-bold">{{ $m['percentage'] }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Amounts Breakdown -->
                        <div class="row g-2 mt-3 pt-3 border-top text-center">
                            <div class="col-6 text-start">
                                <small class="text-muted d-block">Allocated</small>
                                <span class="fw-bold text-dark">${{ number_format($m['allocated'], 2) }}</span>
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block">Remaining</small>
                                <span class="fw-bold {{ bccomp($m['remaining'], '0', 2) < 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($m['remaining'], 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light py-2 border-top d-flex justify-content-between align-items-center">
                        <a href="{{ route('budgets.show', $b) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye me-1"></i> View Details
                        </a>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('budgets.edit', $b) }}" class="btn btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteConfirmModal"
                                    data-action="{{ route('budgets.destroy', $b) }}"
                                    data-name="Budget: {{ $b->name }}"
                                    data-message="Are you sure you want to delete this budget? All associated expense logs will also be permanently deleted.">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <small class="text-muted">
            Showing {{ $budgets->firstItem() ?? 0 }} to {{ $budgets->lastItem() ?? 0 }} of {{ $budgets->total() }} budgets
        </small>
        <div>
            {{ $budgets->links('pagination::bootstrap-5') }}
        </div>
    </div>
@else
    <x-card>
        <x-empty-state 
            title="No budgets found"
            message="No budget plans found for the selected period. Create a new budget to track departmental expense caps."
            :action="'<a href=&quot;'.route('budgets.create').'&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Add New Budget</a>'"
        />
    </x-card>
@endif
@endsection

