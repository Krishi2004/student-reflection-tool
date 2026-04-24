<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\returnArgument;

class Reflection extends Model
{
    use HasFactory;

    protected $fillable = [ // All the columns in the Relfection table
        'user_id',
        'title',
        'narrative',
        'verified_at',
        'r_quality_score',
        'template_used',
    ];

    protected $casts = [ // converts the JSON text to a PHP array
        'narrative' => 'array',
        'verified_at' => 'datetime',
    ];


    public function student() // links the relfection to the student who created it
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Relationship to Assessments
    public function skillAssessments() // one to many relationship
    {
        return $this->hasMany(SkillAssessment::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}