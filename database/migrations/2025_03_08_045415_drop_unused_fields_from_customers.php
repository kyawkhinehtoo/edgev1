<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'nrc',
                'sale_channel',
                'sale_remark',
                'sale_person_id',
                'contract_term',
                'foc',
                'foc_period',
                'advance_payment',
                'advance_payment_day',
                'extra_bandwidth',
                'pppoe_account',
                'pppoe_password',
                'wlan_ssid',
                'wlan_password',
                'service_off_date',
                'promotion_package_plan',
                'refer_bonus',
                'rollback_to_original_package_plan_date',
                'social_account',
                'service_activation_date',
            ]);
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nrc')->nullable();
            $table->string('sale_channel')->nullable();
            $table->string('sale_remark')->nullable();
            $table->unsignedBigInteger('sale_person_id')->nullable();
            $table->string('contract_term')->nullable();
            $table->boolean('foc')->default(false);
            $table->integer('foc_period')->nullable();
            $table->boolean('advance_payment')->default(false);
            $table->integer('advance_payment_day')->nullable();
            $table->boolean('extra_bandwidth')->default(false);
            $table->string('pppoe_account')->nullable();
            $table->string('pppoe_password')->nullable();
     
            $table->string('wlan_ssid')->nullable();
            $table->string('wlan_password')->nullable();
            $table->timestamp('service_off_date')->nullable();
            $table->string('promotion_package_plan')->nullable();
            $table->boolean('refer_bonus')->default(false);
            $table->timestamp('rollback_to_original_package_plan_date')->nullable();
            $table->string('social_account')->nullable();
            $table->timestamp('service_activation_date')->nullable();
        });
    }
};