@extends('layouts.app')

@section('title', 'New Budget')
@section('page_title', 'Create Budget')
@section('page_subtitle', 'Allocate financial limits by month, year and category')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Budget Allocation Details">
            <x-slot:actions>
                <a href="{{ route('budgets.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Budgets
                </a>
            </x-slot:actions>

            <form action="{{ route('budgets.store') }}" method="POST" novalidate>
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="name" class="form-label fw-semibold">Budget Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Q4 Marketing Budget" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="type" class="form-label fw-semibold">Budget Period <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required onchange="toggleMonthSelect(this.value)">
                            <option value="monthly" {{ old('type', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('type') === 'yearly' ? 'selected' : '' }}>Yearly (Annual)</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="year" class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                        <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" required>
                        @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4" id="monthContainer">
                        <label for="month" class="form-label fw-semibold">Month</label>
                        <select name="month" id="month" class="form-select @error('month') is-invalid @enderror">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                        @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label fw-semibold">Expense Category <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="amount" class="form-label fw-semibold">Allocated Cap Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="10000.00" required>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('budgets.index') }}" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save Budget
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>

@push('scripts')
<script>
    function toggleMonthSelect(val) {
        const container = document.getElementById('monthContainer');
        if (val === 'yearly') {
            container.style.display = 'none';
        } else {
            container.style.display = 'block';
        }
    }
    toggleMonthSelect(document.getElementById('type').value);
</script>
@endpush
@endsection

