<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Skill;
use App\Models\SkillAssessment; 
use App\Models\Reflection;
use Illuminate\Support\Facades\Auth;
use App\Models\Goal;

class GoalsController extends Controller
{
    public function create()
{
    
    $skills = Skill::all(); 

    $goals = Goal::where('user_id', Auth::id())
        ->with('skill') 
        ->orderBy('deadline', 'asc')
        ->get();


    return view('goals', compact('skills','goals')); 
}

public function store(Request $request)
    {
        // 1. VALIDATION
        $request->validate([
            'title' => 'required|string|max:255',
            'skill_id' => 'required|exists:skills,id',

        ]);
    

        $goal = Goal::create([
            'user_id' => Auth::id(),
            'skill_id' => $request->skill_id,
            'target_score' => $request->target_score,
            'status' => 'In Progress',
            'title' => $request->title,
            'deadline' => $request->deadline,
            'description' => $request->description,
        ]);


        return redirect()->route('goals')->with('success', 'Goal submitted successfully!');
    }

    public function update(Request $request, Goal $goal)
 {
    
    $goal->update([
        'title' => $request->title,
        'description' => $request->description,
        'deadline' => $request->deadline,
        'target_score' => $request->target_score,
        'status' => $request->status,
        
        

    ]);

    return redirect()->route('goals')->with('success');
}

public function edit(Goal $goal)
{

    $skills = Skill::all();

    return view('goals_edit', compact('skills', 'goal'));
}

    public function deleteGoal(Goal $goal) {
        if (auth()->id() !== $goal->user_id) {
            abort(403, 'unauthorised action');

        }
        $goal->delete();
        return redirect()->route('goals')->with('success', 'Goal deleted');
    }
}
