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
    public function index() // Selects all the goals related to the logged in user
    {

        $skills = Skill::all(); // pulls all the skills for the dropdown



        $goals = Goal::where('user_id', Auth::id())
            ->with([
                'skill',
                'actionSteps' => function ($query) {
                    $query->orderBy('sequence_order', 'asc'); // gets the action steps in the sequence order
                }
            ])
            ->orderBy('deadline', 'asc')
            ->get(); // selects all the goals that belong to the logged in user and displays their goals and action steps


        return view('goals', compact('skills', 'goals')); // packages the skills and goals to be viewed in the goals page
    }

    public function store(Request $request) // used to save a new goals and its action steps
    {
        
        $request->validate([
            'title' => 'required|string|max:255',
            'skill_id' => 'required|exists:skills,id',
            'target_score' => 'required|numeric|min:1|max:5',
            'deadline' => 'nullable|date',
            'description' => 'nullable|string',
            'steps' => 'nullable|array',
            'steps.*' => 'required_with:steps|string|max:255', //apply this to every item in the action_steps array

        ]);


        $goal = Goal::create([ // creating a new goal record
            'user_id' => Auth::id(),
            'skill_id' => $request->skill_id,
            'target_score' => $request->target_score,
            'status' => 'In Progress',
            'title' => $request->title,
            'deadline' => $request->deadline,
            'description' => $request->description,
        ]);

        if ($request->filled('steps')) { // checks if the goal record has action steps
            $sequence = 1;
            foreach ($request->steps as $stepdescription) {
                if (trim($stepdescription) !== '') {
                    $goal->actionSteps()->create([ // create a record in the action steps table
                        'description' => $stepdescription,
                        'sequence_order' => $sequence,
                    ]);
                    $sequence++;
                }
            }
        }


        return redirect()->route('goals');
    }

    public function update(Request $request, Goal $goal) // allows a user to edit their existing goals
    {

        $goal->update([ // updates the record with the new data
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'target_score' => $request->target_score,
            'status' => $request->status,
        ]);
        $goal->actionSteps()->delete();

        
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

        return redirect()->route('goals');
    }

    public function editView(Goal $goal) // allows the user to click on edit which takes them to the goals edit page
    {

        $skills = Skill::all();

        return view('goals_edit', compact('skills', 'goal'));
    }

    public function deleteGoal(Goal $goal) // function to allow the user to delete their goals
    {
        if (auth()->id() !== $goal->user_id) { // checks if the logged in user matches the goals owner id
            abort(403, 'unauthorised action');

        }
        $goal->delete(); // deletes the selected goal
        return redirect()->route('goals');
    }

    public function toggleStep(ActionStep $step) // Allows the user to tick off their steps 
    {
        
        if (auth()->id() !== $step->goal->user_id) { // checks if the logged in user matches the action steps owner
            abort(403, 'unauthorised action');
        }

        
        $step->update([ 
            'is_completed' => !$step->is_completed // whatever the status was it flips
        ]);

        $goal = $step->goal;
        $completedsteps = $goal->actionSteps()->where('is_completed', true)->count();
        $totalsteps = $goal->actionSteps()->count();

        if ($totalsteps > 0 && $totalsteps === $completedsteps) { // if all the steps are completed mark status as completed
            $goal->update(['status' => 'Completed']);

        }elseif ($goal->status === 'Completed' && $completedsteps < $totalsteps) { // if not keep it as in progress
            $goal->update(['status' => 'In Progress']);

        }

        
        return back();
    }
}
