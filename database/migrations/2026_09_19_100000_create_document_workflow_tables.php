<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('quotations')) {
            Schema::create('quotations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
                $table->string('quotation_number')->unique();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->date('date');
                $table->date('valid_until')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->string('discount_type')->default('none'); // none, fixed, percent
                $table->decimal('discount_value', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('status')->default('draft'); // draft, sent, accepted, rejected, expired, converted
                $table->text('notes')->nullable();
                $table->text('terms')->nullable();
                $table->foreignId('salesperson_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedBigInteger('converted_proforma_id')->nullable();
                $table->unsignedBigInteger('converted_invoice_id')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description')->nullable();
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
                $table->string('invoice_number')->unique();
                $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
                $table->unsignedBigInteger('quotation_id')->nullable();
                $table->unsignedBigInteger('proforma_invoice_id')->nullable();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->date('invoice_date');
                $table->date('due_date')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->string('discount_type')->default('none');
                $table->decimal('discount_value', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('balance_due', 12, 2)->default(0);
                $table->string('payment_status')->default('unpaid'); // unpaid, partially_paid, paid, overdue, cancelled
                $table->string('payment_method')->nullable();
                $table->text('notes')->nullable();
                $table->text('terms')->nullable();
                $table->foreignId('salesperson_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description')->nullable();
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('invoice_payments')) {
            Schema::create('invoice_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
                $table->string('payment_method'); // cash, m-pesa, airtel_money, mixx, halopesa, bank, card, credit
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('reference')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('proforma_invoices')) {
            Schema::create('proforma_invoices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('shop_id')->nullable()->constrained('shops')->nullOnDelete();
                $table->string('proforma_number')->unique();
                $table->unsignedBigInteger('quotation_id')->nullable();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->date('date');
                $table->date('valid_until')->nullable();
                $table->date('due_date')->nullable();
                $table->string('payment_terms')->nullable();
                $table->text('bank_details')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->string('discount_type')->default('none');
                $table->decimal('discount_value', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->decimal('balance_due', 12, 2)->default(0);
                $table->string('status')->default('draft'); // draft, sent, accepted, partially_paid, paid, converted, cancelled, expired
                $table->text('notes')->nullable();
                $table->text('terms')->nullable();
                $table->foreignId('salesperson_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedBigInteger('converted_invoice_id')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('proforma_invoice_items')) {
            Schema::create('proforma_invoice_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proforma_invoice_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description')->nullable();
                $table->integer('quantity');
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('discount', 12, 2)->default(0);
                $table->decimal('total', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('proforma_invoice_payments')) {
            Schema::create('proforma_invoice_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proforma_invoice_id')->constrained()->cascadeOnDelete();
                $table->string('payment_method');
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('reference')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_invoice_payments');
        Schema::dropIfExists('proforma_invoice_items');
        Schema::dropIfExists('proforma_invoices');
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};