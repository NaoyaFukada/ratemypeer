<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Attendance;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get assessments that are 'teacher-assign' type
        Assessment::where('type', 'teacher-assign')
            ->chunk(100, function ($assessments) { // Process assessments in chunks to avoid memory exhaustion
                foreach ($assessments as $assessment) {
                    // Get all students in the course
                    $students = $assessment->course->students()->get();

                    // Prepare attendance data
                    $attendanceData = [];
                    foreach ($students as $student) {
                        $attendanceData[] = [
                            'assessment_id' => $assessment->id,
                            'user_id' => $student->id,
                            'attended' => (bool)random_int(0, 1),  // Random true/false for attendance
                        ];
                    }

                    // Insert attendance data in bulk (This is to optimize the process)
                    Attendance::insert($attendanceData);
                }
            });
    }
}


