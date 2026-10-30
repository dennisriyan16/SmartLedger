<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['product', 'service']),
            'sku' => fake()->unique()->bothify('SKU-####-????'),
            'unit_price' => fake()->randomFloat(2, 50, 2500),
            'tax_rate' => fake()->randomElement([0.00, 5.00, 12.00, 18.00]),
            'description' => fake()->sentence(),
        ];
    }
}
