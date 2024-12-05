<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// https://laravel.com/docs/11.x/hashing

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed 5 Teachers
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Teacher {$i}",
                'email' => "teacher{$i}@example.com",
                's_number' => "T100{$i}",
                'password' => Hash::make('password'), // Default password for testing
                'role' => 'teacher',
            ]);
        }

        // Seed 100 Students
        for ($i = 1; $i <= 100; $i++) {
            User::create([
                'name' => "Student {$i}",
                'email' => "student{$i}@example.com",
                's_number' => "S100{$i}",
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);
        }
    }
}
