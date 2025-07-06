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
        Schema::table('maintenance_services', function (Blueprint $table) {
            $table->string('service_type')->default('ftth')->after('sla_hours')->comment('Type of service, e.g., ftth, dia, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenance_services', function (Blueprint $table) {
            //
        });
    }
};
