<?php

use App\Http\Controllers\ClasseController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CampusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/classes', [ClasseController::class, 'index']);
Route::get('/students', [StudentController::class, 'index']);
Route::post('/students', [StudentController::class, 'store']);
Route::put('/students/{id}', [StudentController::class, 'update']);
Route::delete('/students/{id}', [StudentController::class, 'destroy']);
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/programs', [ProgramController::class, 'index']);
Route::get('/curricula', [CurriculumController::class, 'index']);
Route::get('/campus', [CampusController::class, 'index']);
Route::post('/campus', [CampusController::class, 'store']);
Route::put('/campus/{id}', [CampusController::class, 'update']);
Route::delete('/campus/{id}', [CampusController::class, 'destroy']);
Route::post('/campus/bulk-delete', [CampusController::class, 'bulkDelete']);
