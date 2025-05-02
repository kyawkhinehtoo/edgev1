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
        Schema::table('sn_splitters', function (Blueprint $table) {
            $table->foreignId('fiber_id')->nullable()->change();
            $table->integer('core_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sn_splitters', function (Blueprint $table) {
            $table->foreignId('fiber_id')->nullable(false)->change();
            $table->integer('core_number')->nullable(false)->change();
        });
    }
};
