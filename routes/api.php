<?php

use App\Http\Controllers\Webhook\GowaWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhook/gowa', GowaWebhookController::class);
