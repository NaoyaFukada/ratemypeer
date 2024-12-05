<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'reviewer_id',
        'reviewee_id',
        'review_text',
        'rating',
    ];

    // Assessment this review belongs to
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // The user who wrote the review
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // The user receiving the review
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
