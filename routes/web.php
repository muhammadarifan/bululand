<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::domain('{event}.bl.test')->group(function () {
//     Route::get('/', [EventController::class, 'show'])->name('events.show');
// });

Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
