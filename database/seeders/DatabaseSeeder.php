<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InvoiceCalculatorService;
use App\Services\PaymentStatusService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $calculator = new InvoiceCalculatorService;
        $statusService = new PaymentStatusService;

        // 1. Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Customers (~10)
        $customerData = [
            ['name' => 'Apex Global Technologies', 'email' => 'contact@apextech.io', 'phone' => '+1-555-0101', 'address' => '100 Silicon Blvd, Suite 200, Austin, TX', 'gst_number' => 'GSTUSA100123'],
            ['name' => 'BlueStar Logistics Pvt Ltd', 'email' => 'billing@bluestar.com', 'phone' => '+91-9820011223', 'address' => 'Plot 45, MIDC Industrial Area, Pune, India', 'gst_number' => '27AABCU9603R1ZM'],
            ['name' => 'CloudScale Analytics Corp', 'email' => 'accounts@cloudscale.net', 'phone' => '+1-555-0103', 'address' => '742 Evergreen Terrace, Springfield, OR', 'gst_number' => 'GSTUSA100456'],
            ['name' => 'Delta Dynamics Ltd', 'email' => 'finance@deltadynamics.co.uk', 'phone' => '+44-20-7946-0912', 'address' => '12 Cannon St, London, EC4N 6AS, UK', 'gst_number' => 'GB999999973'],
            ['name' => 'Echo Enterprise Solutions', 'email' => 'invoicing@echoenterprise.org', 'phone' => '+1-555-0105', 'address' => '404 Innovate Way, Boston, MA', 'gst_number' => 'GSTUSA100789'],
            ['name' => 'Frontline Health Services', 'email' => 'payables@frontlinehealth.org', 'phone' => '+1-555-0106', 'address' => '250 Medical Center Dr, Atlanta, GA', 'gst_number' => 'GSTUSA100990'],
            ['name' => 'GreenLeaf Bio-Agri', 'email' => 'admin@greenleafbio.in', 'phone' => '+91-9876543210', 'address' => 'Farm 12, Agri Corridor, Bengaluru, India', 'gst_number' => '29AAACG1234A1Z5'],
            ['name' => 'Horizon Retail Partners', 'email' => 'accounts@horizonretail.com', 'phone' => '+1-555-0108', 'address' => '88 Market St, San Francisco, CA', 'gst_number' => 'GSTUSA100341'],
            ['name' => 'Integrity Media Group', 'email' => 'billing@integritymedia.com', 'phone' => '+1-555-0109', 'address' => '500 Madison Ave, New York, NY', 'gst_number' => 'GSTUSA100882'],
            ['name' => 'Jupiter Aerospace Inc', 'email' => 'vendors@jupiteraero.com', 'phone' => '+1-555-0110', 'address' => '1 Aerospace Way, Seattle, WA', 'gst_number' => 'GSTUSA100554'],
        ];

        $customers = [];
        foreach ($customerData as $c) {
            $customers[] = Customer::create($c);
        }

        // 3. Suppliers (~10)
        $supplierData = [
            ['name' => 'Dell Technologies OEM Supplies', 'email' => 'orders@dell-partner.com', 'phone' => '+1-800-456-3355', 'address' => 'One Dell Way, Round Rock, TX', 'gst_number' => 'GSTUSA200111'],
            ['name' => 'AWS Cloud Infrastructure Inc', 'email' => 'billing@amazonwebservices.com', 'phone' => '+1-800-297-2270', 'address' => '410 Terry Ave N, Seattle, WA', 'gst_number' => 'GSTUSA200222'],
            ['name' => 'Metro Office Depot & Furniture', 'email' => 'support@metrostationery.in', 'phone' => '+91-9811223344', 'address' => 'B-12 Connaught Place, New Delhi, India', 'gst_number' => '07AAACM3344K1ZV'],
            ['name' => 'Swift Courier & Freight Network', 'email' => 'ops@swiftfreight.com', 'phone' => '+1-555-0204', 'address' => '89 Logistics Blvd, Chicago, IL', 'gst_number' => 'GSTUSA200444'],
            ['name' => 'Nordic Fiber Optic Telecom', 'email' => 'enterprise@nordictelecom.eu', 'phone' => '+46-8-123456', 'address' => 'Stureplan 4, Stockholm, Sweden', 'gst_number' => 'SE556000461501'],
            ['name' => 'Prime Power & Electric Utilities', 'email' => 'corp@primeelectric.com', 'phone' => '+1-555-0206', 'address' => '71 Industrial Pkwy, Cleveland, OH', 'gst_number' => 'GSTUSA200666'],
            ['name' => 'SecureGuard Facilities & Safety', 'email' => 'contracts@secureguard.com', 'phone' => '+1-555-0207', 'address' => '312 Safety Dr, Dallas, TX', 'gst_number' => 'GSTUSA200777'],
            ['name' => 'Creative Pulse Design & Print', 'email' => 'studios@creativepulse.io', 'phone' => '+1-555-0208', 'address' => '64 SOHO Square, New York, NY', 'gst_number' => 'GSTUSA200888'],
            ['name' => 'Global Software Licenses Ltd', 'email' => 'licensing@globalsw.com', 'phone' => '+44-20-7123-4567', 'address' => '30 St Mary Axe, London, UK', 'gst_number' => 'GB888888812'],
            ['name' => 'CleanPro Environmental Services', 'email' => 'info@cleanpro-services.com', 'phone' => '+1-555-0210', 'address' => '15 Green Park, Houston, TX', 'gst_number' => 'GSTUSA200999'],
        ];

        $suppliers = [];
        foreach ($supplierData as $s) {
            $suppliers[] = Supplier::create($s);
        }

        // 4. Products (~20)
        $productsData = [
            ['name' => 'Dell Latitude 5440 Laptop', 'type' => 'product', 'sku' => 'HW-DELL-5440', 'unit_price' => 1150.00, 'tax_rate' => 18.00, 'description' => '14-inch Business Laptop, 16GB RAM, 512GB SSD'],
            ['name' => 'Dell UltraSharp 27 4K Monitor', 'type' => 'product', 'sku' => 'HW-DELL-U2723', 'unit_price' => 520.00, 'tax_rate' => 18.00, 'description' => '4K IPS USB-C Hub Monitor'],
            ['name' => 'Logitech MX Master 3S Mouse', 'type' => 'product', 'sku' => 'ACC-LOGI-MX3S', 'unit_price' => 99.00, 'tax_rate' => 18.00, 'description' => 'Performance Wireless Ergonomic Mouse'],
            ['name' => 'Ergonomic Mesh Office Chair', 'type' => 'product', 'sku' => 'FUR-CHAIR-ERGO', 'unit_price' => 340.00, 'tax_rate' => 12.00, 'description' => 'High back lumbar support desk chair'],
            ['name' => 'Height Adjustable Standing Desk', 'type' => 'product', 'sku' => 'FUR-DESK-MOTOR', 'unit_price' => 680.00, 'tax_rate' => 12.00, 'description' => 'Dual motor electric sit-stand desk 60x30'],
            ['name' => 'Cisco Catalyst 24-Port Switch', 'type' => 'product', 'sku' => 'NET-CSCO-2960', 'unit_price' => 890.00, 'tax_rate' => 18.00, 'description' => 'Gigabit managed enterprise network switch'],
            ['name' => 'Cat6 Shielded Patch Cable 10m', 'type' => 'product', 'sku' => 'NET-CAT6-10M', 'unit_price' => 15.00, 'tax_rate' => 18.00, 'description' => 'High speed gigabit ethernet patch cord'],
            ['name' => 'A4 High White Copy Paper (Box)', 'type' => 'product', 'sku' => 'STN-PAPER-A4', 'unit_price' => 38.00, 'tax_rate' => 5.00, 'description' => '5 reams 80gsm multipurpose paper'],
            ['name' => 'HP LaserJet Toner Cartridge 58A', 'type' => 'product', 'sku' => 'STN-TONER-58A', 'unit_price' => 125.00, 'tax_rate' => 18.00, 'description' => 'Original black laser toner cartridge'],
            ['name' => 'APC Smart-UPS 1500VA', 'type' => 'product', 'sku' => 'PWR-APC-1500', 'unit_price' => 620.00, 'tax_rate' => 18.00, 'description' => 'Line interactive backup battery power unit'],
            ['name' => 'Enterprise Cloud Consulting', 'type' => 'service', 'sku' => 'SRV-CLOUD-CONS', 'unit_price' => 180.00, 'tax_rate' => 18.00, 'description' => 'Hourly cloud architecture & migration advisory'],
            ['name' => 'Full-Stack Software Development', 'type' => 'service', 'sku' => 'SRV-DEV-HOURLY', 'unit_price' => 120.00, 'tax_rate' => 18.00, 'description' => 'Hourly custom Laravel / Vue engineering services'],
            ['name' => 'UI/UX Design Sprint (Weekly)', 'type' => 'service', 'sku' => 'SRV-DES-SPRINT', 'unit_price' => 2500.00, 'tax_rate' => 18.00, 'description' => 'Dedicated product design & prototyping sprint'],
            ['name' => 'Annual Server Maintenance SLA', 'type' => 'service', 'sku' => 'SRV-SLA-ANNUAL', 'unit_price' => 4800.00, 'tax_rate' => 18.00, 'description' => '24/7 server monitoring, backups, patching'],
            ['name' => 'Quarterly Cybersecurity Audit', 'type' => 'service', 'sku' => 'SRV-SEC-AUDIT', 'unit_price' => 3500.00, 'tax_rate' => 18.00, 'description' => 'Vulnerability assessment & penetration testing'],
            ['name' => 'SEO & Performance Optimization', 'type' => 'service', 'sku' => 'SRV-SEO-MONTH', 'unit_price' => 1200.00, 'tax_rate' => 18.00, 'description' => 'Core Web Vitals and technical SEO management'],
            ['name' => 'Corporate Branding Package', 'type' => 'service', 'sku' => 'SRV-BRAND-KIT', 'unit_price' => 2200.00, 'tax_rate' => 18.00, 'description' => 'Logo, typography, color guide and assets'],
            ['name' => 'Network Installation & Cabling', 'type' => 'service', 'sku' => 'SRV-NET-DEPLOY', 'unit_price' => 950.00, 'tax_rate' => 18.00, 'description' => 'Onsite structured cabling and rack mounting'],
            ['name' => 'Database Performance Tuning', 'type' => 'service', 'sku' => 'SRV-DB-TUNE', 'unit_price' => 1500.00, 'tax_rate' => 18.00, 'description' => 'MySQL query indexing, caching and tuning'],
            ['name' => 'Staff IT Training Workshop (Day)', 'type' => 'service', 'sku' => 'SRV-TRAIN-DAY', 'unit_price' => 1600.00, 'tax_rate' => 18.00, 'description' => 'Hands-on IT security & tools enablement seminar'],
        ];

        $products = [];
        foreach ($productsData as $p) {
            $products[] = Product::create($p);
        }

        // 5. Budgets (Monthly across several months + Yearly for current year)
        $currentYear = (int) date('Y');
        $categories = [
            'Office Supplies' => 15000,
            'Marketing' => 35000,
            'Software & IT' => 50000,
            'Travel & Transport' => 20000,
            'Utilities' => 18000,
            'Maintenance' => 12000,
        ];

        $budgets = [];

        // Yearly Budgets
        foreach ($categories as $cat => $annualAmt) {
            $budgets[] = Budget::create([
                'name' => "Annual {$cat} Budget {$currentYear}",
                'type' => 'yearly',
                'year' => $currentYear,
                'month' => null,
                'category' => $cat,
                'amount' => $annualAmt * 1.2,
            ]);
        }

        // Monthly Budgets for past 6 months up to current month
        $currentMonth = (int) date('n');
        for ($m = max(1, $currentMonth - 5); $m <= $currentMonth; $m++) {
            $monthName = Carbon::create($currentYear, $m, 1)->format('F');
            foreach ($categories as $cat => $annualAmt) {
                $monthlyAmt = round($annualAmt / 12, 2);
                $budgets[] = Budget::create([
                    'name' => "{$monthName} {$cat}",
                    'type' => 'monthly',
                    'year' => $currentYear,
                    'month' => $m,
                    'category' => $cat,
                    'amount' => $monthlyAmt,
                ]);
            }
        }

        // 6. Expenses across budgets
        $expenseTitles = [
            'Office Supplies' => ['High quality paper & folders', 'Stationery restock', 'Coffee machine supplies', 'Ink cartridges pack'],
            'Marketing' => ['Google Ads Campaign', 'Meta Ads promotion', 'Flyers printing & event banner', 'Influencer sponsorship'],
            'Software & IT' => ['AWS Monthly Hosting', 'Google Workspace subscription', 'GitHub Enterprise licenses', 'Slack workspace plan'],
            'Travel & Transport' => ['Client visit flight tickets', 'Taxi & cab reimbursements', 'Hotel stay for conference', 'Fuel allowance'],
            'Utilities' => ['Monthly Electricity bill', 'High speed fiber broadband', 'Water utility charges', 'Office HVAC service'],
            'Maintenance' => ['AC repair & gas refill', 'Plumbing repair work', 'Office deep sanitization', 'Fire extinguisher inspection'],
        ];

        foreach ($budgets as $b) {
            // Generate 1-4 expenses per budget
            $numExpenses = rand(2, 4);
            $titles = $expenseTitles[$b->category] ?? ['General operational expense'];

            for ($i = 0; $i < $numExpenses; $i++) {
                $title = $titles[array_rand($titles)];
                // expense amount between 15% and 40% of budget amount
                $portion = rand(15, 40) / 100;
                $amount = round((float) $b->amount * $portion, 2);

                if ($b->type === 'monthly' && $b->month) {
                    $day = rand(1, 28);
                    $expenseDate = Carbon::create($b->year, $b->month, $day)->toDateString();
                } else {
                    $monthRand = rand(1, $currentMonth);
                    $dayRand = rand(1, 28);
                    $expenseDate = Carbon::create($b->year, $monthRand, $dayRand)->toDateString();
                }

                Expense::create([
                    'budget_id' => $b->id,
                    'title' => $title,
                    'category' => $b->category,
                    'amount' => $amount,
                    'expense_date' => $expenseDate,
                    'notes' => 'Authorized by finance department',
                ]);
            }
        }

        // 7. Purchase Invoices (~25)
        for ($i = 1; $i <= 25; $i++) {
            $supplier = $suppliers[array_rand($suppliers)];
            $invoiceNum = sprintf('PI-%s-%04d', $currentYear, $i);

            // Generate invoice dates spread across past 4 months
            $daysAgo = rand(5, 120);
            $invDate = Carbon::today()->subDays($daysAgo);
            // Due date is between 15 and 45 days after invoice date
            $dueTerms = rand(15, 30);
            $dueDate = (clone $invDate)->addDays($dueTerms);

            // Select 1 to 4 products for items
            $itemCount = rand(1, 4);
            $selectedProducts = array_rand($products, $itemCount);
            if (! is_array($selectedProducts)) {
                $selectedProducts = [$selectedProducts];
            }

            $rawItems = [];
            foreach ($selectedProducts as $prodIndex) {
                $p = $products[$prodIndex];
                $qty = rand(1, 5);
                $disc = rand(0, 3) * 5; // 0, 5, 10, 15%
                $rawItems[] = [
                    'product_id' => $p->id,
                    'description' => $p->name.' - '.$p->description,
                    'quantity' => $qty,
                    'unit_price' => (float) $p->unit_price,
                    'discount_percent' => $disc,
                    'tax_percent' => (float) $p->tax_rate,
                ];
            }

            $calc = $calculator->calculateInvoice($rawItems);

            $purchase = PurchaseInvoice::create([
                'invoice_no' => $invoiceNum,
                'supplier_id' => $supplier->id,
                'invoice_date' => $invDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'subtotal' => $calc['subtotal'],
                'discount_total' => $calc['discount_total'],
                'tax_total' => $calc['tax_total'],
                'grand_total' => $calc['grand_total'],
                'paid_amount' => '0.00',
                'status' => 'pending',
                'notes' => 'Demo purchase order from '.$supplier->name,
            ]);

            foreach ($calc['items'] as $itemRow) {
                $purchase->items()->create([
                    'product_id' => $itemRow['product_id'],
                    'description' => $itemRow['description'],
                    'quantity' => $itemRow['quantity'],
                    'unit_price' => $itemRow['unit_price'],
                    'discount_percent' => $itemRow['discount_percent'],
                    'tax_percent' => $itemRow['tax_percent'],
                    'line_total' => $itemRow['line_total'],
                ]);
            }

            // Decide payment scenario (Paid, Partial/Pending, Unpaid Overdue, Unpaid Pending)
            $scenario = $i % 4;
            if ($scenario === 0) {
                // Fully Paid
                Payment::create([
                    'payable_type' => PurchaseInvoice::class,
                    'payable_id' => $purchase->id,
                    'amount' => $purchase->grand_total,
                    'payment_date' => (clone $invDate)->addDays(rand(2, 10))->toDateString(),
                    'method' => 'Bank Transfer',
                    'reference' => 'NEFT-'.rand(100000, 999999),
                    'notes' => 'Full settlement',
                ]);
            } elseif ($scenario === 1) {
                // Partial payment
                $half = bcdiv((string) $purchase->grand_total, '2', 2);
                if (bccomp($half, '0', 2) > 0) {
                    Payment::create([
                        'payable_type' => PurchaseInvoice::class,
                        'payable_id' => $purchase->id,
                        'amount' => $half,
                        'payment_date' => (clone $invDate)->addDays(rand(2, 8))->toDateString(),
                        'method' => 'UPI',
                        'reference' => 'UPI-'.rand(100000, 999999),
                        'notes' => '50% advance payment',
                    ]);
                }
            }

            // Sync payment status
            $statusService->syncInvoicePayment($purchase);
        }

        // 8. Sales Invoices (~25)
        for ($i = 1; $i <= 25; $i++) {
            $customer = $customers[array_rand($customers)];
            $invoiceNum = sprintf('SI-%s-%04d', $currentYear, $i);

            $daysAgo = rand(3, 110);
            $invDate = Carbon::today()->subDays($daysAgo);
            $dueTerms = rand(15, 30);
            $dueDate = (clone $invDate)->addDays($dueTerms);

            $itemCount = rand(1, 4);
            $selectedProducts = array_rand($products, $itemCount);
            if (! is_array($selectedProducts)) {
                $selectedProducts = [$selectedProducts];
            }

            $rawItems = [];
            foreach ($selectedProducts as $prodIndex) {
                $p = $products[$prodIndex];
                $qty = rand(1, 6);
                $disc = rand(0, 2) * 5; // 0, 5, 10%
                $rawItems[] = [
                    'product_id' => $p->id,
                    'description' => $p->name.' - '.$p->description,
                    'quantity' => $qty,
                    'unit_price' => (float) $p->unit_price,
                    'discount_percent' => $disc,
                    'tax_percent' => (float) $p->tax_rate,
                ];
            }

            $calc = $calculator->calculateInvoice($rawItems);

            $sale = SalesInvoice::create([
                'invoice_no' => $invoiceNum,
                'customer_id' => $customer->id,
                'invoice_date' => $invDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'subtotal' => $calc['subtotal'],
                'discount_total' => $calc['discount_total'],
                'tax_total' => $calc['tax_total'],
                'grand_total' => $calc['grand_total'],
                'paid_amount' => '0.00',
                'status' => 'pending',
                'notes' => 'Sales order for '.$customer->name,
            ]);

            foreach ($calc['items'] as $itemRow) {
                $sale->items()->create([
                    'product_id' => $itemRow['product_id'],
                    'description' => $itemRow['description'],
                    'quantity' => $itemRow['quantity'],
                    'unit_price' => $itemRow['unit_price'],
                    'discount_percent' => $itemRow['discount_percent'],
                    'tax_percent' => $itemRow['tax_percent'],
                    'line_total' => $itemRow['line_total'],
                ]);
            }

            // Scenario distribution
            $scenario = $i % 4;
            if ($scenario === 0) {
                // Fully Paid
                Payment::create([
                    'payable_type' => SalesInvoice::class,
                    'payable_id' => $sale->id,
                    'amount' => $sale->grand_total,
                    'payment_date' => (clone $invDate)->addDays(rand(3, 12))->toDateString(),
                    'method' => 'Credit Card',
                    'reference' => 'STRIPE-'.rand(100000, 999999),
                    'notes' => 'Paid via customer online portal',
                ]);
            } elseif ($scenario === 1) {
                // Partial payment
                $part = bcdiv((string) $sale->grand_total, '3', 2);
                if (bccomp($part, '0', 2) > 0) {
                    Payment::create([
                        'payable_type' => SalesInvoice::class,
                        'payable_id' => $sale->id,
                        'amount' => $part,
                        'payment_date' => (clone $invDate)->addDays(rand(2, 7))->toDateString(),
                        'method' => 'Bank Transfer',
                        'reference' => 'RTGS-'.rand(100000, 999999),
                        'notes' => 'First milestone release',
                    ]);
                }
            }

            $statusService->syncInvoicePayment($sale);
        }

        // Final overdue sweep
        $statusService->updateAllOverdueInvoices();
    }
}
