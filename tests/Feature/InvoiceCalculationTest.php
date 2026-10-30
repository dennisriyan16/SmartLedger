<?php

namespace Tests\Feature;

use App\Services\InvoiceCalculatorService;
use Tests\TestCase;

class InvoiceCalculationTest extends TestCase
{
    protected InvoiceCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new InvoiceCalculatorService;
    }

    public function test_single_line_calculation_without_tax_or_discount(): void
    {
        // 5 * 100 = 500.00
        $result = $this->calculator->calculateLine(5, 100, 0, 0);

        $this->assertEquals('500.00', $result['gross']);
        $this->assertEquals('0.00', $result['discount_amount']);
        $this->assertEquals('500.00', $result['taxable_amount']);
        $this->assertEquals('0.00', $result['tax_amount']);
        $this->assertEquals('500.00', $result['line_total']);
    }

    public function test_line_calculation_with_discount_and_tax(): void
    {
        // qty: 2, price: 150.00 -> gross: 300.00
        // discount: 10% -> 30.00
        // taxable: 270.00
        // tax: 18% -> 48.60
        // line_total: 318.60
        $result = $this->calculator->calculateLine(2, 150, 10, 18);

        $this->assertEquals('300.00', $result['gross']);
        $this->assertEquals('30.00', $result['discount_amount']);
        $this->assertEquals('270.00', $result['taxable_amount']);
        $this->assertEquals('48.60', $result['tax_amount']);
        $this->assertEquals('318.60', $result['line_total']);
    }

    public function test_line_calculation_edge_case_with_fractional_cents(): void
    {
        // qty: 3, price: 19.99 -> gross: 59.97
        // discount: 7.5% -> 59.97 * 0.075 = 4.49775 -> 4.49
        // taxable: 59.97 - 4.49 = 55.48
        // tax: 8.25% -> 55.48 * 0.0825 = 4.5771 -> 4.57
        // line_total: 55.48 + 4.57 = 60.05
        $result = $this->calculator->calculateLine(3, 19.99, 7.5, 8.25);

        $this->assertEquals('59.97', $result['gross']);
        $this->assertEquals('4.49', $result['discount_amount']);
        $this->assertEquals('55.48', $result['taxable_amount']);
        $this->assertEquals('4.57', $result['tax_amount']);
        $this->assertEquals('60.05', $result['line_total']);
    }

    public function test_full_invoice_calculation_aggregation(): void
    {
        $items = [
            ['quantity' => 2, 'unit_price' => 100, 'discount_percent' => 10, 'tax_percent' => 10], // taxable: 180, tax: 18, total: 198
            ['quantity' => 1, 'unit_price' => 200, 'discount_percent' => 0, 'tax_percent' => 18],  // taxable: 200, tax: 36, total: 236
        ];

        $invoice = $this->calculator->calculateInvoice($items);

        $this->assertEquals('380.00', $invoice['subtotal']);
        $this->assertEquals('20.00', $invoice['discount_total']);
        $this->assertEquals('54.00', $invoice['tax_total']);
        $this->assertEquals('434.00', $invoice['grand_total']);
        $this->assertCount(2, $invoice['items']);
    }
}
