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
        Schema::table('bill_configuration', function (Blueprint $table) {
            $table->integer('max_suspension_month')->default(1);
            $table->integer('port_maintenance_fee')->default(2000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_configuration', function (Blueprint $table) {
            //
        });
    }
};
