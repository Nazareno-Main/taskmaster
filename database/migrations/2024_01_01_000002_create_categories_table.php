<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create categories table
 * Stores task categories (e.g., Math, Science, Personal).
 * Related to tasks via a foreign key (one-to-many).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the categories table.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                          // Primary key
            $table->string('name');                // Category label (e.g. "Math")
            $table->string('color')->default('#6366f1'); // UI color for badge
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
