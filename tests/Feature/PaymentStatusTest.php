<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\SalesInvoice;
use App\Services\PaymentStatusService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentStatusService $statusService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->statusService = new PaymentStatusService;
    }

    public function test_determine_status_logic(): void
    {
        $today = Carbon::today();
        $future = (clone $today)->addDays(15);
        $past = (clone $today)->subDays(5);

        // Paid when paid >= grand total
        $this->assertEquals('paid', $this->statusService->determineStatus('500.00', '500.00', $future));
        $this->assertEquals('paid', $this->statusService->determineStatus('500.00', '600.00', $past));

        // Pending when unpaid and future due date
        $this->assertEquals('pending', $this->statusService->determineStatus('500.00', '200.00', $future));

        // Overdue when unpaid and past due date
        $this->assertEquals('overdue', $this->statusService->determineStatus('500.00', '200.00', $past));
    }

    public function test_sync_invoice_payment_updates_model_status(): void
    {
        $customer = Customer::factory()->create();
        $invoice = SalesInvoice::create([
            'invoice_no' => 'SI-TEST-0001',
            'customer_id' => $customer->id,
            'invoice_date' => Carbon::today()->subDays(10)->toDateString(),
            'due_date' => Carbon::today()->subDays(2)->toDateString(),
            'subtotal' => 1000.00,
            'discount_total' => 0.00,
            'tax_total' => 0.00,
            'grand_total' => 1000.00,
            'paid_amount' => 0.00,
            'status' => 'pending',
        ]);

        // Sync initially -> should mark as overdue since due_date is past
        $this->statusService->syncInvoicePayment($invoice);
        $this->assertEquals('overdue', $invoice->fresh()->status);

        // Record full payment
        $invoice->payments()->create([
            'amount' => 1000.00,
            'payment_date' => Carbon::today()->toDateString(),
            'method' => 'Bank Transfer',
        ]);

        $this->statusService->syncInvoicePayment($invoice);
        $refreshed = $invoice->fresh();
        $this->assertEquals('paid', $refreshed->status);
        $this->assertEquals('1000.00', $refreshed->paid_amount);
    }
}
