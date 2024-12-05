<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assessment;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display the attendance form for a specific assessment.
     */
    public function index()
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create($assessment_id)
    {
        // Get the authenticated user
        $user = Auth::user();
        
        // Fetch the assessment and the associated course
        $assessment = Assessment::findOrFail($assessment_id);
        $course = $assessment->course;

        // Ensure the authenticated user is a teacher of the course
        if ($user->role !== 'teacher' || !$course->teachers->contains($user)) {
            abort(403, 'Unauthorized action.');
        }

        // Fetch all students enrolled in the course
        $students = $course->students;

        // Fetch current attendance records for the assessment
        $attendanceRecords = Attendance::where('assessment_id', $assessment_id)->get();

        return view('attendances.create', compact('assessment', 'students', 'attendanceRecords'));
    }

    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request, $assessment_id)
    {
        // Fetch the assessment
        $assessment = Assessment::findOrFail($assessment_id);
        $course = $assessment->course;

        // Fetch students who attended the course (those marked as attended in the request)
        $attendingStudents = collect($request->attendance)->filter(function($attended) {
         return (bool) $attended;
        });

        // Check if type is 'teacher-assign' and handle group creation logic
        if ($assessment->type === 'teacher-assign') {
            // Fetch the actual User models for attending students
            $students = User::whereIn('id', $attendingStudents->keys())->get(); // Convert keys (user_ids) into a collection

            $totalStudents = $students->count();
            $groupSize = $assessment->num_reviews_required + 1; // Minimum group size (num_reviews_required + 1)

            // Check if there are enough students to create the groups
            if ($totalStudents < $groupSize) {
                // If not enough students, return an error and do not save attendance
                return redirect()->back()->withErrors([
                    'error' => 'Not enough students attended. A minimum of ' . $groupSize . ' students is required to create groups.'
                ]);
            }
        }

        // Save attendance data only if there are enough students
        foreach ($request->attendance as $student_id => $attended) {
            // Sync the attended value in the pivot table
            $assessment->attendance()->syncWithoutDetaching([
                $student_id => ['attended' => (bool) $attended],
            ]);
        }

        // Group creation logic after attendance has been successfully saved
        if ($assessment->type === 'teacher-assign') {
            // Delete any existing groups for this assessment to ensure they are recreated
            $assessment->groups()->delete();

            $shuffledStudents = $students->shuffle();
            $numGroups = floor($totalStudents / $groupSize);
            $remainingStudents = $totalStudents % $groupSize;

            $groupIndex = 0;
            // First distribute into full-size groups
            for ($i = 0; $i < $numGroups; $i++) {
                $group = $assessment->groups()->create([
                    'group_name' => 'Group ' . ($i + 1),
                ]);

                $studentsInGroup = $shuffledStudents->slice($groupIndex, $groupSize);
                foreach ($studentsInGroup as $student) {
                    $group->users()->attach($student->id);
                }
                $groupIndex += $groupSize;
            }

            // Distribute remaining students into the groups
            if ($remainingStudents > 0 && $numGroups > 0) {
                for ($i = 0; $i < $remainingStudents; $i++) {
                    $group = $assessment->groups()->skip($i % $numGroups)->first();
                    $group->users()->attach($shuffledStudents[$groupIndex]->id);
                    $groupIndex++;
                }
            }
        }

        // Redirect back with a success message
        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Attendance and groups updated successfully.');
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
