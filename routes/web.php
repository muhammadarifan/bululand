<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::domain('{event}.bl.test')->group(function () {
//     Route::get('/', [EventController::class, 'show'])->name('events.show');
// });

Route::get('/{event}/transactions', [EventController::class, 'transactions'])->name('events.transactions');
Route::get('/{event}/check-contribution', [EventController::class, 'checkContribution'])->name('events.check-contribution');
Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
