<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'period_covered',
                'bill_to',
                'attn',
                'previous_balance',
                'current_charge',
                'compensation',
                'otc',
                'sub_total',
                'payment_duedate',
                'service_description',
                'qty',
                'usage_days',
                'usage_day',
                'usage_month',
                'bonus_day',
                'bonus_month',
                'customer_status',
                'normal_cost',
                'type',
                'discount',
                'amount_in_word',
                'commercial_tax',
                'tax',
                'public_ip',
                'email',
                'phone',
                'sent_date',
                'mail_sent_status',
                'sms_sent_status',
                'invoice_file',
                'invoice_url',
                'bill_month',
                'bill_year',
                'reminder_sms_sent_status',
                'reminder_sms_sent_date',
                'popsite_id'
            ]);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            //
        });
    }
};
