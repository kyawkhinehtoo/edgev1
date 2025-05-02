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
        Schema::create('odb_fiber_cables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odb_id')->constrained('odbs')->onDelete('cascade');
            $table->foreignId('pop_device_id')->constrained('pop_devices')->onDelete('cascade');
            $table->foreignId('fiber_cable_id')->constrained('fiber_cables')->onDelete('cascade');
            $table->integer('odb_port')->comment('Port number on the ODB');
            $table->integer('olt_port')->comment('Port number on the OLT');

            $table->string('olt_cable_label')->nullable();
        
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();

            // Add unique constraint to prevent duplicate connections
            $table->unique(['odb_id', 'odb_port']);
            $table->unique(['fiber_cable_id', 'odb_port']);
            $table->unique(['pop_device_id', 'olt_port']);
         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odb_fiber_cables');
    }
};