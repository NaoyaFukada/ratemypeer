<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;


Route::get('/', function () {
    $user = Auth::user();

    // Determine the courses to fetch based on user role
    if ($user->role === 'student') {
        // If the user is a student, fetch the courses they are enrolled in
        $courses = $user->courses; // student courses via `courses()`
    } elseif ($user->role === 'teacher') {
        // If the user is a teacher, fetch the courses they are teaching
        $courses = $user->taughtCourses; // teacher courses via `taughtCourses()`
    } else {
        $courses = collect(); // Return an empty collection if neither role matches
    }

    // Pass the courses and role to the view
    // compact() is shorthand for ->with() or [].
    return view('home', compact('courses', 'user'));
})->middleware('auth')->name('home');


Route::resource('courses', CourseController::class)->middleware('auth');
Route::post('/courses/{id}/add-students', [CourseController::class, 'addStudents'])->name('courses.addStudents');
Route::resource('assessments', AssessmentController::class)->middleware('auth');
Route::get('/assessments/create/{course_id}', [AssessmentController::class, 'create'])->name('assessments.create')->middleware('auth');
Route::resource('reviews', ReviewController::class)->middleware('auth');
Route::put('/reviews/{review}/rate', [ReviewController::class, 'rate'])->name('reviews.rate');
Route::get('/assessments/{assessment}/students/{student}/reviews', [StudentController::class, 'showReviews']) // showReviews is method in controller file
    ->name('student.reviews')
    ->middleware('auth');
Route::get('/students/top-reviewers', [StudentController::class, 'showTopReviewers'])
    ->name('students.top-reviewers')
    ->middleware('auth');
Route::post('/assessments/{assessment}/assign-score', [AssessmentController::class, 'assignScore'])->name('assessments.assign-score')->middleware('auth');
Route::get('/attendance/{assessment_id}', [AttendanceController::class, 'create'])->name('attendance.create')->middleware('auth');
Route::post('/attendance/{assessment_id}', [AttendanceController::class, 'store'])->name('attendance.store');


// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
