<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
           // $table->dropForeign(['township_id']);
            $table->dropColumn('route_kmz_image');
            $table->dropColumn('drum_no_txt');
            $table->dropColumn('drum_no_image');
            $table->dropColumn('start_meter_txt');
            $table->dropColumn('start_meter_image');
            $table->dropColumn('end_meter_txt');
            $table->dropColumn('end_meter_image');
            $table->dropColumn('vlan');
            $table->dropColumn('fc_used');
            $table->dropColumn('fc_damaged');
            $table->dropColumn('installation_timeline');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('township_id')->nullable()->after('service_activation_date');
            $table->foreign('township_id')->references('id')->on('townships')->onDelete('set null');
        });
    }
};
