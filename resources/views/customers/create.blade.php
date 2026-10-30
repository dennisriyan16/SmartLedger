@extends('layouts.app')

@section('title', 'Add Customer')
@section('page_title', 'New Customer')
@section('page_subtitle', 'Register a new corporate or individual client')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Customer Information">
            <x-slot:actions>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </x-slot:actions>

            <form action="{{ route('customers.store') }}" method="POST" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-semibold">Customer / Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Acme Corporation" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="billing@acme.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+1-555-0123">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="gst_number" class="form-label fw-semibold">Tax ID / GST Number</label>
                        <input type="text" name="gst_number" id="gst_number" class="form-control @error('gst_number') is-invalid @enderror" value="{{ old('gst_number') }}" placeholder="27AABCU9603R1ZM">
                        @error('gst_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="address" class="form-label fw-semibold">Full Billing Address</label>
                        <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Street, Suite/Apartment, City, State, ZIP">{{ old('address') }}</textarea>
                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save Customer
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

