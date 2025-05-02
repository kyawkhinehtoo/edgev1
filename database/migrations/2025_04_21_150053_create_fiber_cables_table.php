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
        Schema::create('fiber_cables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('core_quantity');
            $table->enum('type', ['feeder', 'sub_feeder', 'destributed_route']);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiber_cables');
    }
};
