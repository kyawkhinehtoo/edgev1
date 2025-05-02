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
        Schema::create('sn_ports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sn_splitter_id');
            $table->integer('port_number');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('sn_splitter_id')->references('id')->on('sn_splitters')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sn_ports');
    }
};
