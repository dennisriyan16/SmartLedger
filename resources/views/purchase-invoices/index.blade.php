@extends('layouts.app')

@section('title', 'Purchase Invoices')
@section('page_title', 'Purchase Invoices (Bills)')
@section('page_subtitle', 'Manage supplier purchase bills, payment status and procurement')

@section('header_actions')
    <a href="{{ route('purchase-invoices.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Purchase Bill
    </a>
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-body p-3">
            <form action="{{ route('purchase-invoices.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-sm-6 col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search invoice #, vendor..."
                        value="{{ $search }}">
                </div>
                <div class="col-sm-6 col-md-3">
                    <select name="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}" {{ (string) $supplierId === (string) $s->id ? 'selected' : '' }}>
                                {{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ $status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-sm-6 col-md-2">
                    <input type="date" name="start_date" class="form-control" title="From date" value="{{ $startDate }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary"><i class="bi bi-funnel"></i> Filter</button>
                    @if ($search || $supplierId || $status || $startDate)
                        <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-card>
        @if ($invoices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $inv)
                            <tr>
                                <td>
                                    <a href="{{ route('purchase-invoices.show', $inv) }}" class="fw-bold text-decoration-none">
                                        {{ $inv->invoice_no }}
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $inv->supplier?->name ?? 'Unknown Supplier' }}</div>
                                </td>
                                <td>{{ $inv->invoice_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="{{ $inv->status === 'overdue' ? 'text-danger fw-bold' : '' }}">
                                        {{ $inv->due_date->format('M d, Y') }}
                                    </span>
                                </td>
                                <td class="text-end text-muted">${{ number_format($inv->subtotal, 2) }}</td>
                                <td class="text-end fw-bold text-dark">${{ number_format($inv->grand_total, 2) }}</td>
                                <td class="text-end text-success fw-medium">${{ number_format($inv->paid_amount, 2) }}</td>
                                <td class="text-center">
                                    <x-status-badge :status="$inv->status" />
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('purchase-invoices.show', $inv) }}" class="btn btn-outline-secondary"
                                            title="View details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('purchase-invoices.edit', $inv) }}" class="btn btn-outline-primary"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmModal"
                                            data-action="{{ route('purchase-invoices.destroy', $inv) }}"
                                            data-name="Bill: {{ $inv->invoice_no }}"
                                            data-message="Are you sure you want to delete this purchase bill?">
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
                    Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }}
                    purchase bills
                </small>
                <div>
                    {{ $invoices->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <x-empty-state title="No purchase invoices found"
                message="No bills matched your criteria. You can create a new supplier purchase bill below." :action="'<a href=&quot;' . route('purchase-invoices.create') . '&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Create Purchase Bill</a>'" />
        @endif
    </x-card>
@endsection