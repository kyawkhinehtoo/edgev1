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
        Schema::create('sn_splitters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('sn_id');
            $table->unsignedBigInteger('fiber_id');
            $table->integer('core_number')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('fiber_id')->references('id')->on('fiber_cables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sn_splitters');
    }
};
