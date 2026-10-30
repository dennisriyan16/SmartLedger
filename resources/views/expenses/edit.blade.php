@extends('layouts.app')

@section('title', 'Edit Expense')
@section('page_title', 'Edit Expense')
@section('page_subtitle', 'Update expense details, amount or linked budget allocation')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Edit Expense Information">
            <x-slot:actions>
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to Expenses
                </a>
            </x-slot:actions>

            <form action="{{ route('expenses.update', $expense) }}" method="POST" novalidate>
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="budget_id" class="form-label fw-semibold">Target Budget <span class="text-danger">*</span></label>
                        <select name="budget_id" id="budget_id" class="form-select @error('budget_id') is-invalid @enderror" required>
                            @foreach ($budgets as $b)
                                <option value="{{ $b->id }}" {{ old('budget_id', $expense->budget_id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->category }} &bull; Cap: ${{ number_format($b->amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('budget_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="title" class="form-label fw-semibold">Expense Title / Payee <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $expense->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="amount" class="form-label fw-semibold">Amount ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $expense->amount) }}" required>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="expense_date" class="form-label fw-semibold">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" id="expense_date" class="form-control @error('expense_date') is-invalid @enderror" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required>
                        @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-12">
                        <label for="notes" class="form-label fw-semibold">Notes / Receipt Reference</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $expense->notes) }}</textarea>
                        @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('expenses.index') }}" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Update Expense
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

