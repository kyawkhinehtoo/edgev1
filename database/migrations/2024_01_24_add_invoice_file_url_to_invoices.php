<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_file')->nullable()->after('total_amount');
            $table->string('invoice_url')->nullable()->after('invoice_file');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['invoice_file', 'invoice_url']);
        });
    }
};