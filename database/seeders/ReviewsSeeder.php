<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Assessment;
use Faker\Factory as Faker;

class ReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Process assessments in chunks to avoid loading everything into memory at once
        Assessment::chunk(50, function ($assessments) use ($faker) {
            foreach ($assessments as $assessment) {
                $students = $assessment->course->students()->pluck('id')->toArray(); // Fetch only student IDs

                $reviews = []; // Collect reviews for batch insert

                // Handle 'student-select' type
                if ($assessment->type === 'student-select') {
                    foreach ($students as $reviewerId) {
                        // Get random reviewees, excluding the reviewer
                        $reviewees = array_diff($students, [$reviewerId]);
                        shuffle($reviewees);
                        $reviewees = array_slice($reviewees, 0, $assessment->num_reviews_required);

                        foreach ($reviewees as $revieweeId) {
                            $reviews[] = [
                                'assessment_id' => $assessment->id,
                                'reviewer_id' => $reviewerId,
                                'reviewee_id' => $revieweeId,
                                'review_text' => $faker->sentence(40),
                                'rating' => rand(1, 5),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }
                }

                // Handle 'teacher-assign' type (group-based reviews)
                if ($assessment->type === 'teacher-assign') {
                    $groups = $assessment->groups()->with('users:id')->get();

                    foreach ($groups as $group) {
                        $groupStudents = $group->users->pluck('id')->toArray();

                        foreach ($groupStudents as $reviewerId) {
                            // Get random reviewees from the same group
                            $reviewees = array_diff($groupStudents, [$reviewerId]);
                            shuffle($reviewees);
                            $reviewees = array_slice($reviewees, 0, $assessment->num_reviews_required);

                            foreach ($reviewees as $revieweeId) {
                                $reviews[] = [
                                    'assessment_id' => $assessment->id,
                                    'reviewer_id' => $reviewerId,
                                    'reviewee_id' => $revieweeId,
                                    'review_text' => $faker->sentence(40),
                                    'rating' => rand(1, 5),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }
                }

                // Insert all reviews at once for this assessment
                Review::insert($reviews);
            }
        });
    }
}


