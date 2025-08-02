<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCollectedByFieldsToStringInCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['onu_collected_by']);
            $table->dropForeign(['drop_cable_collected_by']);
            // Change columns to string
            $table->string('onu_collected_by')->nullable()->change();
            $table->string('drop_cable_collected_by')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Change columns back to unsignedBigInteger
            $table->unsignedBigInteger('onu_collected_by')->nullable()->change();
            $table->unsignedBigInteger('drop_cable_collected_by')->nullable()->change();
            // Restore foreign keys
            $table->foreign('onu_collected_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('drop_cable_collected_by')->references('id')->on('users')->nullOnDelete();
        });
    }
}
