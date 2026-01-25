<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reflection_id',
        'skill_id',
        'self_score',
        'verifier_score',
        'is_verified',
        'verifier_email',
        'verification_token',
    ];

    public function reflection()
    {
        return $this->belongsTo(Reflection::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}