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
class ReflectionController extends Controller
{
    /**
     * Show the form for creating a new reflection.
     */
    public function deleteReflection(Reflection $reflection)
    {
        if (auth()->id() !== $reflection->user_id) {
            abort(403, 'unauthorised action');

        }
        $reflection->delete();
        return redirect()->route('reflection')->with('success', 'Reflection deleted');
    }




    public function create()
    {
        // 1. Fetch the skills from the database
        $skills = Skill::all();

        $reflections = Reflection::where('user_id', Auth::id())
            ->with('skillAssessments.skill')
            ->latest() // Short for orderBy('created_at', 'desc')
            ->get();


        return view('reflection', compact('skills', 'reflections'));
    }

    public function review($id)
    {
        $reflection = Reflection::findOrFail($id);
        return view('supervisor-review', compact('reflection'));
    }

    public function approve(Request $request, $id)
    {


        $request->validate(['verifier_score' => 'required|numeric|min:1|max:5',]);

        $reflection = Reflection::findOrFail($id);
        $reflection->update(['verified_at' => now()]);

        $assessment = SkillAssessment::where('reflection_id', $id)->first();
        if ($assessment) {
            $assessment->update(['verifier_score' => $request->verifier_score]);
        }
        return "<h1>Verification Complete!</h1><p>Thank you, you can now close this tab.</p>";
    }

    public function edit(Reflection $reflection)
    {

        $skills = Skill::all();

        $narrative = $reflection->narrative;



        if (is_string($narrative)) {
            $narrative = json_decode($narrative, true);
        }


        if (is_string($narrative)) {
            $narrative = json_decode($narrative, true);
        }


        if (!is_array($narrative)) {
            $narrative = [];
        }

        return view('reflection_edit', compact('skills', 'reflection', 'narrative'));
    }

    public function update(Request $request, Reflection $reflection)
    {
        
        // 1. VALIDATION
        $request->validate([
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

        // 2. PACKAGE BASE NARRATIVE
        $narrativeData = [
            'situation' => $request->situation,
            'task' => $request->task,
            'action' => $request->action,
            'result' => $request->result,
            'analysis' => $request->analysis
        ];

        // 3. PROTECT THE ACTION PLAN
// 3. CAPTURE CONDITIONAL FIELDS
        if ((int) $request->self_score < 4) {
            // Grab the new inputs from the form
            $newActions = $request->input('action_plan');

            if ($newActions !== null) {
                // The user submitted the form, so use the NEW data (even if empty)
                $cleaned = array_values(array_filter($newActions));
                $narrativeData['action_plan'] = !empty($cleaned) ? $cleaned : null;
            } else {
                // The field wasn't in the request at all (JS disabled it)
                // ONLY in this specific case do we keep the old data
                $narrativeData['action_plan'] = $reflection->narrative['action_plan'] ?? null;
            }
        } else {
            // If score is 4 or 5, we explicitly remove the action plan
            $narrativeData['action_plan'] = null;
        }

        // 4. QUALITY SCORE (Including Task in count now)
        $text = $request->situation . " " . $request->task . " " . $request->action . " " . $request->result . " " . $request->analysis;
        $qualityScore = min(5.0, round(str_word_count($text) / 50, 2));

        // 5. UPDATE
        $reflection->update([
            'title' => $request->title,
            'narrative' => $narrativeData,
            'r_quality_score' => $qualityScore,
        ]);

        if ($assessment = $reflection->skillAssessments()->first()) {
            $assessment->update([
                'skill_id' => $request->skill_id,
                'self_score' => $request->self_score,
                'verifier_email' => $request->supervisor_email,
            ]);
        }

        return redirect()->route('reflection')->with('success', 'Updated successfully!');
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
            'task' => 'required|string|min:20',
            'action' => 'required|string|min:20',
            'result' => 'required|string|min:20',
            'analysis' => 'required|string|min:20',

            'action_plan' => [Rule::requiredIf($request->self_score < 4), 'nullable', 'array', 'min:1'],

            'action_plan.*' => ['nullable', 'string'],
        ]);




        $narrativeData = [
            'situation' => $request->situation,
            'task' => $request->task,
            'action' => $request->action,
            'result' => $request->result,
            'analysis' => $request->analysis
        ];




        $totalWords = str_word_count($request->situation . $request->action . $request->result . $request->analysis);
        $qualityScore = min(5.0, round($totalWords / 50, 2));

        if ($request->self_score < 4) {
            $narrativeData['action_plan'] = array_filter($request->action_plan);
        }


        $reflection = Reflection::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'narrative' => $narrativeData,
            'r_quality_score' => $qualityScore,
            'template_used' => 'STAR'
        ]);




        SkillAssessment::create([
            'reflection_id' => $reflection->id,
            'skill_id' => $request->skill_id,
            'self_score' => $request->self_score,
            'verifier_email' => $request->supervisor_email,
            //'is_verified' => false,
        ]);

        Mail::to($request->supervisor_email)->send(new ReflectionVerification($reflection));


        return redirect()->route('dashboard')->with('success', 'Reflection submitted successfully!');
    }
}