<?php

use App\Http\Controllers\ClasseController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CampusController;
use Illuminate\Support\Facades\Route;

Route::get('/api/classes', [ClasseController::class, 'index']);
Route::get('/api/students', [StudentController::class, 'index']);
Route::post('/api/students', [StudentController::class, 'store']);
Route::put('/api/students/{id}', [StudentController::class, 'update']);
Route::delete('/api/students/{id}', [StudentController::class, 'destroy']);
Route::get('/api/courses', [CourseController::class, 'index']);
Route::get('/api/programs', [ProgramController::class, 'index']);
Route::get('/api/curricula', [CurriculumController::class, 'index']);
Route::get('/api/campus', [CampusController::class, 'index']);

// SPA fallback: serve the Vue app for all other GET requests so client-side routing works
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
