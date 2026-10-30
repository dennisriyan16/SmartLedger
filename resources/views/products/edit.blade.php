@extends('layouts.app')

@section('title', 'Edit Item')
@section('page_title', 'Edit Product or Service')
@section('page_subtitle', 'Update pricing, tax category and item specification')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Edit Item Details">
            <x-slot:actions>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </x-slot:actions>

            <form action="{{ route('products.update', $product) }}" method="POST" novalidate>
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="type" class="form-label fw-semibold">Item Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="product" {{ old('type', $product->type) === 'product' ? 'selected' : '' }}>Physical Product</option>
                            <option value="service" {{ old('type', $product->type) === 'service' ? 'selected' : '' }}>Service / Hourly</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="sku" class="form-label fw-semibold">SKU / Code</label>
                        <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}">
                        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="unit_price" class="form-label fw-semibold">Standard Unit Price ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $product->unit_price) }}" required>
                        @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="tax_rate" class="form-label fw-semibold">Default Tax Rate (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" max="100" name="tax_rate" id="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', $product->tax_rate) }}" required>
                        @error('tax_rate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="description" class="form-label fw-semibold">Description / Specifications</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Update Item
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

