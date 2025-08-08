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
        Schema::table('sn_ports', function (Blueprint $table) {
            $table->foreignId('dn_box_id')->nullable()->constrained('dn_boxes')->after('pop_id')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sn_ports', function (Blueprint $table) {
            $table->dropForeign(['dn_box_id']);
            $table->dropColumn('dn_box_id');
        });
    }
};
