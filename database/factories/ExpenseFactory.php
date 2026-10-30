<?php

namespace Database\Factories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'budget_id' => Budget::factory(),
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(['Office Supplies', 'Marketing', 'Software & IT', 'Travel & Transport', 'Utilities', 'Maintenance']),
            'amount' => fake()->randomFloat(2, 100, 3000),
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
