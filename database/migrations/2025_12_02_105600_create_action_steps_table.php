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
        Schema::create('action_steps', function (Blueprint $table) {
        $table->id();
        $table->foreignId('goal_id')->constrained('goals')->onDelete('cascade');
        $table->string('description', 500); 
        $table->boolean('is_reflection_step')->default(false); 
        $table->boolean('is_completed')->default(false);
        $table->foreignId('linked_reflection_id')->nullable()->constrained('reflections')->onDelete('set null');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_steps');
    }
};
