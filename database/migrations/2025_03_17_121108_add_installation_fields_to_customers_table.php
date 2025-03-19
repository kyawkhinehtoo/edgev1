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
         Schema::table('customers', function (Blueprint $table) {
             $table->string('installation_status')->nullable();
             $table->string('route_kmz_image')->nullable();
             $table->string('drum_no_txt')->nullable();
             $table->string('drum_no_image')->nullable();
             $table->string('start_meter_txt')->nullable();
             $table->string('start_meter_image')->nullable();
             $table->string('end_meter_txt')->nullable();
             $table->string('end_meter_image')->nullable();
             $table->dateTime('installation_reappointment_date')->nullable();
         });
     }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            //
        });
    }
};
