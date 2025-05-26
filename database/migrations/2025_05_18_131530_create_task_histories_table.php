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
        Schema::create('task_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('incident_id');
            $table->integer('assigned');
            $table->date('target');
            $table->string('status');
            $table->text('description');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('root_causes_id')->nullable();
            $table->unsignedBigInteger('sub_root_causes_id')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('incident_id')->references('id')->on('incidents')->onDelete('cascade');
            $table->foreign('root_causes_id')->references('id')->on('root_causes')->onDelete('set null');
            $table->foreign('sub_root_causes_id')->references('id')->on('sub_root_causes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_histories');
    }
};