<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('root_causes', function (Blueprint $table) {
            // Add new boolean columns
            $table->boolean('is_installation')->default(false)->after('status');
            $table->boolean('is_maintenance')->default(false)->after('is_installation');
        });
        
        // Migrate data from type to the new boolean columns
        DB::table('root_causes')->get()->each(function ($rootCause) {
            $updates = [];
            
            if ($rootCause->type === 'installation') {
                $updates['is_installation'] = true;
            } elseif ($rootCause->type === 'maintenance') {
                $updates['is_maintenance'] = true;
            }
            
            if (!empty($updates)) {
                DB::table('root_causes')
                    ->where('id', $rootCause->id)
                    ->update($updates);
            }
        });
        
        Schema::table('root_causes', function (Blueprint $table) {
            // Remove the old type column
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('root_causes', function (Blueprint $table) {
            // Add back the type column
            $table->string('type')->nullable()->after('status');
        });
        
        // Migrate data back from boolean columns to type
        DB::table('root_causes')->get()->each(function ($rootCause) {
            $type = null;
            
            if ($rootCause->is_installation) {
                $type = 'installation';
            } elseif ($rootCause->is_maintenance) {
                $type = 'maintenance';
            }
            
            if ($type) {
                DB::table('root_causes')
                    ->where('id', $rootCause->id)
                    ->update(['type' => $type]);
            }
        });
        
        Schema::table('root_causes', function (Blueprint $table) {
            // Remove the boolean columns
            $table->dropColumn(['is_installation', 'is_maintenance']);
        });
    }
};