<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'skill_id',
        'target_score',
        'status',
    ];

    // Relationship: A Goal belongs to a Student
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship: A Goal belongs to a Skill
    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    // Relationship: A Goal has many Action Steps
    public function actionSteps()
    {
        return $this->hasMany(ActionStep::class);
    }
}