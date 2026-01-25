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
        Schema::create('verification', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reflection_id')->constrained()->onDelete('cascade');
        $table->string('supervisor_email');
        $table->string('supervisor_name')->nullable();
        $table->string('token')->unique();
        $table->enum('status', ['Pending', 'Completed', 'Expired'])->default('Pending');
        $table->text('comments')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->timestamp('verified_at')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
