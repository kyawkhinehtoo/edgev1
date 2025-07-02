<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('temp_invoices', function (Blueprint $table) {
            $table->integer('total_port_leasing_customer')->nullable()->after('updated_at');
            $table->integer('total_maintenance_customer')->nullable()->after('total_port_leasing_customer');
            $table->integer('total_suspension_customer')->nullable()->after('total_maintenance_customer');
            $table->integer('total_installation_customer')->nullable()->after('total_suspension_customer');
            $table->integer('total_relocation_customer')->nullable()->after('total_installation_customer');
            $table->integer('total_material_customer')->nullable()->after('total_relocation_customer');

            $table->decimal('total_port_leasing_amount', 10, 2)->nullable()->after('total_material_customer');
            $table->decimal('total_maintenance_amount', 10, 2)->nullable()->after('total_port_leasing_amount');
            $table->decimal('total_suspension_amount', 10, 2)->nullable()->after('total_maintenance_amount');
            $table->decimal('total_termination_amount', 10, 2)->nullable()->after('total_suspension_amount');
            $table->decimal('total_relocation_amount', 10, 2)->nullable()->after('total_termination_amount');
            $table->decimal('total_material_amount', 10, 2)->nullable()->after('total_relocation_amount');
        });
    }

    public function down(): void
    {
        Schema::table('temp_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'total_port_leasing_customer',
                'total_maintenance_customer',
                'total_suspension_customer',
                'total_installation_customer',
                'total_relocation_customer',
                'total_material_customer',
                'total_port_leasing_amount',
                'total_maintenance_amount',
                'total_suspension_amount',
                'total_termination_amount',
                'total_relocation_amount',
                'total_material_amount',
            ]);
        });
    }
};
