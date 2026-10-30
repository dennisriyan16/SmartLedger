@extends('layouts.app')

@section('title', 'Add Supplier')
@section('page_title', 'New Supplier')
@section('page_subtitle', 'Register a new supplier or vendor')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Supplier Information">
            <x-slot:actions>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </x-slot:actions>

            <form action="{{ route('suppliers.store') }}" method="POST" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-semibold">Supplier / Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Dell Global Supplies" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="orders@dell-partner.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+1-800-456-3355">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="gst_number" class="form-label fw-semibold">Tax ID / GST Number</label>
                        <input type="text" name="gst_number" id="gst_number" class="form-control @error('gst_number') is-invalid @enderror" value="{{ old('gst_number') }}" placeholder="07AAACM3344K1ZV">
                        @error('gst_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="address" class="form-label fw-semibold">Office / Dispatch Address</label>
                        <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Vendor warehouse address, city, country">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('suppliers.index') }}" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save Supplier
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

