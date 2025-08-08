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
            $table->string('fiber_cable_color')->nullable()->after('fiber_cable_id');
            $table->integer('fiber_cable_port')->nullable()->after('fiber_cable_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odb_fiber_cables', function (Blueprint $table) {
            $table->dropColumn(['fiber_cable_color', 'fiber_cable_port']);
        });
    }
};
