<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [ // all the columns in the Goal table
        'student_id',
        'skill_id',
        'target_score',
        'status',
        'title',
        'user_id',
        'description',
        'deadline',
    ];

    public static function getStatus() { // function to get the status of a goal
        return ['In Progress', 'Completed', 'Abandoned'];
    }

    public function student() //links the goal to the user who created it
    {
        return $this->belongsTo(User::class, 'student_id');
    }


    public function skill() // which skill belongs to that goal
    {
        return $this->belongsTo(Skill::class);
    }

    public function actionSteps() // one to many relationship (One goal can have many action steps)
    {
        return $this->hasMany(ActionStep::class);
    }
}