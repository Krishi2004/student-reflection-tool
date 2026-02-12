<?php

use App\Http\Controllers\ProfileController;
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

Route::get('/goals', function() {
    return view('goals');
})->middleware(['auth', 'verified'])->name('goals');

require __DIR__.'/auth.php';
