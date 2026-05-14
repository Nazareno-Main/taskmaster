<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * DatabaseSeeder
 * Seeds the database with default categories and a demo student account.
 * Run with: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default subject categories with distinct UI colors
        $categories = [
            ['name' => 'Mathematics',  'color' => '#6366f1', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science',      'color' => '#10b981', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'English',      'color' => '#f59e0b', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'History',      'color' => '#ef4444', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Filipino',     'color' => '#ec4899', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Personal',     'color' => '#8b5cf6', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('categories')->insert($categories);

        // Seed a demo student account for testing
        $userId = DB::table('users')->insertGetId([
            'name'       => 'Juan dela Cruz',
            'email'      => 'demo@taskmaster.com',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed sample tasks for the demo user
        DB::table('tasks')->insert([
            [
                'user_id'     => $userId,
                'category_id' => 1,
                'title'       => 'Solve Chapter 3 Exercises',
                'description' => 'Pages 45-52, problems 1-20',
                'due_date'    => now()->addDays(2)->toDateString(),
                'priority'    => 'high',
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'user_id'     => $userId,
                'category_id' => 2,
                'title'       => 'Lab Report: Photosynthesis',
                'description' => 'Write conclusions for the experiment',
                'due_date'    => now()->addDays(5)->toDateString(),
                'priority'    => 'medium',
                'status'      => 'in_progress',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'user_id'     => $userId,
                'category_id' => 3,
                'title'       => 'Book Review: Noli Me Tangere',
                'description' => null,
                'due_date'    => now()->addDays(7)->toDateString(),
                'priority'    => 'low',
                'status'      => 'done',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
