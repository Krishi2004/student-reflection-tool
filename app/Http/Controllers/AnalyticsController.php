<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Reflection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Skill; 

class AnalyticsController extends Controller
{
    public function index() {
        $userId = auth()->id();
        $stats = [
            'total_goals' => Goal::where('user_id', $userId)->count(),
            'completed_goals' => Goal::where('user_id', $userId)->where('status', 'Completed')->count(),
            'avg_target_score' => Goal::where('user_id', $userId)->avg('target_score'),
            'total_reflection' => Reflection::where('user_id', $userId)->count(),
        ];
        return view('analytics', compact('stats'));
    }

    public function lineChart() {


        $skills = Skill::all();

        // 2. Set up the empty array for the graph
        $chartData = [];

        // 3. Get all reflections for the logged-in user in chronological order
        $reflections = Reflection::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($reflections as $reflection) {
            
            // 4. BULLETPROOF SEARCH: Look directly into your skill_assessment table
            // We check both plural and singular table names just to be 100% safe!
            $assessment = \Illuminate\Support\Facades\DB::table('skill_assessments')
                ->where('reflection_id', $reflection->id)
                ->first();

            if (!$assessment) {
                $assessment = \Illuminate\Support\Facades\DB::table('skill_assessment')
                    ->where('reflection_id', $reflection->id)
                    ->first();
            }

            // 5. If we found the assessment, format it perfectly for Chart.js!
            if ($assessment) {
                $skill = Skill::find($assessment->skill_id);
                
                if ($skill) {
                    $skillName = $skill->name;
                    $date = \Carbon\Carbon::parse($reflection->created_at)->format('M d');

                    // Group the data by the exact Skill Name
                    $chartData[$skillName][] = [
                        'x' => $date,
                        'y' => (float) $assessment->self_score
                    ];
                }
            }
        }

        // 6. Send the perfectly formatted data to your page!
        return view('analytics', compact('chartData', 'skills'));
    
}
}
