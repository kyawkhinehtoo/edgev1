<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('payment_status', ['Pending', 'Paid', 'Partially Paid', 'Overdue'])->default('Pending');
            $table->enum('sms_status', ['Pending', 'Sent', 'Failed'])->default('Pending');
            $table->enum('email_status', ['Pending', 'Sent', 'Failed'])->default('Pending');
            $table->timestamp('sms_date')->nullable();
            $table->timestamp('email_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'sms_status',
                'email_status',
                'sms_date',
                'email_date'
            ]);
        });
    }
};