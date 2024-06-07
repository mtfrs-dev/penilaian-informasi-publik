<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\WorkUnitController;
use App\Http\Middleware\ProfileCompletedMiddleware;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Questions
Route::middleware(['auth',ProfileCompletedMiddleware::class])->controller(QuestionController::class)->as('question.')->prefix('pertanyaan')->group(function() {
    Route::get('/', 'index')->name('index');
});

// Questionnaire
Route::middleware(['auth',ProfileCompletedMiddleware::class])->controller(QuestionnaireController::class)->as('questionnaire.')->prefix('kuesioner')->group(function() {
    Route::get('/', 'index')->name('index');
});

// Work Units
Route::middleware(['auth',AdminMiddleware::class])->controller(WorkUnitController::class)->as('work-unit.')->prefix('unit-kerja')->group(function() {
    Route::get('/', 'index')->name('index');
});

// Users
Route::middleware(['auth',AdminMiddleware::class])->controller(UserController::class)->as('user.')->prefix('pengguna')->group(function() {
    Route::get('/', 'index')->name('index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile-unit', [ProfileController::class, 'updateWorkUnit'])->name('profile.updateWorkUnit');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
