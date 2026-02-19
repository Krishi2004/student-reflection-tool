<?php

use App\Http\Controllers\GoalsController;
use App\Http\Controllers\ProfileController;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReflectionController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/reflection', [ReflectionController::class, 'create'])->middleware(['auth', 'verified'])->name('reflection');
Route::middleware(['auth'])->group(function () {
    Route::post('/reflection', [ReflectionController::class, 'store'])->name('reflections.store');
    Route::delete('/reflection/{reflection}', [ReflectionController::class, 'deleteReflection'])->name('reflection.delete');
});


Route::get('/reflection_edit/{reflection}', [ReflectionController::class, 'edit'])->middleware(['auth', 'verified'])->name('reflection_edit');
Route::put('/reflection_edit/{reflection}', [ReflectionController::class, 'update'])->name('reflection.update');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/goals', [GoalsController::class, 'create'])->middleware(['auth', 'verified'])->name('goals');
Route::middleware(['auth'])->group(function() {
    Route::post('/goals', [GoalsController::class, 'store'])->name('goals.store');
});

Route::get('/goals_edit/{goal}', [GoalsController::class, 'edit'])->middleware(['auth', 'verified'])->name('goals.edit');
Route::put('/goals_edit/{goal}', [GoalsController::class, 'update'])->middleware(['auth', 'verified'])->name('goals.update');
Route::delete('/goal/{goal}', [GoalsController::class, 'deleteGoal'])->name('goal.delete');


Route::get('/analytics', function() {
    return view('analytics');
})->middleware(['auth', 'verified'])->name('analytics');


require __DIR__.'/auth.php';
