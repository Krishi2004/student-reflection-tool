<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Skill; 
use App\Models\Reflection; 
use App\Models\SkillAssessment; 
class ReflectionController extends Controller
{
    /**
     * Show the form for creating a new reflection.
     */


public function create()
{
    // 1. Fetch the skills from the database
    $skills = Skill::all(); 

    // 2. Pass the 'skills' variable to the view
    // If your view is named 'reflection.blade.php', use 'reflection'
    // If it is in a folder 'reflections/create.blade.php', use 'reflections.create'
    return view('reflection', compact('skills')); 
}

    /**
     * Store a newly created reflection in storage.
     */
    public function store(Request $request)
    {
        // 1. VALIDATION
        $request->validate([
            'title' => 'required|string|max:255',
            'skill_id' => 'required|exists:skills,id',
            'self_score' => 'required|numeric|min:1|max:5',
            'supervisor_email' => 'required|email',
            // STAR Framework fields
            'situation' => 'required|string|min:20',
            'action' => 'required|string|min:20',
            'result' => 'required|string|min:20',
            'analysis' => 'required|string|min:20',
        ]);

        // 2. COMBINE STAR DATA INTO JSON
        $narrativeData = [
            'situation' => $request->situation,
            'action' => $request->action,
            'result' => $request->result,
            'analysis' => $request->analysis
        ];

        // 3. CALCULATE QUALITY SCORE (RQS)
        // Simple logic: 1 point for every 50 words, capped at 5.0
        $totalWords = str_word_count($request->situation . $request->action . $request->result . $request->analysis);
        $qualityScore = min(5.0, round($totalWords / 50, 2));

        // 4. SAVE REFLECTION (Main Entry)
        $reflection = Reflection::create([
            'student_id' => Auth::id(),
            'title' => $request->title,
            'narrative' => json_encode($narrativeData), // Save as JSON
            'r_quality_score' => $qualityScore,
            'template_used' => 'STAR'
        ]);

        // 5. SAVE ASSESSMENT (The Pivot/Score)
        SkillAssessment::create([
            'reflection_id' => $reflection->id,
            'skill_id' => $request->skill_id,
            'self_score' => $request->self_score,
            'verifier_email' => $request->supervisor_email,
            'is_verified' => false,
        ]);

        // 6. REDIRECT
        return redirect()->route('dashboard')->with('success', 'Reflection submitted successfully!');
    }
}