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
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('isp_id')->nullable()->constrained('isps')->nullOnDelete();
            $table->longText('order_remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['isp_id']);
            $table->dropColumn(['partner_id', 'created_by', 'isp_id', 'order_remark']);
        });
    }
};
