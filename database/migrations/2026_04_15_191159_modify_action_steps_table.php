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
        Schema::table('action_steps', function (Blueprint $table) {
            $table->dropForeign(['linked_reflection_id']);
            $table->dropColumn(['is_reflection_step', 'linked_reflection_id']);
            $table->integer('sequence_order')->default(0)->after('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_steps', function (Blueprint $table) {
            //
        });
    }
};
