<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService
    ) {}

    public function index(Request $request): View
    {
        $year = $request->query('year', date('Y'));
        $type = $request->query('type');
        $category = $request->query('category');

        $budgetsQuery = Budget::where('year', $year)
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderBy('type', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('category');

        $budgets = $budgetsQuery->paginate(12)->withQueryString();

        // Attach computed metrics
        $budgets->getCollection()->transform(function ($b) {
            $b->metrics = $this->budgetService->getBudgetMetrics($b);

            return $b;
        });

        $categories = Budget::distinct()->pluck('category');
        $years = Budget::distinct()->pluck('year')->sortDesc();
        if (! $years->contains((int) date('Y'))) {
            $years->prepend((int) date('Y'));
        }

        return view('budgets.index', compact('budgets', 'categories', 'years', 'year', 'type', 'category'));
    }

    public function create(): View
    {
        $categories = [
            'Office Supplies',
            'Marketing',
            'Software & IT',
            'Travel & Transport',
            'Utilities',
            'Maintenance',
            'Salaries & Benefits',
            'Legal & Accounting',
        ];

        return view('budgets.create', compact('categories'));
    }

    public function store(BudgetRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if ($validated['type'] === 'yearly') {
            $validated['month'] = null;
        }

        $budget = Budget::create($validated);

        return redirect()->route('budgets.show', $budget)
            ->with('success', "Budget '{$budget->name}' created successfully.");
    }

    public function show(Budget $budget): View
    {
        $metrics = $this->budgetService->getBudgetMetrics($budget);

        $expenses = $budget->expenses()
            ->latest('expense_date')
            ->paginate(15);

        return view('budgets.show', compact('budget', 'metrics', 'expenses'));
    }

    public function edit(Budget $budget): View
    {
        $categories = [
            'Office Supplies',
            'Marketing',
            'Software & IT',
            'Travel & Transport',
            'Utilities',
            'Maintenance',
            'Salaries & Benefits',
            'Legal & Accounting',
        ];

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(BudgetRequest $request, Budget $budget): RedirectResponse
    {
        $validated = $request->validated();
        if ($validated['type'] === 'yearly') {
            $validated['month'] = null;
        }

        $budget->update($validated);

        return redirect()->route('budgets.show', $budget)
            ->with('success', "Budget '{$budget->name}' updated successfully.");
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $name = $budget->name;
        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', "Budget '{$name}' deleted successfully.");
    }
}
