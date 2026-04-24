<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\GoalsController;
use App\Http\Controllers\ProfileController;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReflectionController;

Route::get('/', function () { // the default page 
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () { // the main student dashboard page
    $recentReflections = auth()->user()->reflections() // find the lastest 4 reflections that belong to the logged in user
        ->with('skillAssessments.skill')
        ->latest()
        ->take(4)
        ->get();
    return view('dashboard', compact('recentReflections'));
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/reflection', [ReflectionController::class, 'index'])->middleware(['auth', 'verified'])->name('reflection'); // view the reflection page
Route::middleware(['auth'])->group(function () { // makes sure they are logged in
    Route::post('/reflection', [ReflectionController::class, 'store'])->name('reflections.store'); // allows the user to create a new reflection
    Route::delete('/reflection/{reflection}', [ReflectionController::class, 'deleteReflection'])->name('reflection.delete'); // allows the user to delete a reflection
});


Route::get('/reflection_edit/{reflection}', [ReflectionController::class, 'edit'])->middleware(['auth', 'verified'])->name('reflection_edit'); // gets the reflection_edit page with the selected reflection
Route::put('/reflection_edit/{reflection}', [ReflectionController::class, 'update'])->name('reflection.update'); // allows the user to submit the form if they need to edit an exisitng reflection

Route::get('/goals', [GoalsController::class, 'index'])->middleware(['auth', 'verified'])->name('goals'); // gets the goals page with all the goals related to the logged in user
Route::middleware(['auth'])->group(function () { 
    Route::post('/goals', [GoalsController::class, 'store'])->name('goals.store'); // allows the logged in user to add and submit a new goal
});

Route::get('/goals_edit/{goal}', [GoalsController::class, 'editView'])->middleware(['auth', 'verified'])->name('goals.edit'); // allows the logged in user to view the edit screen for the goal
Route::put('/goals_edit/{goal}', [GoalsController::class, 'update'])->middleware(['auth', 'verified'])->name('goals.update'); //allows the user to submit the changes to their goal
Route::delete('/goal/{goal}', [GoalsController::class, 'deleteGoal'])->name('goal.delete'); // allows the user to delete a goal


Route::get('/analytics', [AnalyticsController::class, 'lineChart'])->name('analytics');

Route::get('/verify-reflection/{id}', [ReflectionController::class, 'review'])->name('reflection.review'); // allows the supervisor to click on the email link which directs them to view the form

Route::post('/verify-reflection/{id}', [ReflectionController::class, 'approve'])->name('reflection.approve'); // allows the supervisor to click approve which updates the DB

Route::patch('/steps/{step}/toggle', [GoalsController::class, 'toggleStep'])->name('steps.toggle'); // switches the status of the goal


require __DIR__ . '/auth.php';
