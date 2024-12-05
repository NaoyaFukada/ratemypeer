<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class AssessmentScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all assessments with related students (To send with chunk for each ralated field)
        $assessments = Assessment::with('course.students')->get();

        foreach ($assessments as $assessment) {
            // Array to hold the bulk insert data
            $insertData = [];

            // Fetch the students in the course using ORM
            $students = $assessment->course->students;

            foreach ($students as $student) {
                // Use Eloquent to get the count of reviews for the student in this assessment
                $submittedReviewsCount = $student->writtenReviews()
                    ->where('assessment_id', $assessment->id)
                    ->count();

                // Check if the student has submitted the required number of reviews
                if ($submittedReviewsCount >= $assessment->num_reviews_required) {
                    // Assign a random score between 1 and the assessment's max_score
                    $score = rand(1, $assessment->max_score);

                    // Collect data for bulk insert
                    $insertData[] = [
                        'assessment_id' => $assessment->id,
                        'user_id' => $student->id,
                        'score' => $score,
                    ];
                }
            }

            // Perform a bulk insert for all students who have met the criteria
            if (!empty($insertData)) {
                // Use ORM's DB::table to insert all rows at once
                DB::table('assessment_scores')->insert($insertData);
            }
        }
    }
}




