<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Reflection;
use Illuminate\Http\Request;

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
    
}
