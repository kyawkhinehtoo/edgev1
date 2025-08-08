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
        // Drop foreign key constraint and dn_box_id column from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['dn_box_id']);
            $table->dropColumn('dn_box_id');
        });

        // Make sn_splitter_id nullable in sn_ports table
        Schema::table('sn_ports', function (Blueprint $table) {
            $table->unsignedBigInteger('sn_splitter_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back dn_box_id column to customers table with foreign key
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('dn_box_id')->nullable()->after('id');
            $table->foreign('dn_box_id')->references('id')->on('dn_boxes');
        });

        // Make sn_splitter_id not nullable in sn_ports table (restore original state)
        Schema::table('sn_ports', function (Blueprint $table) {
            $table->unsignedBigInteger('sn_splitter_id')->nullable(false)->change();
        });
    }
};
