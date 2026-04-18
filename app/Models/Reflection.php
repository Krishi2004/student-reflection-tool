<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnArgument;

class Reflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'narrative',
        'verified_at',
        'r_quality_score',
        'template_used',
    ];

    protected $casts = [
        'narrative' => 'array',
        'verified_at' => 'datetime',
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

    public function user(){
        return $this->belongsTo(User::class);
    }
}