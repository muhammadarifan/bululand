<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::domain('{event}.bululand.web.id')->group(function () {
    Route::get('/', function ($event) {
        return "Selamat datang di $event";
    });
});
