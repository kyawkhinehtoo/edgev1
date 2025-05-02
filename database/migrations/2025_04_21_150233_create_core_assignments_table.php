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
        Schema::create('core_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_id');
            $table->string('source_color')->nullable();
            $table->integer('source_port')->nullable();
            $table->unsignedBigInteger('dest_id');
            $table->string('dest_color')->nullable();
            $table->integer('dest_port')->nullable();
            $table->unsignedBigInteger('jc_id');
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('jc_id')->references('id')->on('jc_boxes')->onDelete('cascade'); // Assuming 'jc_boxes' is the table name
            $table->foreign('source_id')->references('id')->on('fiber_cables')->onDelete('cascade'); 
            $table->foreign('dest_id')->references('id')->on('fiber_cables')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_assignments');
    }
};
