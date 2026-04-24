<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    
    protected $fillable = [ // columns for the skill table
        'name',
        'description',
    ];

    
    public function assessments() // links a skill to every time a student has scored on it
    {
        return $this->hasMany(SkillAssessment::class);
    }
    
    
    public function goals() // links a skill to the students objectives
    {
        return $this->hasMany(Goal::class);
    }
}