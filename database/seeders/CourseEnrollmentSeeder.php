<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Course;

class CourseEnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        // Enroll each student in random courses (3 courses per student)
        // https://laravel.com/docs/11.x/eloquent-relationships#updating-many-to-many-relationships
        foreach ($students as $student) {
            $randomCourses = $courses->random(3);
            foreach ($randomCourses as $course) {
                $student->courses()->attach($course->id);
            }
        }
    }
}
