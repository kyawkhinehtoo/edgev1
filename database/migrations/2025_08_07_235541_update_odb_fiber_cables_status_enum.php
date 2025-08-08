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
        Schema::table('odb_fiber_cables', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'maintenance', 'plan'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odb_fiber_cables', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active')->change();
        });
    }
};
