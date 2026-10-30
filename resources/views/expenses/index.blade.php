@extends('layouts.app')

@section('title', 'Expenses')
@section('page_title', 'Expenses Tracker')
@section('page_subtitle', 'Record operational expenses and track budget allocations')

@section('header_actions')
<a href="{{ route('expenses.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Log Expense
</a>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body p-3">
        <form action="{{ route('expenses.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-sm-6 col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search expense title..." value="{{ $search }}">
            </div>
            <div class="col-sm-6 col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <input type="date" name="start_date" class="form-control" title="From date" value="{{ $startDate }}">
            </div>
            <div class="col-sm-6 col-md-2">
                <input type="date" name="end_date" class="form-control" title="To date" value="{{ $endDate }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
                @if ($search || $category || $startDate || $endDate)
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<x-card>
    @if ($expenses->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Title / Description</th>
                        <th>Category</th>
                        <th>Linked Budget</th>
                        <th>Date</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $e)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $e->title }}</div>
                                @if ($e->notes)
                                    <small class="text-muted">{{ $e->notes }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $e->category }}</span>
                            </td>
                            <td>
                                @if ($e->budget)
                                    <a href="{{ route('budgets.show', $e->budget) }}" class="text-decoration-none small fw-semibold">
                                        {{ $e->budget->name }}
                                    </a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>{{ $e->expense_date->format('M d, Y') }}</td>
                            <td class="text-end fw-bold text-danger fs-6">
                                -${{ number_format($e->amount, 2) }}
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
                                            data-message="Are you sure you want to delete this expense record?">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <small class="text-muted">
                Showing {{ $expenses->firstItem() ?? 0 }} to {{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} expense records
            </small>
            <div>
                {{ $expenses->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @else
        <x-empty-state 
            title="No expenses logged"
            message="No expenses matched your search criteria. Record your operational expenses to keep track of spending."
            :action="'<a href=&quot;'.route('expenses.create').'&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Log First Expense</a>'"
        />
    @endif
</x-card>
@endsection

