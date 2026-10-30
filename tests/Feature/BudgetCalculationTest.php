<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Expense;
use App\Services\BudgetService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected BudgetService $budgetService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->budgetService = new BudgetService;
    }

    public function test_budget_metrics_calculation_and_status(): void
    {
        $budget = Budget::create([
            'name' => 'IT Software Budget',
            'type' => 'monthly',
            'year' => (int) date('Y'),
            'month' => (int) date('n'),
            'category' => 'Software & IT',
            'amount' => 1000.00,
        ]);

        // 1. Initial State: spent = 0, remaining = 1000, status = On track
        $metrics = $this->budgetService->getBudgetMetrics($budget);
        $this->assertEquals('1000.00', $metrics['allocated']);
        $this->assertEquals('0.00', $metrics['spent']);
        $this->assertEquals('1000.00', $metrics['remaining']);
        $this->assertEquals('On track', $metrics['status']);

        // 2. Add expense of $850 (85%) -> status = Near limit
        Expense::create([
            'budget_id' => $budget->id,
            'title' => 'Server Licenses',
            'category' => $budget->category,
            'amount' => 850.00,
            'expense_date' => Carbon::create($budget->year, $budget->month, 10)->toDateString(),
        ]);

        $metrics = $this->budgetService->getBudgetMetrics($budget);
        $this->assertEquals('850.00', $metrics['spent']);
        $this->assertEquals('150.00', $metrics['remaining']);
        $this->assertEquals(85.0, $metrics['percentage']);
        $this->assertEquals('Near limit', $metrics['status']);

        // 3. Add expense of $200 (Total $1050 / 105%) -> status = Exceeded
        Expense::create([
            'budget_id' => $budget->id,
            'title' => 'Domain Renewal',
            'category' => $budget->category,
            'amount' => 200.00,
            'expense_date' => Carbon::create($budget->year, $budget->month, 15)->toDateString(),
        ]);

        $metrics = $this->budgetService->getBudgetMetrics($budget);
        $this->assertEquals('1050.00', $metrics['spent']);
        $this->assertEquals('-50.00', $metrics['remaining']);
        $this->assertEquals('Exceeded', $metrics['status']);
    }

    public function test_expense_impact_warning_detection(): void
    {
        $budget = Budget::create([
            'name' => 'Marketing Budget',
            'type' => 'yearly',
            'year' => 2026,
            'month' => null,
            'category' => 'Marketing',
            'amount' => 5000.00,
        ]);

        // Check impact of $6000 expense
        $impact = $this->budgetService->checkExpenseBudgetImpact($budget, 6000.00);

        $this->assertTrue($impact['exceeds']);
        $this->assertEquals('1000.00', $impact['over_amount']);
        $this->assertNotNull($impact['warning_message']);
    }
}
