<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewBandwidthAndMaintenanceServiceToIncidentsTable extends Migration
{
    public function up()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->integer('new_bandwidth')->nullable()->after('new_port_number');
            $table->unsignedBigInteger('new_maintenance_service_id')->nullable()->after('new_bandwidth');
            $table->foreign('new_maintenance_service_id')->references('id')->on('maintenance_services')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['new_maintenance_service_id']);
            $table->dropColumn(['new_bandwidth', 'new_maintenance_service_id']);
        });
    }
}
