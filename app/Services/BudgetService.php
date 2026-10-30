<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Expense;

class BudgetService
{
    /**
     * Compute budget analytics:
     * - allocated: decimal amount
     * - spent: sum of related expenses (monthly budgets only within month/year; yearly aggregated across year)
     * - remaining: allocated - spent
     * - percentage: (spent / allocated) * 100
     * - status: 'On track' (< 80%), 'Near limit' (>= 80% and <= 100%), 'Exceeded' (> 100%)
     */
    public function getBudgetMetrics(Budget $budget): array
    {
        $allocated = (string) $budget->amount;

        $spentQuery = Expense::where('category', $budget->category);

        if ($budget->type === 'monthly') {
            $year = $budget->year;
            $month = $budget->month;
            $spentQuery->whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month);
        } else {
            // yearly budget: aggregate expenses for that category across the entire year
            $spentQuery->whereYear('expense_date', $budget->year);
        }

        $spentRaw = (string) $spentQuery->sum('amount');
        $spent = number_format((float) $spentRaw, 2, '.', '');

        $remaining = bcsub($allocated, $spent, 2);

        $percentage = 0.0;
        if (bccomp($allocated, '0', 2) > 0) {
            $percentage = round(((float) $spent / (float) $allocated) * 100, 1);
        }

        $status = 'On track';
        if ($percentage > 100.0 || bccomp($remaining, '0', 2) < 0) {
            $status = 'Exceeded';
        } elseif ($percentage >= 80.0) {
            $status = 'Near limit';
        }

        return [
            'budget' => $budget,
            'allocated' => $allocated,
            'spent' => $spent,
            'remaining' => $remaining,
            'percentage' => $percentage,
            'status' => $status,
        ];
    }

    /**
     * Check if a proposed expense amount exceeds the remaining budget balance.
     * Returns array with warning flag and metrics.
     */
    public function checkExpenseBudgetImpact(Budget $budget, float|string $proposedAmount): array
    {
        $metrics = $this->getBudgetMetrics($budget);
        $remaining = (float) $metrics['remaining'];
        $proposed = (float) $proposedAmount;

        $exceeds = $proposed > $remaining;
        $overAmount = $exceeds ? ($proposed - $remaining) : 0.00;

        return [
            'metrics' => $metrics,
            'exceeds' => $exceeds,
            'over_amount' => number_format($overAmount, 2, '.', ''),
            'warning_message' => $exceeds
                ? 'Warning: Adding this expense of $'.number_format($proposed, 2)." will exceed the '{$budget->name}' remaining budget of $".number_format($remaining, 2).' by $'.number_format($overAmount, 2).'.'
                : null,
        ];
    }
}
