<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    // This allows mass-assignment for these columns
    protected $fillable = [
        'name',
        'description',
    ];

    // Define relationship: A Skill has many Assessments
    public function assessments()
    {
        return $this->hasMany(SkillAssessment::class);
    }
    
    // Define relationship: A Skill has many Goals
    public function goals()
    {
        return $this->hasMany(Goal::class);
    }
}