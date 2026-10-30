@extends('layouts.app')

@section('title', $product->name)
@section('page_title', $product->name)
@section('page_subtitle', 'Product specification and pricing breakdown')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Item Details">
            <x-slot:actions>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            </x-slot:actions>

            <div class="row g-4">
                <div class="col-md-6">
                    <span class="text-muted small d-block">Item Name</span>
                    <span class="fw-bold fs-5 text-dark">{{ $product->name }}</span>
                </div>
                <div class="col-md-6">
                    <span class="text-muted small d-block">Item Type</span>
                    <div class="mt-1"><x-status-badge :status="$product->type" /></div>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">SKU / Code</span>
                    <span class="badge bg-light text-secondary border font-monospace fs-6">{{ $product->sku ?: '—' }}</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Standard Price</span>
                    <span class="fw-bold fs-5 text-primary">${{ number_format($product->unit_price, 2) }}</span>
                </div>
                <div class="col-md-4">
                    <span class="text-muted small d-block">Tax Rate</span>
                    <span class="fw-bold fs-5 text-dark">{{ number_format($product->tax_rate, 2) }}%</span>
                </div>
                <div class="col-12">
                    <span class="text-muted small d-block">Description</span>
                    <div class="p-3 bg-light rounded mt-1 text-secondary">
                        {{ $product->description ?: 'No detailed description provided.' }}
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection

