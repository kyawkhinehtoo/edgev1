<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryToSubconChecklistsGroupTable extends Migration
{
    public function up()
    {
        Schema::table('subcon_checklists_group', function (Blueprint $table) {
            $table->string('category')->default('installation')->after('description');
        });
    }

    public function down()
    {
        Schema::table('subcon_checklists_group', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
