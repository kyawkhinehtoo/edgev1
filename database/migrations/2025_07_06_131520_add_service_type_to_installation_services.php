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
        Schema::table('installation_services', function (Blueprint $table) {
            //
            $table->string('service_type')->default('ftth')->after('type')->comment('Type of service, e.g., ftth, dia, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installation_services', function (Blueprint $table) {
            //
        });
    }
};
