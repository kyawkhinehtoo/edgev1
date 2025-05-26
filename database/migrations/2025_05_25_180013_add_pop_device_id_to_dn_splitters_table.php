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
        Schema::table('dn_splitters', function (Blueprint $table) {
            $table->unsignedBigInteger('pop_device_id')->nullable();
            $table->foreign('pop_device_id')
                  ->references('id')
                  ->on('pop_devices')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dn_splitters', function (Blueprint $table) {
            //
        });
    }
};
