<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::domain('{event}.bululand.web.id')->group(function () {
    Route::get('/', [EventController::class, 'show'])->name('events.show');
});
