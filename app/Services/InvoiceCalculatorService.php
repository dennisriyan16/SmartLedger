<?php

namespace App\Services;

class InvoiceCalculatorService
{
    /**
     * Compute line item totals using exact bcmath arithmetic rounded to 2 decimals.
     *
     * gross = qty * unit_price
     * discount = gross * (discount_percent / 100)
     * taxable = gross - discount
     * tax = taxable * (tax_percent / 100)
     * line_total = taxable + tax
     *
     * @return array{quantity: string, unit_price: string, discount_percent: string, tax_percent: string, gross: string, discount_amount: string, taxable_amount: string, tax_amount: string, line_total: string}
     */
    public function calculateLine(
        float|string $quantity,
        float|string $unitPrice,
        float|string $discountPercent = 0,
        float|string $taxPercent = 0
    ): array {
        $qty = number_format((float) $quantity, 2, '.', '');
        $price = number_format((float) $unitPrice, 2, '.', '');
        $discRate = number_format((float) $discountPercent, 2, '.', '');
        $taxRate = number_format((float) $taxPercent, 2, '.', '');

        // gross = qty * unit_price (4 decimal scale for intermediate precision)
        $grossExact = bcmul($qty, $price, 4);
        $gross = bcadd($grossExact, '0', 2);

        // discount = gross * (discount_percent / 100)
        $discFactor = bcdiv($discRate, '100', 6);
        $discAmountExact = bcmul($grossExact, $discFactor, 4);
        $discAmount = bcadd($discAmountExact, '0', 2);

        // taxable = gross - discount
        $taxable = bcsub($gross, $discAmount, 2);
        if (bccomp($taxable, '0', 2) < 0) {
            $taxable = '0.00';
        }

        // tax = taxable * (tax_percent / 100)
        $taxFactor = bcdiv($taxRate, '100', 6);
        $taxAmountExact = bcmul($taxable, $taxFactor, 4);
        $taxAmount = bcadd($taxAmountExact, '0', 2);

        // line_total = taxable + tax
        $lineTotal = bcadd($taxable, $taxAmount, 2);

        return [
            'quantity' => $qty,
            'unit_price' => $price,
            'discount_percent' => $discRate,
            'tax_percent' => $taxRate,
            'gross' => $gross,
            'discount_amount' => $discAmount,
            'taxable_amount' => $taxable,
            'tax_amount' => $taxAmount,
            'line_total' => $lineTotal,
        ];
    }

    /**
     * Compute invoice summary from an array of item row definitions.
     *
     * @param  array<int, array{quantity: float|string, unit_price: float|string, discount_percent?: float|string, tax_percent?: float|string, product_id?: int|string|null, description?: string}>  $items
     * @return array{subtotal: string, discount_total: string, tax_total: string, grand_total: string, items: array}
     */
    public function calculateInvoice(array $items): array
    {
        $subtotal = '0.00';
        $discountTotal = '0.00';
        $taxTotal = '0.00';
        $grandTotal = '0.00';
        $calculatedItems = [];

        foreach ($items as $item) {
            $qty = $item['quantity'] ?? 1;
            $price = $item['unit_price'] ?? 0;
            $disc = $item['discount_percent'] ?? 0;
            $tax = $item['tax_percent'] ?? 0;

            $lineCalc = $this->calculateLine($qty, $price, $disc, $tax);

            $subtotal = bcadd($subtotal, $lineCalc['taxable_amount'], 2);
            $discountTotal = bcadd($discountTotal, $lineCalc['discount_amount'], 2);
            $taxTotal = bcadd($taxTotal, $lineCalc['tax_amount'], 2);
            $grandTotal = bcadd($grandTotal, $lineCalc['line_total'], 2);

            $calculatedItems[] = array_merge($item, [
                'quantity' => $lineCalc['quantity'],
                'unit_price' => $lineCalc['unit_price'],
                'discount_percent' => $lineCalc['discount_percent'],
                'tax_percent' => $lineCalc['tax_percent'],
                'line_total' => $lineCalc['line_total'],
            ]);
        }

        return [
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'grand_total' => $grandTotal,
            'items' => $calculatedItems,
        ];
    }
}
