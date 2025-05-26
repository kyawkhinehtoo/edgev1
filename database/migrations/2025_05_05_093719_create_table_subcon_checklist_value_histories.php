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
        Schema::create('subcon_checklist_value_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_value_id')->constrained('subcon_checklist_values')->cascadeOnDelete();
            $table->string('title'); 
            $table->string('attachment')->nullable(); // Store file URL/path as string
            $table->enum('action',['requested','approved','declined'])->default('requested');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcon_checklist_values');
    }
};
