@extends('layouts.app')

@section('title', 'Sales Invoice ' . $salesInvoice->invoice_no)
@section('page_title', 'Sales Invoice Details')
@section('page_subtitle', $salesInvoice->invoice_no)

@section('content')
<div class="row g-4">
    <!-- Main Invoice Document View -->
    <div class="col-lg-8">
        <x-card>
            <x-slot:actions>
                <div class="btn-group btn-group-sm btn-print-hide">
                    <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Print / PDF
                    </button>
                    <a href="{{ route('sales-invoices.edit', $salesInvoice) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit Invoice
                    </a>
                    <a href="{{ route('sales-invoices.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> All Invoices
                    </a>
                </div>
            </x-slot:actions>

            <!-- Invoice Header -->
            <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">SALES INVOICE</h3>
                    <div class="font-monospace text-primary fs-5 fw-bold">{{ $salesInvoice->invoice_no }}</div>
                </div>
                <div class="text-end">
                    <x-status-badge :status="$salesInvoice->status" class="fs-6 px-3 py-1.5" />
                    <div class="small text-muted mt-2">Date: <strong>{{ $salesInvoice->invoice_date->format('M d, Y') }}</strong></div>
                    <div class="small text-muted">Due Date: <strong class="{{ $salesInvoice->status === 'overdue' ? 'text-danger' : '' }}">{{ $salesInvoice->due_date->format('M d, Y') }}</strong></div>
                </div>
            </div>

            <!-- Party Details -->
            <div class="row mb-4">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <h6 class="text-uppercase fw-bold text-muted small mb-2">Billed By:</h6>
                    <h5 class="fw-bold text-dark mb-1">SmartLedger Corp</h5>
                    <div class="text-secondary small">Headquarters Financial Operations</div>
                    <div class="text-secondary small">billing@smartledger.local</div>
                    <div class="text-secondary small">Tax ID: GSTCORP2026001</div>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="text-uppercase fw-bold text-muted small mb-2">Customer / Client:</h6>
                    <h5 class="fw-bold text-dark mb-1">{{ $salesInvoice->customer->name }}</h5>
                    <div class="text-secondary small">{{ $salesInvoice->customer->address ?: 'No billing address provided' }}</div>
                    <div class="text-secondary small mt-1">
                        @if ($salesInvoice->customer->email)
                            <div><i class="bi bi-envelope me-1"></i> {{ $salesInvoice->customer->email }}</div>
                        @endif
                        @if ($salesInvoice->customer->phone)
                            <div><i class="bi bi-telephone me-1"></i> {{ $salesInvoice->customer->phone }}</div>
                        @endif
                        @if ($salesInvoice->customer->gst_number)
                            <div class="mt-1"><span class="badge bg-light text-secondary border font-monospace">Tax ID: {{ $salesInvoice->customer->gst_number }}</span></div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Disc %</th>
                            <th class="text-end">Tax %</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesInvoice->items as $idx => $item)
                            <tr>
                                <td class="text-muted small">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->description }}</div>
                                    @if ($item->product)
                                        <small class="text-muted">SKU: {{ $item->product->sku ?: '—' }}</small>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->discount_percent, 1) }}%</td>
                                <td class="text-end">{{ number_format($item->tax_percent, 1) }}%</td>
                                <td class="text-end fw-bold text-dark">${{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals Breakdown -->
            <div class="row justify-content-end mb-4">
                <div class="col-md-6 col-lg-5">
                    <div class="d-flex justify-content-between py-1.5 border-bottom small">
                        <span class="text-muted">Taxable Subtotal:</span>
                        <span class="fw-medium">${{ number_format($salesInvoice->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1.5 border-bottom small text-secondary">
                        <span>Total Discount:</span>
                        <span class="text-danger fw-medium">-${{ number_format($salesInvoice->discount_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1.5 border-bottom small text-secondary">
                        <span>Tax Total:</span>
                        <span class="text-primary fw-medium">+${{ number_format($salesInvoice->tax_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="fw-bold text-dark">Grand Total:</span>
                        <span class="fw-bold text-primary fs-5">${{ number_format($salesInvoice->grand_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1.5 border-bottom small">
                        <span class="text-muted">Paid to Date:</span>
                        <span class="text-success fw-bold">${{ number_format($salesInvoice->paid_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 bg-light px-2 rounded mt-2">
                        <span class="fw-bold text-danger">Balance Due:</span>
                        <span class="fw-bold text-danger fs-5">${{ number_format($salesInvoice->balance_due, 2) }}</span>
                    </div>
                </div>
            </div>

            @if ($salesInvoice->notes)
                <div class="p-3 bg-light rounded text-secondary small">
                    <strong>Payment Instructions / Notes:</strong> {{ $salesInvoice->notes }}
                </div>
            @endif
        </x-card>
    </div>

    <!-- Payments Column -->
    <div class="col-lg-4 btn-print-hide">
        <!-- Record Payment Form -->
        <x-card title="Record Payment Received">
            @if ((float) $salesInvoice->balance_due > 0)
                <form action="{{ route('sales-invoices.payments.store', $salesInvoice) }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-semibold">Payment Amount ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" max="{{ $salesInvoice->balance_due }}" name="amount" id="amount" class="form-control" value="{{ old('amount', $salesInvoice->balance_due) }}" required>
                        </div>
                        <small class="text-muted">Balance remaining: ${{ number_format($salesInvoice->balance_due, 2) }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="method" class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select name="method" id="method" class="form-select" required>
                            <option value="Bank Transfer">Bank Transfer (NEFT/RTGS)</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="UPI">UPI</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reference" class="form-label fw-semibold">Txn Reference / Payment ID</label>
                        <input type="text" name="reference" id="reference" class="form-control" placeholder="TXN-987654" value="{{ old('reference') }}">
                    </div>

                    <div class="mb-3">
                        <label for="payment_notes" class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" id="payment_notes" rows="2" class="form-control" placeholder="Payment remarks...">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i> Record Customer Payment
                    </button>
                </form>
            @else
                <div class="text-center py-4 text-success">
                    <i class="bi bi-check-circle-fill fs-1"></i>
                    <h6 class="fw-bold mt-2">Invoice Fully Settled</h6>
                    <p class="text-muted small mb-0">Full balance has been paid by the customer.</p>
                </div>
            @endif
        </x-card>

        <!-- Payment History -->
        <x-card title="Payment Log">
            @if ($salesInvoice->payments->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach ($salesInvoice->payments as $p)
                        <div class="list-group-item px-0 py-2.5">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-success fs-6">${{ number_format($p->amount, 2) }}</span>
                                <span class="badge bg-light text-secondary border">{{ $p->method }}</span>
                            </div>
                            <div class="small text-muted d-flex justify-content-between mt-1">
                                <span>{{ $p->payment_date->format('M d, Y') }}</span>
                                <span>Ref: {{ $p->reference ?: 'None' }}</span>
                            </div>
                            @if ($p->notes)
                                <div class="small text-secondary mt-1 fst-italic">{{ $p->notes }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small mb-0 text-center py-3">No payments recorded yet.</p>
            @endif
        </x-card>
    </div>
</div>
@endsection

