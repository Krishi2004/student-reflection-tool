<?php

namespace App\Http\Controllers;

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
        $userId = auth()->id();
        $stats = [
            'total_goals' => Goal::where('user_id', $userId)->count(),
            'completed_goals' => Goal::where('user_id', $userId)->where('status', 'Completed')->count(),
            'avg_target_score' => Goal::where('user_id', $userId)->avg('target_score'),
            'total_reflection' => Reflection::where('user_id', $userId)->count(),
        ];
        return view('analytics', compact('stats'));
    }

    public function linechart()
    {
        // 1. Get standard data
        $skills = Skill::all();
        $reflections = Reflection::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        // 2. Build the Chart Data
        $chartData = [];
        foreach ($reflections as $reflection) {
            $assessment = \Illuminate\Support\Facades\DB::table('skill_assessments')
                ->where('reflection_id', $reflection->id)
                ->first() ?? \Illuminate\Support\Facades\DB::table('skill_assessment')
                ->where('reflection_id', $reflection->id)
                ->first();

            if ($assessment) {
                $skill = Skill::find($assessment->skill_id);
                if ($skill) {
                    $chartData[$skill->name][] = [
                        'x' => \Carbon\Carbon::parse($reflection->created_at)->format('M d'),
                        'y' => (float) $assessment->self_score
                    ];
                }
            }
        }

        // 3. Delegate the heavy math to our new custom helper method!
        $stats = $this->calculateExecutiveStats($chartData, $reflections->count());

        // 4. Send it to the view
        return view('analytics', [
            'chartData'          => $chartData,
            'skills'             => $skills,
            'totalReflections'   => $stats['totalReflections'],
            'mostPracticedSkill' => $stats['mostPracticedSkill'],
            'topSkill'           => $stats['topSkill']
        ]);
    }

    /**
     * Helper Method: Calculates stats for the Executive Summary Cards
     */
    private function calculateExecutiveStats($chartData, $totalReflections)
    {
        $skillCounts = [];
        $skillAverages = [];

        // Loop through the chart data to calculate counts and averages
        foreach ($chartData as $skillName => $dataPoints) {
            $skillCounts[$skillName] = count($dataPoints);
            
            // Get all scores for this skill and find the average
            $scores = array_column($dataPoints, 'y');
            $skillAverages[$skillName] = array_sum($scores) / count($scores);
        }

        // Math for "Most Practiced Skill"
        $mostPracticedSkill = 'No data yet';
        if (!empty($skillCounts)) {
            arsort($skillCounts); // Sorts highest to lowest
            $mostPracticedSkill = array_key_first($skillCounts); // Grabs the top one
        }

        // Math for "Top Skill"
        $topSkill = 'No data yet';
        if (!empty($skillAverages)) {
            arsort($skillAverages); // Sorts highest to lowest
            $topSkillName = array_key_first($skillAverages);
            $topSkill = $topSkillName . ' (' . number_format($skillAverages[$topSkillName], 1) . '/5)';
        }

        // Return a clean array back to the main analytics method
        return [
            'totalReflections'   => $totalReflections,
            'mostPracticedSkill' => $mostPracticedSkill,
            'topSkill'           => $topSkill
        ];
    }


}
