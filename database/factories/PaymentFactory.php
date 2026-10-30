<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 50, 2000),
            'payment_date' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'method' => fake()->randomElement(['Cash', 'Bank Transfer', 'UPI', 'Credit Card', 'Cheque']),
            'reference' => 'TXN-'.fake()->numerify('######'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
