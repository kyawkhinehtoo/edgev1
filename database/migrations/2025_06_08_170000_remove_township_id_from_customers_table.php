<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
           // $table->dropForeign(['township_id']);
           $table->dropColumn('township_id');
            $table->dropColumn('location');
            $table->dropColumn('address');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('township_id')->nullable()->after('service_activation_date');
            $table->foreign('township_id')->references('id')->on('townships')->onDelete('set null');
        });
    }
};
