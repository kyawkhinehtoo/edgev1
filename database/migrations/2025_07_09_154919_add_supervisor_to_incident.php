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
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('supervisor_id')->nullable()->after('incharge_id');
            $table->unsignedBigInteger('assigned_by')->nullable()->after('supervisor_id');
            
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropForeign(['assigned_by']);
            $table->dropColumn(['supervisor_id', 'assigned_by']);
        });
    }
};
