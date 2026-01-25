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
        Schema::create('advising_rules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
        $table->decimal('trigger_score', 3, 2); 
        $table->string('trigger_keyword')->nullable(); 
        $table->text('advice_text'); 
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advising_rules');
    }
};
