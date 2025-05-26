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
        Schema::create('subcon_checklist_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcon_checklist_id')->constrained('subcon_checklists')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->string('title'); 
            $table->text('attachment')->nullable(); // Store file URL/path as string
            $table->enum('status',['requested','approved','declined'])->default('requested');
            $table->foreignId('current_actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_status_changed_at')->nullable();
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
