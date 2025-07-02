<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->enum('type', ['new_installation', 'relocated'])->default('new_installation')->after('is_current');
            $table->date('installation_date')->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('customer_addresses', function (Blueprint $table) {
            $table->dropColumn(['type', 'installation_date']);
        });
    }
};
