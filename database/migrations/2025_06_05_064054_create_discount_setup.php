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
        Schema::create('discount_setup', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('port_sharing_service_id')->constrained('port_sharing_services')->onDelete('cascade');
            $table->foreignId('isp_id')->constrained('isps')->onDelete('cascade');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->text('fix_rate')->nullable();
            $table->string('rate_type')->default('fixed'); // 'fixed' or 'percentage'
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_setup');
    }
};
