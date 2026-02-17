<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Fingerprint Machine Push Endpoint
Route::post('/attendance/push', [App\Http\Controllers\Api\FingerprintApiController::class, 'push']);
// Optional Alias for older machines
Route::post('/iclock/cdata', [App\Http\Controllers\Api\FingerprintApiController::class, 'push']);
