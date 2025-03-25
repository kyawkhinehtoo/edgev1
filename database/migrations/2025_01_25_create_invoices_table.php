<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills');
            $table->foreignId('isp_id')->constrained('isps');
            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('total_mrc_amount', 10, 2)->default(0);
            $table->decimal('total_installation_amount', 10, 2)->default(0);
            $table->integer('total_mrc_customer')->default(0);
            $table->integer('total_new_customer')->default(0);
            $table->text('additional_description')->nullable();
            $table->decimal('additional_fees', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('sub_total', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('invoice_file')->nullable();
            $table->string('invoice_url')->nullable();
            $table->enum('payment_status', ['Pending', 'Paid', 'Partially Paid', 'Overdue'])->default('Pending');
            $table->enum('sms_status', ['Pending', 'Sent', 'Failed'])->default('Pending');
            $table->enum('email_status', ['Pending', 'Sent', 'Failed'])->default('Pending');
            $table->timestamp('sms_date')->nullable();
            $table->timestamp('email_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};