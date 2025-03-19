<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('townships', function (Blueprint $table) {
            $table->foreignId('partner_id')->nullable()->constrained('partners')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('townships', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn('partner_id');
        });
    }
};