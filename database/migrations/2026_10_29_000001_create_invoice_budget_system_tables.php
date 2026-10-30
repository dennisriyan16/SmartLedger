<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('gst_number')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('gst_number')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['product', 'service'])->default('product');
            $table->string('sku')->unique()->nullable();
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->enum('status', ['paid', 'pending', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('invoice_date');
            $table->index('due_date');
        });

        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('discount_total', 15, 2)->default(0.00);
            $table->decimal('tax_total', 15, 2)->default(0.00);
            $table->decimal('grand_total', 15, 2)->default(0.00);
            $table->decimal('paid_amount', 15, 2)->default(0.00);
            $table->enum('status', ['paid', 'pending', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('invoice_date');
            $table->index('due_date');
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('invoiceable');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->text('description');
            $table->decimal('quantity', 15, 2)->default(1.00);
            $table->decimal('unit_price', 15, 2)->default(0.00);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->decimal('tax_percent', 5, 2)->default(0.00);
            $table->decimal('line_total', 15, 2)->default(0.00);
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['monthly', 'yearly'])->default('monthly');
            $table->integer('year');
            $table->tinyInteger('month')->nullable();
            $table->string('category');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->timestamps();

            $table->index(['year', 'month', 'category']);
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->string('title');
            $table->string('category');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->date('expense_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('expense_date');
            $table->index('category');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->morphs('payable');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->date('payment_date');
            $table->string('method')->default('Cash'); // Cash, Bank Transfer, Cheque, UPI, Credit Card
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('purchase_invoices');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
    }
};
