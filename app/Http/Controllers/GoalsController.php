<?php

namespace App\Http\Controllers;

use App\Models\ActionStep;
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
            ->with([
                'skill',
                'actionSteps' => function ($query) {
                    $query->orderBy('sequence_order', 'asc');
                }
            ])
            ->orderBy('deadline', 'asc')
            ->get();


        return view('goals', compact('skills', 'goals'));
    }

    public function store(Request $request)
    {
        // 1. VALIDATION
        $request->validate([
            'title' => 'required|string|max:255',
            'skill_id' => 'required|exists:skills,id',
            'target_score' => 'required|numeric|min:1|max:5',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
            'steps' => 'nullable|array',
            'steps.*' => 'required_with:steps|string|max:255',

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

        if ($request->filled('steps')) {
            $sequence = 1;
            foreach ($request->steps as $stepdescription) {
                if (trim($stepdescription) !== '') {
                    $goal->actionSteps()->create([
                        'description' => $stepdescription,
                        'sequence_order' => $sequence,
                    ]);
                    $sequence++;
                }
            }
        }


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
        $goal->actionSteps()->delete();

        // Then, recreate them based on whatever is currently in the form
        if ($request->filled('steps')) {
            $sequence = 1;
            foreach ($request->steps as $stepDescription) {
                if (trim($stepDescription) !== '') {
                    $goal->actionSteps()->create([
                        'description' => $stepDescription,
                        'sequence_order' => $sequence,
                    ]);
                    $sequence++;
                }
            }
        }

        return redirect()->route('goals')->with('success');
    }

    public function edit(Goal $goal)
    {

        $skills = Skill::all();

        return view('goals_edit', compact('skills', 'goal'));
    }

    public function deleteGoal(Goal $goal)
    {
        if (auth()->id() !== $goal->user_id) {
            abort(403, 'unauthorised action');

        }
        $goal->delete();
        return redirect()->route('goals')->with('success', 'Goal deleted');
    }

    public function toggleStep(ActionStep $step)
    {
        // 1. Security: Make sure they own the goal this step belongs to!
        if (auth()->id() !== $step->goal->user_id) {
            abort(403, 'unauthorised action');
        }

        // 2. Flip the status
        $step->update([
            'is_completed' => !$step->is_completed
        ]);

        $goal = $step->goal;
        $completedsteps = $goal->actionSteps()->where('is_completed', true)->count();
        $totalsteps = $goal->actionsteps()->count();

        if ($totalsteps > 0 && $totalsteps === $completedsteps) {
            $goal->update(['status' => 'Completed']);

        }elseif ($goal->status === 'Completed' && $completedsteps < $totalsteps) {
            $goal->update(['status' => 'In Progress']);

        }

        // 3. Send them right back where they were
        return back();
    }
}
