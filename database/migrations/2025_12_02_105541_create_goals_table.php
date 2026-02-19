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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            
            // 1. Link to User (CRITICAL: You need this!)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 2. Link to Skill
            $table->foreignId('skill_id')->constrained("skills")->onDelete('cascade');
            
            // 3. Goal Details
            $table->string('title'); // e.g. "Public Speaking Plan"
            $table->text('description')->nullable(); // e.g. "Practice twice a week"
            $table->date('deadline')->nullable(); // e.g. "2026-05-01"
            
            // 4. Score & Status
            // Decimal allows for scores like 4.5
            $table->decimal('target_score', 3, 1)->default(5.0); 
            
            // Your custom status list
            $table->enum('status', ['In Progress', 'Completed', 'Abandoned'])->default('In Progress');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
