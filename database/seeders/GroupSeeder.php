<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assessment;
use App\Models\Group;
use App\Models\Attendance;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all assessments where type is 'teacher-assign'
        $assessments = Assessment::where('type', 'teacher-assign')->get();

        foreach ($assessments as $assessment) {
            // Get students who attended the assessment (Only retrieve user_id in array)
            $attendedStudents = Attendance::where('assessment_id', $assessment->id)
                ->where('attended', true)
                ->pluck('user_id')
                ->toArray();

            // Determine the number of students required in a group (num_reviews_required + 1)
            $groupSize = $assessment->num_reviews_required + 1;

            // Create groups for the attended students
            $this->createGroups($assessment, $attendedStudents, $groupSize);
        }
    }

    /**
     * Create groups and assign students to those groups.
     */
    private function createGroups($assessment, $students, $groupSize)
    {
        $totalStudents = count($students);

        // Shuffle students to randomize group allocation
        shuffle($students);

        // Calculate the number of full groups
        // https://www.w3schools.com/Php/func_math_intdiv.asp
        $numGroups = intdiv($totalStudents, $groupSize);

        // Handle leftover students
        $remainingStudents = $totalStudents % $groupSize;

        // Create full-size groups
        for ($i = 0; $i < $numGroups; $i++) {
            // Create group for assessment
            $group = Group::create([
                'assessment_id' => $assessment->id,
                'group_name' => "Group " . ($i + 1),
            ]);

            // Assign students to group
            // https://www.w3schools.com/php/func_array_slice.asp
            // array_slice(array, start_point, length)
            $studentsInGroup = array_slice($students, $i * $groupSize, $groupSize);
            $group->users()->attach($studentsInGroup);
        }

        // If there are leftover students, distribute them into the existing groups
        if ($remainingStudents > 0 && $numGroups > 0) {
            for ($i = 0; $i < $remainingStudents; $i++) {
                // Attach the remaining students to existing groups (distribute them evenly)
                $group = Group::where('assessment_id', $assessment->id)->skip($i % $numGroups)->first();
                $group->users()->attach($students[$numGroups * $groupSize + $i]);
            }
        }
    }
}

