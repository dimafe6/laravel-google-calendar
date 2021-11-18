<?php

use Dimafe6\GoogleCalendar\Http\Controllers\GoogleWebhookController;
use Illuminate\Support\Facades\Route;

Route::name('google.calendar.webhook')
    ->post(config('googlecalendar.webhook_uri'), GoogleWebhookController::class);