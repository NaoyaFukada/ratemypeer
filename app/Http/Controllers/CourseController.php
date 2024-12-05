<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;

class CourseController extends Controller
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
    public function create()
    {
        // Display the form for creating a new course
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate that a file is provided and has the correct extension
        $request->validate([
            'course_file' => 'required|file|mimes:txt',
        ]);

        // Get the file content
        $file = $request->file('course_file');
        $fileContent = file_get_contents($file->getPathname());

        // Split file content into lines
        $lines = explode("\n", $fileContent);
        $courseCode = '';
        $courseName = '';
        $teacherSNumbers = [];
        $studentSNumbers = [];
        $assessmentData = [];
        $missingUsers = ['teachers' => [], 'students' => []];

        // Loop through the lines and process based on section headers
        $currentSection = '';
        foreach ($lines as $line) {
            $line = trim($line);

            if ($line == 'COURSE_CODE:') {
                $currentSection = 'course_code';
            } elseif ($line == 'COURSE_NAME:') {
                $currentSection = 'course_name';
            } elseif ($line == 'TEACHERS:') {
                $currentSection = 'teachers';
            } elseif ($line == 'ASSESSMENTS:') {
                $currentSection = 'assessments';
            } elseif ($line == 'STUDENTS:') {
                $currentSection = 'students';
            } elseif (!empty($line)) {
                // Append the line to the appropriate section
                if ($currentSection == 'course_code') {
                    $courseCode = $line;
                } elseif ($currentSection == 'course_name') {
                    $courseName = $line;
                } elseif ($currentSection == 'teachers') {
                    $teacherSNumbers[] = $line; // Collect teacher s_numbers
                } elseif ($currentSection == 'assessments') {
                    // Expecting assessment data in the following format for each line:
                    // title|instruction|num_reviews_required|max_score|due_date|type
                    $assessmentFields = explode('|', $line);
                    if (count($assessmentFields) === 6) {
                        // (int) casts the value to an integer
                        $assessmentData[] = [
                            'title' => trim($assessmentFields[0]),
                            'instruction' => trim($assessmentFields[1]),
                            'num_reviews_required' => trim($assessmentFields[2]),
                            'max_score' => trim($assessmentFields[3]),
                            'due_date' => trim($assessmentFields[4]),
                            'type' => trim($assessmentFields[5]),
                        ];
                    } else {
                        return redirect()->back()->withErrors(['course_file' => 'Invalid assessment format.']);
                    }
                } elseif ($currentSection == 'students') {
                    $studentSNumbers[] = $line; // Collect student s_numbers
                }
            }
        }

        // Validate parsed data
        if (empty($courseCode) || empty($courseName) || empty($teacherSNumbers) || empty($assessmentData) || empty($studentSNumbers)) {
            return redirect()->back()->withErrors(['course_file' => 'Invalid file format. All sections must be filled.']);
        }

        // Check if a course with the same course_code already exists
        if (Course::where('course_code', $courseCode)->exists()) {
            return redirect()->back()->withErrors(['course_file' => 'A course with this code already exists.']);
        }

        // **Validate each assessment's fields**
        foreach ($assessmentData as $assessment) {
            if (!is_numeric($assessment['num_reviews_required']) || (int)$assessment['num_reviews_required'] <= 0) {
                return redirect()->back()->withErrors(['course_file' => 'Invalid number of reviews required for assessment: ' . $assessment['title']]);
            }

            if (!is_numeric($assessment['max_score']) || (int)$assessment['max_score'] <= 0 || (int)$assessment['max_score'] > 100) {
                return redirect()->back()->withErrors(['course_file' => 'Invalid max score for assessment: ' . $assessment['title']]);
            }

            // If the input date is valid and matches the Y-m-d format, a DateTime object is returned.
            $date = \DateTime::createFromFormat('Y-m-d', $assessment['due_date']);
            // If DataTime object was not successgully created, it would return false
            // Also, to prevent unuaul behaviout when converting to Data object, it's making sure if the value is the same
            if (!$date || $date->format('Y-m-d') !== $assessment['due_date']) {
                return redirect()->back()->withErrors(['course_file' => 'Invalid due date for assessment: ' . $assessment['title']]);
            }

            if (!in_array($assessment['type'], ['student-select', 'teacher-assign'])) {
                return redirect()->back()->withErrors(['course_file' => 'Invalid type for assessment: ' . $assessment['title']]);
            }
        }

        // Validate and check if at least 1 teacher and 1 student exist
        $existingTeachers = User::whereIn('s_number', $teacherSNumbers)
            ->where('role', 'teacher')
            ->pluck('s_number')
            ->toArray();

        if (empty($existingTeachers)) {
            return redirect()->back()->withErrors(['course_file' => 'No valid teachers found in the system. At least 1 teacher is required.']);
        }

        // Calculate the required number of students based on the maximum number of required reviews across all assessments
        $maxRequiredReviews = max(array_column($assessmentData, 'num_reviews_required')) + 1;

        // Get the existing students based on the s_numbers in the file
        $existingStudents = User::whereIn('s_number', $studentSNumbers)
                            ->where('role', 'student')
                            ->get();

        // Check if there are enough valid students to match the required number of reviews
        if ($existingStudents->count() < $maxRequiredReviews) {
            return redirect()->back()->withErrors(['course_file' => "At least {$maxRequiredReviews} valid students are required based on the number of required reviews in the assessments."]);
        }

        // Create the course
        $course = Course::create([
            'course_code' => $courseCode,
            'course_name' => $courseName,
        ]);

        // Add teachers to the course using s_number
        // Making sure if its role is teacher
        foreach ($teacherSNumbers as $s_number) {
            $teacher = User::where('s_number', trim($s_number))
                            ->where('role', 'teacher')
                            ->first();

            if ($teacher) {
                // If the teacher exists, attach them to the course via bridge entity (courseTeachers entity)
                $course->teachers()->attach($teacher->id);
            } else {
                $missingUsers['teachers'][] = $s_number;
            }
        }

        // Add assessments to the course
        // course_id would be automatically added
        foreach ($assessmentData as $assessment) {
            $course->assessments()->create([
                'title' => $assessment['title'],
                'instruction' => $assessment['instruction'],
                'num_reviews_required' => (int)$assessment['num_reviews_required'],
                'max_score' => (int)$assessment['max_score'],
                'due_date' => $assessment['due_date'],
                'type' => $assessment['type'],
            ]);
        }

        // Enroll students in the course using s_number
        foreach ($studentSNumbers as $s_number) {
            $student = User::where('s_number', trim($s_number))
                            ->where('role', 'student')
                            ->first();

            if ($student) {
                // If the student exists, attach them to the course via bridge entity (courseEnrollments entity)
                $course->students()->attach($student->id);
            } else {
                $missingUsers['students'][] = $s_number;
            }
        }

        if (!empty($missingUsers['teachers']) || !empty($missingUsers['students'])) {
            session()->flash('missingUsers', $missingUsers);
        }

        // Redirect back to the courses index with a success message
        return redirect()->route('courses.show', $course->id)->with('success', 'Course created successfully along with assessments, teachers, and students.');
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        $teachers = $course->teachers;
        $assessments = $course->assessments;

        // Get all students who are not part of the course
        $availableStudents = User::where('role', 'student')
            ->whereDoesntHave('courses', function ($query) use ($course) {
                $query->where('courses.id', $course->id);
            })
            ->get();

        return view('courses.show', compact('course', 'teachers', 'assessments', 'availableStudents'));
    }

    public function addStudents(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Validate that the input contains valid student IDs
        $request->validate([
            'students' => 'required|array',
            'students.*' => 'exists:users,id'
        ]);

        // Attach the selected students to the course
        $course->students()->attach($request->students);

        // Redirect back to the course page with a success message
        return redirect()->route('courses.show', $course->id)
            ->with('success', 'Selected students have been added to the course successfully.');
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
