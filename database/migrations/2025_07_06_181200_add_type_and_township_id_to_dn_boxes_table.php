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
        Schema::table('dn_boxes', function (Blueprint $table) {
            $table->enum('type', ['dn', 'cabinet'])->after('name')->nullable();
            $table->foreignId('township_id')->after('type')->nullable()->constrained('townships')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dn_boxes', function (Blueprint $table) {
            $table->dropForeign(['township_id']);
            $table->dropColumn(['type', 'township_id']);
        });
    }
};