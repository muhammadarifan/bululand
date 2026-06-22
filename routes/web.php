<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::domain('{event}.bl.test')->group(function () {
//     Route::get('/', [EventController::class, 'show'])->name('events.show');
// });

if (config('app.env') === 'local') {
    Route::get('/{event}/transactions', [EventController::class, 'transactions'])->name('events.transactions');
    Route::get('/{event}/check-contribution', [EventController::class, 'checkContribution'])->name('events.check-contribution');
    Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
} else {
    Route::domain('{event}.' . config('app.url'))->group(function () {
        Route::get('/', [EventController::class, 'show'])->name('events.show');
        Route::get('/transactions', [EventController::class, 'transactions'])->name('events.transactions');
        Route::get('/check-contribution', [EventController::class, 'checkContribution'])->name('events.check-contribution');
    });
}
