@extends('layouts.app')

@section('title', $customer->name)
@section('page_title', $customer->name)
@section('page_subtitle', 'Customer overview and sales invoice history')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <x-card title="Customer Profile">
            <div class="mb-3">
                <span class="text-muted small d-block">Company / Client</span>
                <span class="fw-bold fs-5 text-dark">{{ $customer->name }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Email</span>
                <span class="fw-medium">{{ $customer->email ?: '—' }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Phone</span>
                <span class="fw-medium">{{ $customer->phone ?: '—' }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Tax ID / GST</span>
                <span class="badge bg-light text-secondary border font-monospace">{{ $customer->gst_number ?: 'Not provided' }}</span>
            </div>
            <div class="mb-3">
                <span class="text-muted small d-block">Billing Address</span>
                <div class="text-secondary small">{{ $customer->address ?: 'No address specified' }}</div>
            </div>

            <div class="pt-3 border-top d-flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm flex-fill">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('sales-invoices.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-receipt me-1"></i> New Invoice
                </a>
            </div>
        </x-card>
    </div>

    <div class="col-lg-8">
        <x-card title="Sales Invoices History">
            @if ($customer->salesInvoices->count() > 0)
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
                            @foreach ($customer->salesInvoices as $inv)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales-invoices.show', $inv) }}" class="fw-bold text-decoration-none">
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
                                        <a href="{{ route('sales-invoices.show', $inv) }}" class="btn btn-sm btn-outline-secondary">
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
                    title="No invoices yet"
                    message="There are no sales invoices created for this customer yet."
                    :action="'<a href=&quot;'.route('sales-invoices.create', ['customer_id' => $customer->id]).'&quot; class=&quot;btn btn-primary btn-sm&quot;><i class=&quot;bi bi-plus-lg me-1&quot;></i> Create Sales Invoice</a>'"
                />
            @endif
        </x-card>
    </div>
</div>
@endsection

