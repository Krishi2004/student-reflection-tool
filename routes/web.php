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


Route::get('/reflection', [ReflectionController::class, 'create'])->middleware(['auth', 'verified'])->name('reflections.create');
Route::middleware(['auth'])->group(function () {
    Route::get('/reflections/create', [ReflectionController::class, 'create'])->name('reflections.create');
    Route::post('/reflection', [ReflectionController::class, 'store'])->name('reflections.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
