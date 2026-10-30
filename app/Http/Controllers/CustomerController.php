<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $customers = Customer::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('gst_number', 'like', "%{$search}%");
        })
            ->withCount('salesInvoices')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->validated());

        return redirect()->route('customers.index')
            ->with('success', "Customer '{$customer->name}' created successfully.");
    }

    public function show(Customer $customer): View
    {
        $customer->load(['salesInvoices' => function ($q) {
            $q->latest('invoice_date');
        }]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')
            ->with('success', "Customer '{$customer->name}' updated successfully.");
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', "Customer '{$name}' deleted successfully.");
    }
}
