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
        Schema::table('sn_ports', function (Blueprint $table) {
            $table->foreignId('pop_device_id')->nullable()->after('customer_id')->constrained('pop_devices');
            $table->foreignId('pop_id')->nullable()->after('pop_device_id')->constrained('pops');
            $table->foreignId('dn_splitter_id')->nullable()->after('pop_id')->constrained('dn_splitters');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sn_ports', function (Blueprint $table) {
            //
        });
    }
};
