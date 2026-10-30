@extends('layouts.app')

@section('title', 'Suppliers')
@section('page_title', 'Suppliers Directory')
@section('page_subtitle', 'Manage registered vendors, contractors and suppliers')

@section('header_actions')
<a href="{{ route('suppliers.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Add Supplier
</a>
@endsection

@section('content')
<div class="card mb-3">
    <div class="card-body p-3">
        <form action="{{ route('suppliers.index') }}" method="GET" class="d-flex flex-wrap gap-2">
            <div class="input-group" style="max-width: 380px;">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Search suppliers by name, email, phone..." value="{{ $search }}">
            </div>
            <button type="submit" class="btn btn-secondary px-3">Filter</button>
            @if ($search)
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Reset</a>
            @endif
        </form>
    </div>
</div>

<x-card>
    @if ($suppliers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Supplier / Vendor</th>
                        <th>Contact Info</th>
                        <th>Tax ID (GST)</th>
                        <th>Address</th>
                        <th class="text-center">Purchases</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $s)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $s->name }}</div>
                                <small class="text-muted">Added {{ $s->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div><i class="bi bi-envelope text-muted me-1 small"></i> {{ $s->email ?: '—' }}</div>
                                <div><i class="bi bi-telephone text-muted me-1 small"></i> {{ $s->phone ?: '—' }}</div>
                            </td>
                            <td>
                                @if ($s->gst_number)
                                    <span class="badge bg-light text-secondary border font-monospace">{{ $s->gst_number }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="{{ $s->address }}">
                                    {{ $s->address ?: '—' }}
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5">
                                    {{ $s->purchase_invoices_count }} Bills
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('suppliers.show', $s) }}" class="btn btn-outline-secondary" title="View details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteConfirmModal"
                                            data-action="{{ route('suppliers.destroy', $s) }}"
                                            data-name="Supplier: {{ $s->name }}"
                                            data-message="Are you sure you want to delete this supplier? All associated purchase bills will also be permanently removed.">
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
                Showing {{ $suppliers->firstItem() ?? 0 }} to {{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} suppliers
            </small>
            <div>
                {{ $suppliers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @else
        <x-empty-state 
            title="No suppliers found"
            message="No suppliers matched your search query. Add your first supplier to begin tracking purchase orders."
            :action="'<a href=&quot;'.route('suppliers.create').'&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Add First Supplier</a>'"
        />
    @endif
</x-card>
@endsection

