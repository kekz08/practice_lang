<?php

use App\Http\Controllers\ClasseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('/api/classes', [ClasseController::class, 'index']);
