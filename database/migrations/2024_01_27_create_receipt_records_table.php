<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('receipt_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_id')->constrained('isps');
            $table->foreignId('bill_id')->constrained('bills');
            $table->string('receipt_number');
            $table->string('status');
            $table->decimal('issue_amount', 10, 2)->default(0);
            $table->string('issue_currenty')->default('mmk');
            $table->string('receipt_person')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('collected_person')->nullable();
            $table->timestamp('receipt_date')->nullable();
            $table->text('remark')->nullable();
            $table->decimal('collected_amount', 10, 2)->default(0);
            $table->decimal('outstanding_amount', 10, 2)->default(0);
            $table->string('collected_currency')->default('mmk');
            $table->decimal('collected_exchangerate', 10, 2)->default(1);
            $table->string('receipt_file')->nullable();
            $table->string('receipt_url')->nullable();
            $table->string('transition')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('receipt_records');
    }
};