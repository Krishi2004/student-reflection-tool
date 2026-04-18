<?php

namespace App\Http\Controllers;

use \Illuminate\Support\Facades\DB;
use App\Models\Goal;
use App\Models\Reflection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Skill;
use SebastianBergmann\CodeUnit\FunctionUnit;

class AnalyticsController extends Controller
{
    public function index()
    {
        $userId = auth()->id(); // gets the ID of the user currently logged in
        $stats = [
            'total_goals' => Goal::where('user_id', $userId)->count(), // counts all the goals with the $userID
            'completed_goals' => Goal::where('user_id', $userId)->where('status', 'Completed')->count(), // counts all the goals that are completed
            'avg_target_score' => Goal::where('user_id', $userId)->avg('target_score'), // Calcualtes the avg target score all of the goals
            'total_reflection' => Reflection::where('user_id', $userId)->count(), // counts all the reflections with the $userID
        ];
        return view('analytics', compact('stats')); // sends the above to the analytic page
    }

    public function linechart()
    {
 
        $skills = Skill::all(); // gets all the skills available from the DB
        $reflections = Reflection::where('user_id', auth()->id()) // Gets all the reflection from the user 
            ->orderBy('created_at', 'asc')
            ->get();


        $chartData = [];
        foreach ($reflections as $reflection) { // loops through each reflection
            $assessment = DB::table('skill_assessments')
                ->where('reflection_id', $reflection->id) // looks for the scores that tie with the reflection
                ->first();

            if ($assessment) {
                $skill = Skill::find($assessment->skill_id);
                if ($skill) {

                // Self Score
                    $chartData[$skill->name]['self'][] = [
                        'x' => \Carbon\Carbon::parse($reflection->created_at)->format('M d'), // Carbon converts timestamps to readable text
                        'y' => (float) $assessment->self_score
                    ];

                //Supervisor Score
                    $chartData[$skill->name]['verifier'][] = [
                        'x' => \Carbon\Carbon::parse($reflection->created_at)->format('M d'),
                        'y' =>(float) $assessment->verifier_score
                    ];
                }
            }
        }


        $stats = $this->calculateExecutiveStats($chartData, $reflections->count()); // Calculates the Top Skill and most practiced


        return view('analytics', [
            'chartData'          => $chartData,
            'skills'             => $skills,
            'totalReflections'   => $stats['totalReflections'],
            'mostPracticedSkill' => $stats['mostPracticedSkill'],
            'topSkill'           => $stats['topSkill']
        ]);
    }

 
    private function calculateExecutiveStats($chartData, $totalReflections)
    {
        $skillCounts = [];
        $skillAverages = [];

        // Loop through the chart data to calculate counts and averages
        foreach ($chartData as $skillName => $dataPoints) {
            $skillCounts[$skillName] = count($dataPoints);
            
            // Get all scores for this skill and find the average
            $scores = array_column($dataPoints, 'y');
            $skillAverages[$skillName] = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
        }

        // Most Practiced Skill count
        $mostPracticedSkill = 'No data yet';
        if (!empty($skillCounts)) {
            arsort($skillCounts); 
            $mostPracticedSkill = array_key_first($skillCounts); 
        }

        // Top Skill count 
        $topSkill = 'No data yet';
        if (!empty($skillAverages)) {
            arsort($skillAverages); 
            $topSkillName = array_key_first($skillAverages);
            $topSkill = $topSkillName . ' (' . number_format($skillAverages[$topSkillName], 1) . '/5)';
        }


        return [
            'totalReflections'   => $totalReflections,
            'mostPracticedSkill' => $mostPracticedSkill,
            'topSkill'           => $topSkill
        ];
    }


}
