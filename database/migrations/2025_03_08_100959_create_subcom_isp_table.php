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
        Schema::create('subcom_isp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcom_id')->constrained('subcoms')->onDelete('cascade');
            $table->foreignId('isp_id')->constrained('isps')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcom_isp');
    }
};
