<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Budget',
            'type' => 'monthly',
            'year' => (int) date('Y'),
            'month' => (int) date('m'),
            'category' => fake()->randomElement(['Office Supplies', 'Marketing', 'Software & IT', 'Travel & Transport', 'Utilities', 'Maintenance']),
            'amount' => fake()->randomFloat(2, 5000, 50000),
        ];
    }
}
