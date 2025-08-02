<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallationAndCollectionFieldsToCustomersTable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('customer_installation_type', ['new_installation', 'relocation'])->default('new_installation')->after('installation_status');
            $table->unsignedBigInteger('old_customer_id')->nullable()->after('customer_installation_type');
            $table->enum('onu_collection_status', ['no_action', 'collected', 'reused'])->default('no_action')->after('old_customer_id');
            $table->enum('onu_condition', ['good', 'damaged'])->nullable()->after('onu_collection_status');
            $table->date('onu_collection_date')->nullable()->after('onu_condition');
            $table->unsignedBigInteger('onu_collected_by')->nullable()->after('onu_collection_date');
            $table->enum('drop_cable_collection_status', ['no_action', 'collected', 'reused'])->default('no_action')->after('onu_collected_by');
            $table->enum('drop_cable_condition', ['good', 'damaged'])->nullable()->after('drop_cable_collection_status');
            $table->date('drop_cable_collection_date')->nullable()->after('drop_cable_condition');
            $table->unsignedBigInteger('drop_cable_collected_by')->nullable()->after('drop_cable_collection_date');

            $table->foreign('old_customer_id')->references('id')->on('customers')->nullOnDelete();
            $table->foreign('onu_collected_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('drop_cable_collected_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['old_customer_id']);
            $table->dropForeign(['onu_collected_by']);
            $table->dropForeign(['drop_cable_collected_by']);
            $table->dropColumn([
                'customer_installation_type',
                'old_customer_id',
                'onu_collection_status',
                'onu_condition',
                'onu_collection_date',
                'onu_collected_by',
                'drop_cable_collection_status',
                'drop_cable_condition',
                'drop_cable_collection_date',
                'drop_cable_collected_by',
            ]);
        });
    }
}
