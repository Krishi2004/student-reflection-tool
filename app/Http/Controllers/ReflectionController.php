<?php

namespace App\Http\Controllers;

use App\Mail\ReflectionVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Skill;
use App\Models\Reflection;
use App\Models\SkillAssessment;
use League\CommonMark\Extension\Attributes\Node\Attributes;
use Mail;
use function PHPUnit\Framework\returnArgument;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ReflectionController extends Controller
{
    /**
     * Show the form for creating a new reflection.
     */
    public function deleteReflection(Reflection $reflection) // function to delete a reflection
    {
        if (auth()->id() !== $reflection->user_id) { // matches the logged in user 
            abort(403, 'unauthorised action');

        }
        $reflection->delete(); //Eloquent ORM sends a delete request to the DB
        return redirect()->route('reflection');
    }




    public function index() // Allows the user to view the reflection page
    {
        
        $skills = Skill::all(); // selects all the skills from the DB

        $reflections = Reflection::where('user_id', Auth::id()) // Selects all the reflections related to that user with the skill names
            ->with('skillAssessments.skill')
            ->latest() 
            ->get();


        return view('reflection', compact('skills', 'reflections')); // loads the reflection page and passes the skills and reflections variables
    }

    public function review($id) // loads the reflection for the supervisor to review
    {
        $reflection = Reflection::findOrFail($id); // makes sure it finds the id otherwise throws an error
        return view('supervisor-review', compact('reflection'));
    }

    public function approve(Request $request, $id) // function to approve and review the reflection
    {

        $request->validate([ // verifies that the supervisor has inputted a score
            'verifier_score' => 'required|numeric|min:1|max:5',
        ]);


        $assessment = SkillAssessment::where('reflection_id', $id)->firstOrFail(); // looks for that specific record in the skill assessment table


        if ($assessment->is_verified == true) { // does not allow the supervisor to use the same link twice
            abort(403, 'This link has expired. The reflection has already been verified.');
        }


        $assessment->update([ // updates the main record with the new data
            'verifier_score' => $request->verifier_score,
            'is_verified' => true
        ]);

        
        $reflection = Reflection::findOrFail($id);
        $reflection->update(['verified_at' => now()]);


        return "<h1>Verification Complete!</h1><p>Thank you, you can now close this tab.</p>";
    }



    public function edit(Reflection $reflection) // function used to edit an exisitng relfection
    {

        $skills = Skill::all(); // fetches all the skills from the DB

        $narrative = $reflection->narrative;



        if (is_string($narrative)) {
            $narrative = json_decode($narrative, true); // unpacks the JSON string
        }

        if (!is_array($narrative)) { // fallback if the data is missing 
            $narrative = [];
        }

        return view('reflection_edit', compact('skills', 'reflection', 'narrative'));
    }

    public function update(Request $request, Reflection $reflection) // allows the user to update their exisitng reflection
    {
        if (auth()->id() !== $reflection->user_id) { // checks if this is the correct user
            abort(403, 'unauthorised action');
        }

        // 1. VALIDATION
        $request->validate([ // validation for the STAR format and other fields
            'title' => 'required|string|max:255',
            'skill_id' => 'required|exists:skills,id',
            'self_score' => 'required|numeric|min:1|max:5',
            'supervisor_email' => 'required|email',
            'situation' => 'required|string|min:20',
            'task' => 'required|string|min:20',
            'action' => 'required|string|min:20',
            'result' => 'required|string|min:20',
            'analysis' => 'required|string|min:20',
            'action_plan' => 'sometimes|nullable|array',
        ]);

        
        $narrativeData = [ // puts the STAR data in an array
            'situation' => $request->situation,
            'task' => $request->task,
            'action' => $request->action,
            'result' => $request->result,
            'analysis' => $request->analysis
        ];


        if ((int) $request->self_score < 4) { // checks if the self score is less than 4
            $newActions = $request->input('action_plan'); // if the user has editing their reflection it stores the new action plan steps 
            if ($newActions !== null) {
                $narrativeData['action_plan'] = !empty($newActions) ? $newActions : null; // uses the exisiting data if the user has not touched it
            } else {
                $narrativeData['action_plan'] = $reflection->narrative['action_plan'] ?? null; // adds the action plan the narrative array
            }
        } else {
            $narrativeData['action_plan'] = null;
        }

        
        $text = $request->situation . " " . $request->task . " " . $request->action . " " . $request->result . " " . $request->analysis;
        $qualityScore = min(5.0, round(str_word_count($text) / 50, 2)); // calculates the quality score

        
        $reflection->update([ // updates the record with the new data
            'title' => $request->title,
            'narrative' => $narrativeData,
            'r_quality_score' => $qualityScore,
        ]);

        if ($assessment = $reflection->skillAssessments()->first()) { // updates the skillAssessment score
            $assessment->update([
                'skill_id' => $request->skill_id,
                'self_score' => $request->self_score,
                'verifier_email' => $request->supervisor_email,
            ]);
        }

        return redirect()->route('reflection');
    }

    public function store(Request $request) // function to create a new reflection
    {
        
        $request->validate([ // validation rules for the form
            'title' => 'required|string|max:255',
            'skill_id' => 'required|exists:skills,id',
            'self_score' => 'required|numeric|min:1|max:5',
            'supervisor_email' => 'required|email',
            'situation' => 'required|string|min:20',
            'task' => 'required|string|min:20',
            'action' => 'required|string|min:20',
            'result' => 'required|string|min:20',
            'analysis' => 'required|string|min:20',
            'action_plan' => [Rule::requiredIf($request->self_score < 4), 'nullable', 'array', 'min:1'], // only used if the self score is less than 4
            'action_plan.*' => ['nullable', 'string'],
        ]);


        $narrativeData = [ // stores the STAR form in an array
            'situation' => $request->situation,
            'task' => $request->task,
            'action' => $request->action,
            'result' => $request->result,
            'analysis' => $request->analysis
        ];




        $totalWords = str_word_count($request->situation . $request->action . $request->result . $request->analysis);
        $qualityScore = min(5.0, round($totalWords / 50, 2)); // calulates the quality score

        if ($request->self_score < 4) {
            $narrativeData['action_plan'] = array_filter($request->action_plan);
        }

        $verification_token = hash('sha256', Auth::id().now().Str::random(10)); // creates the verification token for the supervisor link

        $reflection = Reflection::create([ // creates the new reflection
            'user_id' => Auth::id(),
            'title' => $request->title,
            'narrative' => $narrativeData,
            'r_quality_score' => $qualityScore,
            'template_used' => 'STAR'
        ]);




        SkillAssessment::create([ // creates the new skill assessment record
            'reflection_id' => $reflection->id,
            'skill_id' => $request->skill_id,
            'self_score' => $request->self_score,
            'verifier_email' => $request->supervisor_email,
            'verification_token' => $verification_token,
        ]);

        Mail::to($request->supervisor_email)->send(new ReflectionVerification($reflection)); // send the email to mail drop


        return redirect()->route('dashboard');
    }
}