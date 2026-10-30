<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'description' => fake()->sentence(3),
            'quantity' => fake()->randomFloat(2, 1, 10),
            'unit_price' => fake()->randomFloat(2, 50, 1000),
            'discount_percent' => fake()->randomElement([0.00, 5.00, 10.00]),
            'tax_percent' => fake()->randomElement([0.00, 5.00, 12.00, 18.00]),
            'line_total' => 0.00,
        ];
    }
}
