@extends('layouts.app')

@section('title', $supplier->name)
@section('page_title', $supplier->name)
@section('page_subtitle', 'Supplier overview and purchase invoice history')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <x-card title="Supplier Profile">
            <div class="mb-3">
                <span class="text-muted small d-block">Company / Vendor</span>
                <span class="fw-bold fs-5 text-dark">{{ $supplier->name }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Email</span>
                <span class="fw-medium">{{ $supplier->email ?: '—' }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Phone</span>
                <span class="fw-medium">{{ $supplier->phone ?: '—' }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Tax ID / GST</span>
                <span class="badge bg-light text-secondary border font-monospace">{{ $supplier->gst_number ?: 'Not provided' }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Office Address</span>
                <div class="text-secondary small">{{ $supplier->address ?: 'No address specified' }}</div>
            </div>

            <div class="pt-3 border-top d-flex gap-2">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-primary btn-sm flex-fill">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('purchase-invoices.create', ['supplier_id' => $supplier->id]) }}" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-bag-check me-1"></i> New Purchase Bill
                </a>
            </div>
        </x-card>
    </div>

    <div class="col-lg-8">
        <x-card title="Purchase Invoices History">
            @if ($supplier->purchaseInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Due Date</th>
                                <th class="text-end">Grand Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplier->purchaseInvoices as $inv)
                                <tr>
                                    <td>
                                        <a href="{{ route('purchase-invoices.show', $inv) }}" class="fw-bold text-decoration-none">
                                            {{ $inv->invoice_no }}
                                        </a>
                                    </td>
                                    <td>{{ $inv->invoice_date->format('M d, Y') }}</td>
                                    <td>{{ $inv->due_date->format('M d, Y') }}</td>
                                    <td class="text-end fw-semibold">${{ number_format($inv->grand_total, 2) }}</td>
                                    <td class="text-end text-success">${{ number_format($inv->paid_amount, 2) }}</td>
                                    <td class="text-center">
                                        <x-status-badge :status="$inv->status" />
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('purchase-invoices.show', $inv) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <x-empty-state 
                    title="No purchase invoices yet"
                    message="There are no purchase bills recorded for this supplier yet."
                    :action="'<a href=&quot;'.route('purchase-invoices.create', ['supplier_id' => $supplier->id]).'&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Create Purchase Bill</a>'"
                />
            @endif
        </x-card>
    </div>
</div>
@endsection

