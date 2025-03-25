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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('receipt_id')->nullable()->constrained('receipt_records')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'total_mrc_customer',
                'total_new_customer',
                'additional_description',
                'additional_fees',
                'sub_total',
                'tax_amount',
                'tax_percent'
            ]);
        });
    }
};
