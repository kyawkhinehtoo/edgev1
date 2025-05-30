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
        Schema::create('port_sharing_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('max_speed');
            $table->string('type'); // DIA, FTTH, etc.
            $table->text('rate');
            $table->enum('currency', ['baht', 'usd', 'mmk'])->default('mmk');
            $table->string('short_code');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('port_sharing_services');
    }
};
