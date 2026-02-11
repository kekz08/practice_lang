<?php

use Illuminate\Support\Facades\Route;

// SPA fallback: serve the Vue app for all other GET requests so client-side routing works
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
