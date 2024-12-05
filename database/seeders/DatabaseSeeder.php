<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        ini_set('memory_limit', '256M');

        // Call all the seeders
        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            CourseEnrollmentSeeder::class,
            CourseTeacherSeeder::class,
            AssessmentSeeder::class,
            AttendanceSeeder::class,
            GroupSeeder::class,
            ReviewsSeeder::class,
            AssessmentScoreSeeder::class
        ]);
    }
}
