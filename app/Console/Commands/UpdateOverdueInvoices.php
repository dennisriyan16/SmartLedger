<?php

namespace App\Console\Commands;

use App\Services\PaymentStatusService;
use Illuminate\Console\Command;

class UpdateOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'invoices:update-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Scan pending invoices past their due dates and mark them as overdue';

    /**
     * Execute the console command.
     */
    public function handle(PaymentStatusService $service): int
    {
        $this->info('Scanning invoices for overdue status...');
        $updated = $service->updateAllOverdueInvoices();
        $this->info("Updated {$updated} invoice(s) to OVERDUE status.");

        return Command::SUCCESS;
    }
}
