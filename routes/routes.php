<?php

use App\Http\Middleware\VerifyCsrfToken;
use Dimafe6\GoogleCalendar\Http\Controllers\GoogleWebhookController;
use Illuminate\Support\Facades\Route;

Route::post(config('googlecalendar.webhook_uri'), GoogleWebhookController::class)
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('google.calendar.webhook');