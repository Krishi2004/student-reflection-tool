<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionStep extends Model // model is used to talk to the database
{
    use HasFactory;

    protected $fillable = [ // all the columns in the ActionStep table
        'goal_id',
        'description',
        'is_reflection_step',
        'is_completed',
        'linked_reflection_id',
    ];


    public function goal() //Many to one relationship
    {
        return $this->belongsTo(Goal::class);
    }


    public function linkedReflection() // Action step can be linked back to a specifc reflection
    {
        return $this->belongsTo(Reflection::class, 'linked_reflection_id');
    }
}