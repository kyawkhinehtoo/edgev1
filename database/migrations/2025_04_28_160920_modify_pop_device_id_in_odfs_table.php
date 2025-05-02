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
        Schema::table('odfs', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['pop_device_id']);
            
            // Then drop the column
            $table->dropColumn('pop_device_id');
            
            // Add it back as JSON
            $table->json('pop_device_id')->nullable();
            
            // Add a generated column for indexing
            $table->json('pop_device_id_path')->storedAs(
                'JSON_EXTRACT(pop_device_id, "$")'
            )->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('odfs', function (Blueprint $table) {
            // Drop the JSON columns
            $table->dropColumn(['pop_device_id_path', 'pop_device_id']);
            
            // Recreate the original column with foreign key
            $table->unsignedBigInteger('pop_device_id')->nullable();
            $table->foreign('pop_device_id')->references('id')->on('pop_devices');
        });
    }
};
