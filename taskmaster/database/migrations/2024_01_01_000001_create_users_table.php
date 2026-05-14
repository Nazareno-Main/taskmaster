<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create users table
 * Stores registered student accounts.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the users table with authentication fields.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                          // Primary key (auto-increment)
            $table->string('name');                // Student's full name
            $table->string('email')->unique();     // Unique email for login
            $table->string('password');            // Hashed password
            $table->rememberToken();               // For "remember me" sessions
            $table->timestamps();                  // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
