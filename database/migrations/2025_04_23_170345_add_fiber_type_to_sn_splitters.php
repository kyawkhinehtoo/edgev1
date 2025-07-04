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
            $table->enum('fiber_type', ['patch_chord', 'distributed_route'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sn_splitters', function (Blueprint $table) {
            //
        });
    }
};
