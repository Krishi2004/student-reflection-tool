<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'description',
        'is_reflection_step',
        'is_completed',
        'linked_reflection_id',
    ];

    // Relationship: Belongs to a Goal
    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    // Relationship: Completed by a Reflection
    public function linkedReflection()
    {
        return $this->belongsTo(Reflection::class, 'linked_reflection_id');
    }
}