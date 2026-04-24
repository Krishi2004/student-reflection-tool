<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) { // create these tables for the users table
            $table->id();
            $table->string("name");
            $table->string("email")->unique();
            $table->string("password");
            $table->rememberToken();
            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
