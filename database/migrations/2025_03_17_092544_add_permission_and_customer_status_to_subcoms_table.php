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
        Schema::table('subcoms', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('disabled');
            $table->json('customer_status')->nullable()->after('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcoms', function (Blueprint $table) {
            //
        });
    }
};
