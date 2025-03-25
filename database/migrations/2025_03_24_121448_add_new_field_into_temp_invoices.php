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
        Schema::table('temp_invoices', function (Blueprint $table) {
            $table->integer('total_mrc_customer')->default(0)->after('total_installation_amount');
            $table->integer('total_new_customer')->default(0)->after('total_mrc_customer');
            $table->text('additional_description')->nullable()->after('total_new_customer');
            $table->decimal('additional_fees', 10, 2)->default(0)->after('additional_description');
            $table->decimal('sub_total', 10, 2)->default(0)->after('additional_fees');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('discount_amount');
            $table->decimal('tax_percent', 5, 2)->default(0)->after('tax_amount');
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
