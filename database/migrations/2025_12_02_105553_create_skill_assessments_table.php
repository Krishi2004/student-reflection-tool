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
        Schema::create('skill_assessments', function (Blueprint $table) {
            $table->id();
        $table->foreignId('reflection_id')->constrained('reflections')->onDelete('cascade');
        $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
        $table->decimal('self_score', 3, 2); 
        $table->decimal('verifier_score', 3, 2)->nullable();
        $table->boolean('is_verified')->default(false);
        $table->string('verification_token')->nullable(); 
        $table->string('verifier_email')->nullable();
        $table->string('verifier_name')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skill_assessments');
    }
};
