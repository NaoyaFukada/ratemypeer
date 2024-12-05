<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        's_number',
        'password',
        'role',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Define the courses a student is enrolled in
    public function courses()
    {
        // Using bridge entiry between User and Course as it is many to many realtionship (with course_enrollments)
        return $this->belongsToMany(Course::class, 'course_enrollments');
    }

    // Define the courses a teacher teaches
    public function taughtCourses()
    {
        return $this->belongsToMany(Course::class, 'course_teachers');
    }

    // Reviews written by the user
    public function writtenReviews()
    {
        // One user can have many reviews
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    // Reviews received by the user
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    // Group Assignments
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_assignments');
    }

    // Assessment scores
    public function assessmentScores()
    {
        // By default, pivot only returns assessment_id and user_id. Hence withPivot() allows me to get score. 
        return $this->belongsToMany(Assessment::class, 'assessment_scores')->withPivot('score');
    }

    // Attendance
    public function attendances()
    {
        return $this->belongsToMany(Assessment::class, 'attendances')->withPivot('attended');
    }
}
