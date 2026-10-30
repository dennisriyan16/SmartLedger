<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseInvoiceFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-4 months', 'now');
        $dueDate = (clone $date)->modify('+'.rand(15, 45).' days');

        return [
            'invoice_no' => 'PI-'.date('Y').'-'.fake()->unique()->numerify('#####'),
            'supplier_id' => Supplier::factory(),
            'invoice_date' => $date->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
            'subtotal' => 0.00,
            'discount_total' => 0.00,
            'tax_total' => 0.00,
            'grand_total' => 0.00,
            'paid_amount' => 0.00,
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
