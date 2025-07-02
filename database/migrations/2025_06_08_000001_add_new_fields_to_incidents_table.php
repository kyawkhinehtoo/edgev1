<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('new_sn_splitter_id')->nullable()->after('sub_root_cause_id');
            $table->string('new_partner_id')->nullable()->after('new_sn_splitter_id');
            $table->string('new_port_number')->nullable()->after('new_sn_splitter_id');
            $table->unsignedBigInteger('new_pop_device_id')->nullable()->after('new_port_number');
            $table->unsignedBigInteger('new_pop_id')->nullable()->after('new_pop_device_id');
            $table->unsignedBigInteger('new_dn_splitter_id')->nullable()->after('new_pop_id');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn([
                'new_sn_splitter_id',
                'new_port_number',
                'new_pop_device_id',
                'new_pop_id',
                'new_dn_splitter_id',
            ]);
        });
    }
};
