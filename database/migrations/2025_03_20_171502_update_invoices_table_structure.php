<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Drop the invoices table if it exists
      //  Schema::dropIfExists('invoices');

        // Recreate the invoices table with the new structure
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade');
            $table->foreignId('isp_id')->constrained()->onDelete('cascade'); // Link to ISPs
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date');
            $table->integer('total_mrc_customer')->default(0); //  Snapshot of active subscribers at billing time
            $table->integer('total_new_customer')->default(0); // Snapshot of new installation subscribers at billing time
            $table->decimal('total_mrc_amount', 10, 2)->default(0.00); // Total Monthly Recurring Charges
            $table->decimal('total_new_amount', 10, 2)->default(0.00); // Total New Installation Fees
            $table->string('additional_description')->nullable(); // Description for Other Additional fees
            $table->decimal('additional_fees', 10, 2)->default(0.00); // Other Additional fees
            $table->decimal('discount_amount', 10, 2)->default(0.00); // Any applicable discount
            $table->decimal('sub_total', 10, 2)->default(0.00); // Final total
            $table->integer('tax_percent')->default(0); // Tax percentage
            $table->decimal('tax_amount', 10, 4)->default(0.00); // Tax Amount
            $table->decimal('total_amount', 10, 2)->default(0.00); // Final total
            $table->enum('status', ['Pending', 'Paid', 'Overdue'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
