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
        Schema::create('subcon_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('has_attachment')->default(false);
            $table->enum('service_type',['installation,maintenance']);
            $table->text('remarks')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcon_checklists');
    }
};
