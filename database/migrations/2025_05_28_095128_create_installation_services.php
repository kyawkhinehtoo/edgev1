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
        Schema::create('installation_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['new', 'relocation'])->default('new');
            $table->integer('sla_hours');
            $table->integer('fees');
            $table->string('short_code');
            $table->enum('currency', ['baht', 'usd', 'mmk'])->default('mmk');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('one_time_services');
    }
};
