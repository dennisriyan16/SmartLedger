<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:monthly,yearly'],
            'year' => ['required', 'integer', 'min:2000', 'max:2099'],
            'month' => ['required_if:type,monthly', 'nullable', 'integer', 'min:1', 'max:12'],
            'category' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
