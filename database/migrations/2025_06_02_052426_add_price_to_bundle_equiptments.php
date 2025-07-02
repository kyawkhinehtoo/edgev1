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
        Schema::table('bundle_equiptments', function (Blueprint $table) {
            
            $table->decimal('price', 8, 2)->default(0.00)->after('name')->comment('Price of the bundle equipment');
            $table->boolean('is_active')->default(true)->after('price')->comment('Indicates if the bundle equipment is active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bundle_equiptments', function (Blueprint $table) {
            //
        });
    }
};
