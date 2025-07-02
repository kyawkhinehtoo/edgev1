<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('relocation_service_id')->nullable()->after('new_dn_splitter_id');
            $table->foreign('relocation_service_id')->references('id')->on('installation_services')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['relocation_service_id']);
            $table->dropColumn('relocation_service_id');
        });
    }
};
