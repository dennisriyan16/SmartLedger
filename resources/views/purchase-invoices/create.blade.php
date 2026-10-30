@extends('layouts.app')

@section('title', 'New Purchase Bill')
@section('page_title', 'Create Purchase Bill')
@section('page_subtitle', 'Record a new vendor invoice and enter billable line items')

@section('content')
<form action="{{ route('purchase-invoices.store') }}" method="POST" id="invoiceForm" novalidate>
    @csrf

    <div class="row g-4">
        <!-- Main Form Information -->
        <div class="col-12">
            <x-card title="Bill Header Information">
                <x-slot:actions>
                    <a href="{{ route('purchase-invoices.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Bills
                    </a>
                </x-slot:actions>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Invoice Number</label>
                        <input type="text" class="form-control bg-light font-monospace fw-bold" value="{{ $suggestedNo }} (Auto-assigned)" readonly>
                    </div>

                    <div class="col-md-5">
                        <label for="supplier_id" class="form-label fw-semibold">Supplier / Vendor <span class="text-danger">*</span></label>
                        <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                            <option value="">-- Choose Supplier --</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('supplier_id', $selectedSupplierId) == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} {{ $s->gst_number ? '('.$s->gst_number.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="invoice_date" class="form-label fw-semibold">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" name="invoice_date" id="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror" value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                        @error('invoice_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="due_date" class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" id="due_date" class="form-control @error('due_date') is-invalid @enderror" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required>
                        @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Line Items Section -->
        <div class="col-12">
            <x-card title="Line Items">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Item / Service</th>
                                <th style="width: 25%;">Description</th>
                                <th style="width: 10%;" class="text-end">Qty</th>
                                <th style="width: 13%;" class="text-end">Unit Price ($)</th>
                                <th style="width: 9%;" class="text-end">Disc %</th>
                                <th style="width: 9%;" class="text-end">Tax %</th>
                                <th style="width: 12%;" class="text-end">Line Total ($)</th>
                                <th style="width: 4%;" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Dynamic rows appended via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRowBtn">
                        <i class="bi bi-plus-circle me-1"></i> Add Line Item
                    </button>
                    <span class="text-muted small">Live calculated totals shown on the summary card below.</span>
                </div>
            </x-card>
        </div>

        <!-- Summary & Notes -->
        <div class="col-lg-6">
            <x-card title="Notes & Terms">
                <div class="mb-3">
                    <label for="notes" class="form-label fw-semibold">Notes / Purchase Order References</label>
                    <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Optional internal notes, delivery instructions or terms...">{{ old('notes') }}</textarea>
                </div>
            </x-card>
        </div>

        <div class="col-lg-6">
            <x-card title="Invoice Summary">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted">Subtotal (Taxable):</span>
                    <span class="fw-bold fs-6" id="previewSubtotal">$0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom text-secondary">
                    <span>Total Discount:</span>
                    <span class="fw-medium text-danger" id="previewDiscount">-$0.00</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom text-secondary">
                    <span>Tax Amount:</span>
                    <span class="fw-medium text-primary" id="previewTax">+$0.00</span>
                </div>
                <div class="d-flex justify-content-between py-3 fs-5">
                    <span class="fw-bold text-dark">Grand Total:</span>
                    <span class="fw-bold text-primary" id="previewGrandTotal">$0.00</span>
                </div>

                <div class="mt-3 pt-3 border-top d-flex gap-2 justify-content-end">
                    <a href="{{ route('purchase-invoices.index') }}" class="btn btn-secondary px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Save Purchase Bill
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</form>

@php
    $productsJson = $products->map(fn($p) => [
        'id' => $p->id,
        'name' => $p->name,
        'price' => (float) $p->unit_price,
        'tax' => (float) $p->tax_rate,
        'description' => $p->description ?? '',
    ]);
@endphp

@push('scripts')
<script>
    const productsData = @json($productsJson);
    const existingItems = @json(old('items', [
        ['product_id' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'discount_percent' => 0, 'tax_percent' => 0]
    ]));

    let rowIndex = 0;

    function buildProductOptions(selectedId) {
        let options = '<option value="">-- Custom Item --</option>';
        productsData.forEach(p => {
            const sel = String(p.id) === String(selectedId) ? 'selected' : '';
            options += `<option value="${p.id}" ${sel}>${p.name} ($${p.price.toFixed(2)})</option>`;
        });
        return options;
    }

    function createRow(item = {}) {
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.dataset.index = rowIndex;

        const prodId = item.product_id || '';
        const desc = item.description || '';
        const qty = item.quantity !== undefined ? item.quantity : 1;
        const price = item.unit_price !== undefined ? item.unit_price : 0;
        const disc = item.discount_percent !== undefined ? item.discount_percent : 0;
        const tax = item.tax_percent !== undefined ? item.tax_percent : 0;

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm product-select">
                    ${buildProductOptions(prodId)}
                </select>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][description]" class="form-control form-control-sm item-desc" value="${desc}" placeholder="Description" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-end item-qty" value="${qty}" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="form-control form-control-sm text-end item-price" value="${price}" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" max="100" name="items[${rowIndex}][discount_percent]" class="form-control form-control-sm text-end item-disc" value="${disc}">
            </td>
            <td>
                <input type="number" step="0.01" min="0" max="100" name="items[${rowIndex}][tax_percent]" class="form-control form-control-sm text-end item-tax" value="${tax}">
            </td>
            <td>
                <input type="text" class="form-control form-control-sm text-end bg-light fw-bold item-linetotal" value="0.00" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row p-1" title="Remove row">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        `;

        document.getElementById('itemsBody').appendChild(tr);

        // Bind events
        const prodSelect = tr.querySelector('.product-select');
        prodSelect.addEventListener('change', function () {
            const p = productsData.find(x => String(x.id) === String(this.value));
            if (p) {
                tr.querySelector('.item-desc').value = p.description || p.name;
                tr.querySelector('.item-price').value = p.price.toFixed(2);
                tr.querySelector('.item-tax').value = p.tax.toFixed(2);
            }
            recalculateTotals();
        });

        tr.querySelectorAll('.item-qty, .item-price, .item-disc, .item-tax').forEach(input => {
            input.addEventListener('input', recalculateTotals);
        });

        tr.querySelector('.btn-remove-row').addEventListener('click', function () {
            if (document.querySelectorAll('.item-row').length > 1) {
                tr.remove();
                recalculateTotals();
            } else {
                alert('At least one line item is required.');
            }
        });

        rowIndex++;
    }

    function recalculateTotals() {
        let subtotal = 0;
        let discountTotal = 0;
        let taxTotal = 0;
        let grandTotal = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const discPercent = parseFloat(row.querySelector('.item-disc').value) || 0;
            const taxPercent = parseFloat(row.querySelector('.item-tax').value) || 0;

            const gross = qty * price;
            const discAmt = gross * (discPercent / 100);
            const taxable = Math.max(0, gross - discAmt);
            const taxAmt = taxable * (taxPercent / 100);
            const lineTotal = taxable + taxAmt;

            row.querySelector('.item-linetotal').value = lineTotal.toFixed(2);

            subtotal += taxable;
            discountTotal += discAmt;
            taxTotal += taxAmt;
            grandTotal += lineTotal;
        });

        document.getElementById('previewSubtotal').innerText = '$' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('previewDiscount').innerText = '-$' + discountTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('previewTax').innerText = '+$' + taxTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('previewGrandTotal').innerText = '$' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    document.getElementById('addRowBtn').addEventListener('click', function () {
        createRow();
        recalculateTotals();
    });

    // Initialize existing
    existingItems.forEach(item => createRow(item));
    recalculateTotals();
</script>
@endpush
@endsection

