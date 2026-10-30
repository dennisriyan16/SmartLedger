# SmartLedger – Invoice & Budget Management System

A financial control, billing, and budget tracking system engineered inside **Laravel 12**, **MySQL**, and **Bootstrap 5**.

---

## 🚀 Key Features

1. **Executive Financial Dashboard**
   - Summary metric cards: Total Sales, Total Purchases, Outstanding Receivables, Payables, Overdue balances, and Net Profit estimation.
   - Interactive charts (via Chart.js): Monthly Sales vs Purchases (last 6 months), Expenses by category breakdown (doughnut).
   - Urgent overdue alerts table & recent transactions.
   - **Frankfurter Currency API Integration**: Real-time live exchange rate lookup (USD, EUR, GBP, INR, JPY, CAD) with automatic caching and graceful offline fallback.

2. **Sales & Purchase Invoicing**
   - Auto-generated sequential invoice numbers (`SI-YYYY-####`, `PI-YYYY-####`).
   - Interactive dynamic line-items form with live client-side calculations and rigorous server-side recalculation using exact **BCMath** decimal math.
   - Per-line calculation: `gross = qty × price`, `discount = gross × discount%`, `taxable = gross − discount`, `tax = taxable × tax%`, `line_total = taxable + tax`.
   - Polymorphic line-items (`invoiceable`) supporting catalogue items or custom services.
   - Database transactions on all invoice saves, updates, and deletes.
   - Print-friendly professional layout formatted for paper printing or PDF export.

3. **Payment Tracking & Overdue Lifecycle**
   - Partial and full payment tracking. Strict validation preventing payments from exceeding remaining balance.
   - Real-time status transitions: `Paid`, `Pending`, and `Overdue` (when past due date and balance remains).
   - Automated artisan command `php artisan invoices:update-overdue` configured in the Laravel scheduler.

4. **Budget Management & Expense Control**
   - Create monthly and annual budgets categorized by operational department.
   - Expense tracking against budget allocations.
   - Dynamic threshold analysis:
     - **On track** (< 80% used)
     - **Near limit** (≥ 80% used)
     - **Exceeded** (> 100% used)
   - Real-time warning banners if a proposed expense pushes a budget past its remaining limit.

5. **Reports & Financial Audits**
   - Date-range filtered reports:
     - Statement of Profit & Loss (Sales − Purchases − Expenses)
     - Sales summary breakdown
     - Purchase procurement summary
     - Tax audit (Output GST/Sales tax collected vs Input Tax Credit paid)
     - Budget vs Actual spend comparison
     - Outstanding receivables & payables
   - 1-click Streaming CSV export for all report types.

6. **Master Data Management**
   - Complete CRUD for **Customers**, **Suppliers**, and **Products/Services**.
   - Live search filters, SKU validation, pagination, and deletion safety modals.

---

## 🛠️ Tech Stack & Prerequisites

- **Backend:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL / MariaDB (XAMPP default)
- **Frontend:** Bootstrap 5 (CDN), Bootstrap Icons, Chart.js
- **Exchange Rate API:** Frankfurter API (`https://api.frankfurter.dev`)
- **Prerequisites:**
  - [XAMPP](https://www.apachefriends.org/) with Apache and MySQL running
  - PHP 8.2 or higher with `bcmath`, `pdo_mysql`, and `curl` extensions enabled
  - [Composer](https://getcomposer.org/)

---

## 🗄️ Database Architecture & Entity-Relationship (ER) Summary

| Table | Description | Key Relationships |
|---|---|---|
| `users` | Authenticated admins & personnel | Has `role` field ('admin') |
| `customers` | Client companies & billing contacts | `hasMany(SalesInvoice)` |
| `suppliers` | Vendors and procurement sources | `hasMany(PurchaseInvoice)` |
| `products` | Catalogue items & hourly services | `hasMany(InvoiceItem)` |
| `purchase_invoices` | Accounts payable vendor bills | `belongsTo(Supplier)`, `morphMany(InvoiceItem)`, `morphMany(Payment)` |
| `sales_invoices` | Accounts receivable customer bills | `belongsTo(Customer)`, `morphMany(InvoiceItem)`, `morphMany(Payment)` |
| `invoice_items` | Polymorphic invoice lines | `morphTo(invoiceable)` (PurchaseInvoice / SalesInvoice), `belongsTo(Product)` |
| `budgets` | Departmental budget allocations | `hasMany(Expense)` |
| `expenses` | Recorded expenditures | `belongsTo(Budget)` |
| `payments` | Polymorphic payment receipts / disbursements | `morphTo(payable)` (PurchaseInvoice / SalesInvoice) |

### ER Diagram Textual Flow:
```
[User] (role: admin)
   │
[Customer] ────< (1:N) >─── [SalesInvoice] ────< (1:N) >─── [InvoiceItem] (morph) >─── [Product]
                                   │
                                   └───< (1:N) >─── [Payment] (morph)

[Supplier] ────< (1:N) >─── [PurchaseInvoice] ─< (1:N) >─── [InvoiceItem] (morph) >─── [Product]
                                   │
                                   └───< (1:N) >─── [Payment] (morph)

[Budget] ──────< (1:N) >─── [Expense]
```

---

## 📦 Step-by-Step Setup Guide

### 1. Configure Environment
Copy `.env.example` to `.env` if not already present:
```bash
cp .env.example .env
```
Ensure your database parameters in `.env` match your local XAMPP MySQL setup:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=invoice_budget_system
DB_USERNAME=root
DB_PASSWORD=

FRANKFURTER_API_URL=https://api.frankfurter.dev
```

### 2. Generate Application Key
```bash
php artisan key:generate
```

### 3. Create Database & Run Migrations + Seeders
Ensure MySQL is started in your XAMPP Control Panel. Then run:
```bash
php artisan migrate:fresh --seed
```
This will set up all tables and seed realistic demo data:
- **1 Admin user**
- **10 Customers**
- **10 Suppliers**
- **20 Products & Services**
- **25 Purchase Invoices** with dynamic items & payments
- **25 Sales Invoices** with dynamic items & payments
- **42 Budgets** (monthly & annual) and **~130 Expenses**

### 4. Demo Login Credentials
Navigate to `http://127.0.0.1:8000/login` and sign in with:
- **Email:** `admin@example.com`
- **Password:** `password`

### 5. Running the Application
Start the local PHP server:
```bash
php artisan serve
```
Open your browser and visit: `http://127.0.0.1:8000`

---

## ⏰ Scheduler & Automation

To run the automated invoice overdue check manually:
```bash
php artisan invoices:update-overdue
```

To run the Laravel schedule worker continuously in the background:
```bash
php artisan schedule:work
```

---

## 🧪 Running Automated Tests & Code Quality

Run the comprehensive test suite (Invoice BCMath calculation edge cases, payment status transitions, budget usage calculations, and Frankfurter HTTP mocking):
```bash
php artisan test
```

Run Laravel Pint to verify clean PSR-12 code style:
```bash
vendor/bin/pint --test
```

---

## 🌐 External APIs Used
- **Frankfurter Currency API:** `https://api.frankfurter.dev` (Free open source exchange rates published by the European Central Bank; no API key required). Caching is configured for 3,600 seconds with automated fallback handling.
