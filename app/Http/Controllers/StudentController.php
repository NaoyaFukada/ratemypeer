<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Review;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function showReviews($assessmentId, $studentId)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Fetch the assessment
        $assessment = Assessment::findOrFail($assessmentId);
        $course = $assessment->course;

        // Ensure the user is a teacher of the course
        if ($user->role !== 'teacher' || !$course->teachers->contains($user)) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch the student
        $student = User::findOrFail($studentId);

        // Get the reviews submitted by the student for this assessment
        $submittedReviews = Review::where('assessment_id', $assessment->id)
            ->where('reviewer_id', $student->id)
            ->get();

        // Get the reviews received by the student for this assessment
        $receivedReviews = Review::where('assessment_id', $assessment->id)
            ->where('reviewee_id', $student->id)
            ->get();

        // Fetch the score for the student (if it exists)
        $score = $assessment->scores()->wherePivot('user_id', $student->id)->first()->pivot->score ?? null; // if no score is found, it would return null

        // Return the view with data
        return view('students.show', compact('assessment', 'student', 'submittedReviews', 'receivedReviews', 'score'));
    }

    public function showTopReviewers()
    {
        // Get all students with role 'student'
        $students = User::where('role', 'student')->get();

        // Initialize an empty collection to store students' average scores and ratings
        $rankings = collect();

        // Loop through each student to calculate their normalized assessment score and review rating
        foreach ($students as $student) {
            // Get the assessments where the student has scores
            $assessments = $student->assessmentScores()->get();
            $totalScore = 0;
            $totalMaxScore = 0;

            if ($assessments->isNotEmpty()) {
                // Loop through assessments to calculate the student's average normalized assessment score
                foreach ($assessments as $assessmentScore) {
                    $assessment = Assessment::find($assessmentScore->pivot->assessment_id);
                    if ($assessment) {
                        // Access the max_score from the related assessment
                        $totalMaxScore += $assessment->max_score;
                        // Access the score from the pivot table directly
                        $totalScore += $assessmentScore->pivot->score;
                    }
                }
            }

            // Normalize the assessment score (percentage)
            $normalizedScore = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : null;

            // Normalize the review rating (out of 5)
            $averageRating = $student->writtenReviews()->avg('rating');
            $normalizedRating = $averageRating !== null ? ($averageRating / 5) * 100 : null;
            // If the student has both normalized scores and ratings, add them to the rankings
            if ($normalizedScore !== null && $normalizedRating !== null) {
                $rankings->push([
                    'student' => $student,
                    'normalized_score' => $normalizedScore,
                    'normalized_rating' => $normalizedRating,
                    // Combine both normalized values equally (50% weight for each)
                    'ranking' => ($normalizedScore + $normalizedRating) / 2,
                ]);
            }
        }

        // Sort rankings by the combined ranking value in descending order
        $rankings = $rankings->sortByDesc('ranking')->values()->take(10); // Get top 10 reviewers


        // Return view with the top 10 reviewers
        return view('students.top_reviewers', compact('rankings'));
    }
}
