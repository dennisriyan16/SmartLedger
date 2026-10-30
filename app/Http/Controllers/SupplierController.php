<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $suppliers = Supplier::when($search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('gst_number', 'like', "%{$search}%");
        })
            ->withCount('purchaseInvoices')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' created successfully.");
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['purchaseInvoices' => function ($q) {
            $q->latest('invoice_date');
        }]);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$supplier->name}' updated successfully.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', "Supplier '{$name}' deleted successfully.");
    }
}
