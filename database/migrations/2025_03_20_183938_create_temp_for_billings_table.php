<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('temp_bills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bill_number')->unique();
            $table->date('billing_period'); // Instead of separate month & year columns
            $table->enum('status', ['Draft', 'Pending Review', 'Finalized', 'Cancelled'])->default('Draft');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000);
            $table->timestamps();
        });
        Schema::create('temp_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_bill_id')->constrained('temp_bills')->onDelete('cascade');
            $table->foreignId('isp_id')->constrained('isps')->onDelete('cascade'); // Assuming ISPs are stored in `isps` table
            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('total_mrc_amount', 10, 2)->default(0.00);
            $table->decimal('total_installation_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->timestamps();
        });
        Schema::create('temp_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_invoice_id')->constrained('temp_invoices')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->enum('type', ['FullRecurring', 'ProRatedRecurring', 'NewInstallation']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_for_billings');
    }
};
