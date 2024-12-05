<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Course;
use App\Models\Assessment;

class AssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $courses = Course::all();

        // Seed 5 Assessments for each course
        foreach ($courses as $course) {
            for ($i = 1; $i <= 5; $i++) {
                Assessment::create([
                    'course_id' => $course->id,
                    'title' => "Assessment {$i}",
                    'instruction' => "This is the instruction for Assessment {$i}.",
                    'num_reviews_required' => 3,
                    'max_score' => rand(1, 100),
                    'due_date' => now()->addDays($i * 5)->toDateString(),
                    'type' => ($i % 2 == 0) ? 'student-select' : 'teacher-assign',
                ]);
            }
        }
    }
}
