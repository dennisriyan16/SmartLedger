@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customers Directory')
@section('page_subtitle', 'Manage corporate clients and billing contacts')

@section('header_actions')
<a href="{{ route('customers.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Add Customer
</a>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body p-3">
        <form action="{{ route('customers.index') }}" method="GET" class="d-flex flex-wrap gap-2">
            <div class="input-group" style="max-width: 380px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, phone, GST..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn btn-secondary px-3">Filter</button>
            @if ($search)
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<x-card>
    @if ($customers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Customer / Company</th>
                        <th>Contact Info</th>
                        <th>Tax ID (GST)</th>
                        <th>Address</th>
                        <th class="text-center">Invoices</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $c)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $c->name }}</div>
                                <small class="text-muted">Added {{ $c->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div><i class="bi bi-envelope text-muted me-1 small"></i> {{ $c->email ?: '—' }}</div>
                                <div><i class="bi bi-telephone text-muted me-1 small"></i> {{ $c->phone ?: '—' }}</div>
                            </td>
                            <td>
                                @if ($c->gst_number)
                                    <span class="badge bg-light text-secondary border font-monospace">{{ $c->gst_number }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $c->address }}">
                                    {{ $c->address ?: '—' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5">
                                    {{ $c->sales_invoices_count }} Sales
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('customers.show', $c) }}" class="btn btn-outline-secondary" title="View details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $c) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteConfirmModal"
                                            data-action="{{ route('customers.destroy', $c) }}"
                                            data-name="Customer: {{ $c->name }}"
                                            data-message="Are you sure you want to delete this customer? All associated sales invoices will also be permanently removed.">
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
                Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
            </small>
            <div>
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @else
        <x-empty-state 
            title="No customers found"
            message="We couldn't find any customers matching your criteria. Try adjusting your search or add a new customer."
            :action="'<a href=&quot;'.route('customers.create').'&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Add First Customer</a>'"
        />
    @endif
</x-card>
@endsection

