<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // This is for massAssignment
    protected $fillable = [
        'course_code',
        'course_name',
    ];

    // Students enrolled in the course
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments');
    }

    // Teachers of the course
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'course_teachers');
    }

    // Assessments in the course
    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
