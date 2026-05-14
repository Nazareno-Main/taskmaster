<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create tasks table
 * Core table of the application. Each task belongs to a user and a category.
 * Demonstrates normalization, foreign keys, and referential integrity.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates tasks with FK references to users and categories.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();                          // Primary key (auto-increment)

            // Foreign key: links task to the student who created it
            $table->foreignId('user_id')
                  ->constrained('users')           // References users.id
                  ->onDelete('cascade');            // Delete tasks when user is deleted

            // Foreign key: links task to a subject/category
            $table->foreignId('category_id')
                  ->constrained('categories')      // References categories.id
                  ->onDelete('cascade');

            $table->string('title');               // Task title (e.g. "Solve Chapter 3")
            $table->text('description')->nullable(); // Optional notes
            $table->date('due_date');              // Deadline for the task
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');
            $table->timestamps();                  // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
