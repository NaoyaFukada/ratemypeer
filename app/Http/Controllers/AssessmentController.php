<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Review;
use App\Models\Group;
use App\Models\User;
use App\Models\Course;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(string $course_id)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        $course = Course::findOrFail($course_id);

        // Ensure the user is a teacher for this course
        if ($user->role !== 'teacher' || !$course->teachers->contains($user)) {
            // https://laravel.com/docs/7.x/errors
            abort(403, 'Unauthorized action.');
        }

        // Return the view for creating a new assessment
        return view('assessments.create', compact('course'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'title' => 'required|string|max:20',
            'instruction' => 'required|string',
            'num_reviews_required' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1|max:100',
            'due_date' => 'required|date',
            'type' => 'required|in:student-select,teacher-assign',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Create a new Assessment
        $assessment = Assessment::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'instruction' => $request->instruction,
            'num_reviews_required' => $request->num_reviews_required,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
            'type' => $request->type,
        ]);

        if ($request->type === 'teacher-assign') {
            // Fetch all students in the course
            $course = $assessment->course;
            $students = $course->students()->get();

            // // Set attendance for all students to 'false' for this assessment
            // foreach ($students as $student) {
            //     $assessment->attendance()->attach($student->id, ['attended' => false]);
            // }

            // Redirect to the attendance creation page
            return redirect()->route('attendance.create', ['assessment_id' => $assessment->id])
                ->with('success', 'Peer Review Assessment created successfully. Now, please take attendance.');
        }

        // Redirect back to the course page or a success page
        // Create session flash message to be displayed in courses.show.
        return redirect()->route('courses.show', $request->course_id)
            ->with('success', 'Peer Review Assessment created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $user = Auth::user();
        $assessment = Assessment::findOrFail($id); // Get the assessment
        $course = $assessment->course; // Get the course associated with the assessment

        // For students: display assessment details and allow review submission
        if ($user->role === 'student') {
            // Fetch the student's submitted reviews for this assessment
            $submittedReviews = Review::where('assessment_id', $assessment->id)
                ->where('reviewer_id', $user->id)
                ->get();

            // Check if the student has already submitted the required number of reviews
            $existingReviewsCount = $submittedReviews->count();
            if ($existingReviewsCount >= $assessment->num_reviews_required) {
                // Store a session flash message without redirecting
                session()->flash('info', 'You have already submitted the required number of reviews for this assessment.');
            }

            // Fetch reviews received by this student for this assessment
            $receivedReviews = Review::where('assessment_id', $assessment->id)
                ->where('reviewee_id', $user->id)
                ->get();

            // Default datatype in Eloquent
            $students = collect();

            if ($assessment->type === 'student-select') {
                // Fetch all students in the course except the user themselves and those they have already reviewed
                $students = $course->students()
                    ->where('users.id', '!=', $user->id) // Exclude the current user
                    ->whereDoesntHave('writtenReviews', function($query) use ($assessment, $user) {
                        $query->where('assessment_id', $assessment->id)
                            ->where('reviewer_id', $user->id);
                    })
                    ->get(); 
            } elseif ($assessment->type === 'teacher-assign') {
                // Fetch the group of students assigned to this user (for "teacher-assign" type)
                // whereHas('users', ...): This adds another condition that filters the groups based on a related model, users. (Function in Group model 'users')
                // Also group must include the current user.
                $group = Group::where('assessment_id', $assessment->id)
                ->whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->first();

                if ($group) {
                    // Get all group members except the user themselves
                    $students = $group->users()
                        ->where('users.id', '!=', $user->id) // Exclude the current user
                        ->get();
                }
            }

            // Calculate the ranking for the student
            $rankings = collect();
            $allStudents = User::where('role', 'student')->get();

            foreach ($allStudents as $student) {
                // Get the assessments where the student has scores
                $assessments = $student->assessmentScores()->get();
                $totalScore = 0;
                $totalMaxScore = 0;

                if ($assessments->isNotEmpty()) {
                    // Loop through assessments to calculate the student's average normalized assessment score
                    foreach ($assessments as $assessmentScore) {
                        $all_assessment = Assessment::find($assessmentScore->pivot->assessment_id);
                        if ($all_assessment) {
                            // Access the max_score from the related assessment
                            $totalMaxScore += $all_assessment->max_score;
                            // Access the score from the pivot table directly
                            $totalScore += $assessmentScore->pivot->score;
                        }
                    }
                }

                $normalizedScore = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : null;
                $averageRating = $student->writtenReviews()->avg('rating');
                $normalizedRating = $averageRating !== null ? ($averageRating / 5) * 100 : null;

                if ($normalizedScore !== null && $normalizedRating !== null) {
                    $rankings->push([
                        'student' => $student,
                        'normalized_score' => $normalizedScore,
                        'normalized_rating' => $normalizedRating,
                        'ranking' => ($normalizedScore + $normalizedRating) / 2,
                    ]);
                }
            }
            $rankings = $rankings->sortByDesc('ranking')->values(); 
            $totalStudents = $rankings->count();
            $studentRank = $rankings->search(function ($rank) use ($user) {
                return $rank['student']->id === $user->id;
            }) + 1;
            
            $percentile = $studentRank !== false ? (($studentRank) / $totalStudents) * 100 : null;

            return view('assessments.student', compact('assessment', 'submittedReviews', 'receivedReviews', 'students', 'course', 'studentRank', 'totalStudents', 'percentile'));

        } elseif ($user->role === 'teacher') {
            // For teachers: show students and their reviews and allow grading

            // Get all students in the course
            $studentsQuery = $course->students(); // Returns a query builder, allowing `where()` calls


            // Apply search filter if provided
            // https://stackoverflow.com/questions/70736335/laravel-search-and-filter
            // If HTTP request contains search field and it's value is not empty
            if ($request->has('search') && !empty($request->search)) {
                // Get search value
                $search = $request->search;
                $studentsQuery = $studentsQuery->where(function($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('s_number', 'like', '%' . $search . '%');
                });
            }

            // Paginate students (This is using query builder as it was neccesary to use it for search function)
            $students = $studentsQuery->paginate(10);

            // Fetch the number of reviews each student has submitted and received
            // Take $student as parameter and use $assessment variable
            $studentsWithReviewStats = $students->map(function ($student) use ($assessment) {
                return [
                    'student' => $student,
                    'submittedReviewsCount' => $student->writtenReviews()->where('assessment_id', $assessment->id)->count(),
                    'receivedReviewsCount' => $student->receivedReviews()->where('assessment_id', $assessment->id)->count(),
                    'score' => $assessment->scores()->where('user_id', $student->id)->first()->pivot->score ?? 'Not graded',
                ];
            });

            return view('assessments.teacher', compact('assessment', 'studentsWithReviewStats', 'course', 'students'));
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $assessment = Assessment::findOrFail($id);
        $course = $assessment->course;

        // Ensure the user is a teacher and a teacher for this course
        if ($user->role !== 'teacher' || !$course->teachers->contains($user)) {
            abort(403, 'Unauthorized action.');
        }

        // Check if there are any reviews submitted for this assessment
        if ($assessment->reviews->count() > 0) {
            return redirect()->route('courses.show', $course->id)
                ->withErrors('This assessment cannot be edited because submissions have already been made.');
        }

        return view('assessments.edit', compact('assessment', 'course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the assessment to be updated
        $assessment = Assessment::findOrFail($id);

        // Ensure the user is a teacher and has access to update the assessment
        $user = Auth::user();
        $course = $assessment->course;
        if ($user->role !== 'teacher' || !$course->teachers->contains($user)) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request
        $request->validate([
            'title' => 'required|string|max:20',
            'instruction' => 'required|string',
            'num_reviews_required' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1|max:100',
            'due_date' => 'required|date',
            'type' => 'required|in:student-select,teacher-assign',
        ]);

        // Update the assessment with the new data
        $assessment->update([
            'title' => $request->title,
            'instruction' => $request->instruction,
            'num_reviews_required' => $request->num_reviews_required,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
            'type' => $request->type,
        ]);

        if ($request->type === 'teacher-assign') {
            // Fetch all students in the course
            $course = $assessment->course;
            $students = $course->students()->get();

            // Redirect to the attendance creation page
            return redirect()->route('attendance.create', ['assessment_id' => $assessment->id])
                ->with('success', 'Peer Review Assessment updated successfully. Now, please take attendance.');
        } else {
            // If the type is not 'teacher-assign', delete all related groups and attendance records
            // 1. Delete all groups related to this assessment
            $assessment->groups()->delete();

            // 2. Delete all attendance records related to this assessment
            $assessment->attendance()->detach(); // Detach all relations in the pivot table for attendance
        }

        // Redirect back to the course page or assessment details page with success message
        return redirect()->route('courses.show', $assessment->course_id)
            ->with('success', 'Peer Review Assessment updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Assign a score to a student for an assessment.
     */
    public function assignScore(Request $request, $assessmentId)
    {
        // Fetch the authenticated user
        $user = Auth::user();

        // Fetch the assessment
        $assessment = Assessment::findOrFail($assessmentId);

        // Fetch the course related to this assessment
        $course = $assessment->course;

        // Ensure the authenticated user is a teacher of this course
        if ($user->role !== 'teacher' || !$course->teachers->contains($user)) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:1|max:' . $assessment->max_score,
        ]);

        // Fetch the student
        $student = User::findOrFail($request->student_id);

        // Check if the student has submitted the required number of reviews
        $submittedReviewsCount = Review::where('assessment_id', $assessment->id)
            ->where('reviewer_id', $student->id)
            ->count();

        if ($submittedReviewsCount < $assessment->num_reviews_required) {
            return redirect()->back()->withErrors('The student has not yet submitted the required number of reviews.');
        }

        // Assign the score to the student for this assessment
        // attach() will always add a new record to the pivot table, 
        // whereas syncWithoutDetaching() will only add a new record if one does not exist. (So it will not create duplicates)
        // If they found, they would update existing value in that row.
        $assessment->scores()->syncWithoutDetaching([
            $student->id => ['score' => $request->score]
        ]);

        // Redirect back to the assessment page with success message
        return redirect()->route('assessments.show', $assessment->id)
            ->with('success', 'Score assigned successfully.');
    }
}
