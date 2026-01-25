<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'narrative',
        'r_quality_score',
        'template_used',
    ];

    // Relationship to User
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship to Assessments
    public function skillAssessments()
    {
        return $this->hasMany(SkillAssessment::class);
    }
}