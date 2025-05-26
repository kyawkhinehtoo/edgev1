<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('root_causes', function (Blueprint $table) {
            $table->enum('type', ['installation', 'maintenance'])->nullable()->after('status');
            $table->boolean('is_pending')->default(false)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('root_causes', function (Blueprint $table) {
            //
        });
    }
};
