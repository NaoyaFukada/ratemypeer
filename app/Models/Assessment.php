<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'instruction',
        'num_reviews_required',
        'max_score',
        'due_date',
        'type',
    ];

    // Convert 'datetime' type to Carbon object to use format() method in course detail page
    protected $casts = [
    'due_date' => 'datetime',
    ];

    // Course that this assessment belongs to
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Reviews for this assessment
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Students' scores for this assessment
    public function scores()
    {
        return $this->belongsToMany(User::class, 'assessment_scores')->withPivot('score');
    }

    // Attendance records for this assessment
    public function attendance()
    {
        return $this->belongsToMany(User::class, 'attendances')->withPivot('attended');
    }


    // Groups assigned for this assessment
    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
