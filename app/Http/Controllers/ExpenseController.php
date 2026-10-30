<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Budget;
use App\Models\Expense;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService
    ) {}

    public function index(Request $request): View
    {
        $category = $request->query('category');
        $budgetId = $request->query('budget_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $search = $request->query('search');

        $expenses = Expense::with('budget')
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($budgetId, fn ($q) => $q->where('budget_id', $budgetId))
            ->when($startDate, fn ($q) => $q->whereDate('expense_date', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('expense_date', '<=', $endDate))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->latest('expense_date')
            ->paginate(15)
            ->withQueryString();

        $budgets = Budget::orderBy('name')->get();
        $categories = Budget::distinct()->pluck('category');

        return view('expenses.index', compact('expenses', 'budgets', 'categories', 'category', 'budgetId', 'startDate', 'endDate', 'search'));
    }

    public function create(Request $request): View
    {
        $selectedBudgetId = $request->query('budget_id');
        $budgets = Budget::orderBy('name')->get();

        return view('expenses.create', compact('budgets', 'selectedBudgetId'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $budget = Budget::findOrFail($validated['budget_id']);

        // Check impact
        $impact = $this->budgetService->checkExpenseBudgetImpact($budget, $validated['amount']);

        $expense = Expense::create([
            'budget_id' => $budget->id,
            'title' => $validated['title'],
            'category' => $budget->category,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($impact['exceeds']) {
            return redirect()->route('expenses.index')
                ->with('warning', $impact['warning_message'].' Expense was saved.');
        }

        return redirect()->route('expenses.index')
            ->with('success', "Expense '{$expense->title}' logged successfully.");
    }

    public function edit(Expense $expense): View
    {
        $budgets = Budget::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'budgets'));
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validated();
        $budget = Budget::findOrFail($validated['budget_id']);

        $expense->update([
            'budget_id' => $budget->id,
            'title' => $validated['title'],
            'category' => $budget->category,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('expenses.index')
            ->with('success', "Expense '{$expense->title}' updated successfully.");
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $title = $expense->title;
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', "Expense '{$title}' deleted successfully.");
    }
}
