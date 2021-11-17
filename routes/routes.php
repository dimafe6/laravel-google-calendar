<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::post('/google/calendar/webhook', function (Request $request) {
    Log::info('Google push notification');
    Log::info(json_encode($request->header()));
});