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
        Schema::table('subcoms', function (Blueprint $table) {
            //
            $table->integer('installation')->default(0);
            $table->integer('maintenance')->default(0);
            $table->string('type')->default('internal');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subcoms', function (Blueprint $table) {
            //
        });
    }
};
