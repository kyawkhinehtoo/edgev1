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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('sub_total', 10, 2)->default(0.00)->after('discount_amount');
            $table->integer('tax_percent')->default(0)->after('discount_amount');
            $table->decimal('tax_amount', 10, 4)->default(0.00)->after('discount_amount');
        });
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['FullRecurring', 'ProRatedRecurring', 'NewInstallation'])->notNull();
            $table->date('start_date')->nullable(); // For prorated calculations
            $table->date('end_date')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->text('description'); // Assuming you want to store a description for the amount
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
