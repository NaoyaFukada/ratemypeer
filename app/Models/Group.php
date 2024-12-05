<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'group_name',
    ];

    // Assessment that this group is related to
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    // Students in this group
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_assignments');
    }
}
