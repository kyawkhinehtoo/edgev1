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
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('order_date')->change();
            $table->timestamp('installation_date')->nullable()->change();
            $table->timestamp('subcom_assign_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('order_date')->change();
            $table->date('installation_date')->nullable()->change();
            $table->date('subcom_assign_date')->nullable()->change();
        });
    }
};
