@extends('layouts.app')

@section('title', 'Products & Services')
@section('page_title', 'Products & Services')
@section('page_subtitle', 'Manage inventory catalogue, billable consulting and tax rates')

@section('header_actions')
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Product / Service
    </a>
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-body p-3">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                <div class="input-group" style="max-width: 340px;">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0"
                        placeholder="Search by name, SKU, details..." value="{{ $search }}">
                </div>
                <select name="type" class="form-select" style="max-width: 170px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="product" {{ $type === 'product' ? 'selected' : '' }}>Physical Goods</option>
                    <option value="service" {{ $type === 'service' ? 'selected' : '' }}>Services</option>
                </select>
                <button type="submit" class="btn btn-secondary px-3">Filter</button>
                @if ($search || $type)
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
    </div>

    <x-card>
        @if ($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Item Details</th>
                            <th>Type</th>
                            <th>SKU</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-center">Tax Rate</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $p)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->name }}</div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 350px;">
                                        {{ $p->description ?: 'No description provided' }}
                                    </small>
                                </td>
                                <td>
                                    <x-status-badge :status="$p->type" />
                                </td>
                                <td>
                                    @if ($p->sku)
                                        <span class="badge bg-light text-secondary border font-monospace">{{ $p->sku }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    ${{ number_format($p->unit_price, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        {{ number_format($p->tax_rate, 1) }}%
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('products.show', $p) }}" class="btn btn-outline-secondary"
                                            title="View details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $p) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmModal" data-action="{{ route('products.destroy', $p) }}"
                                            data-name="Item: {{ $p->name }}"
                                            data-message="Are you sure you want to delete this catalogue item? Historical invoice lines will preserve their details.">
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
                    Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }}
                    items
                </small>
                <div>
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <x-empty-state title="No items found"
                message="No products or services matched your filter parameters. Add your standard offerings here." :action="'<a href=&quot;' . route('products.create') . '&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Add Product / Service</a>'" />
        @endif
    </x-card>
@endsection