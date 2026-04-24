<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained("skills")->onDelete('cascade'); // ensures every goal is linked to a skill
            $table->string('title'); 
            $table->text('description')->nullable(); 
            $table->date('deadline')->nullable(); 
            $table->decimal('target_score', 3, 1)->default(5.0); 
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
