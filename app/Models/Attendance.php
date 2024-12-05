<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'user_id',
        'attended',
    ];

    // Assessment this attendance is related to
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // User associated with this attendance record
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public $timestamps = false;
}
