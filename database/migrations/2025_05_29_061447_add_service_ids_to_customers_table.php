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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('installation_service_id')->nullable()->after('package_id')
                  ->constrained('installation_services')->nullOnDelete();
            $table->foreignId('port_sharing_service_id')->nullable()->after('installation_service_id')
                  ->constrained('port_sharing_services')->nullOnDelete();
            $table->foreignId('maintenance_service_id')->nullable()->after('port_sharing_service_id')
                  ->constrained('maintenance_services')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['installation_service_id']);
            $table->dropForeign(['port_sharing_service_id']);
            $table->dropForeign(['maintenance_service_id']);
            
            $table->dropColumn('installation_service_id');
            $table->dropColumn('port_sharing_service_id');
            $table->dropColumn('maintenance_service_id');
        });
    }
};