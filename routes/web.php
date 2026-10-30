<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Root redirect
Route::redirect('/', '/dashboard');

// Protected Admin Portal Routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Executive Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data Resources
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);

    // Transactional Invoices
    Route::resource('purchase-invoices', PurchaseInvoiceController::class);
    Route::resource('sales-invoices', SalesInvoiceController::class);

    // Payments
    Route::post('purchase-invoices/{purchase_invoice}/payments', [PaymentController::class, 'storePurchasePayment'])->name('purchase-invoices.payments.store');
    Route::post('sales-invoices/{sales_invoice}/payments', [PaymentController::class, 'storeSalesPayment'])->name('sales-invoices.payments.store');

    // Budgets & Expenses
    Route::resource('budgets', BudgetController::class);
    Route::resource('expenses', ExpenseController::class);

    // Reports & Analytics
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
});
