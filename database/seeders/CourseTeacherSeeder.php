<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Course;

class CourseTeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all teachers and courses
        $teachers = User::where('role', 'teacher')->get();
        $courses = Course::all();

        // Assign each course to 1 or more teachers
        foreach ($courses as $course) {
            // Randomly select a number between 1 and 3
            $numberOfTeachers = rand(1, 3);
            // Assign a random teacher to the course
            $randomTeachers = $teachers->random($numberOfTeachers);
            foreach ($randomTeachers as $teacher) {
                // This will create user_id (from $teacher->id) and course_id
                $course->teachers()->attach($teacher->id);
            }
        }
    }
}
