<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTerminationToServiceTypeInSubconChecklistsTable extends Migration
{
    public function up()
    {
        // If service_type is an enum, alter the column to add 'termination'
        // If it's a string, no migration needed
        // Assuming enum for this migration
        DB::statement("ALTER TABLE subcon_checklists MODIFY service_type ENUM('installation','maintenance','termination') NOT NULL;");
    }

    public function down()
    {
        DB::statement("ALTER TABLE subcon_checklists MODIFY service_type ENUM('installation','maintenance') NOT NULL;");
    }
}
