<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cv\CvController;

Route::middleware(['auth:sanctum'])->prefix('cv')->group(function () {
    Route::post('/upload', [CvController::class, 'store']);
    Route::get('/history', [CvController::class, 'history']);
    Route::get('/status/{cvDocument}', [CvController::class, 'show']);
    Route::post('/retry/{cvDocument}', [CvController::class, 'retry']);
    Route::post('/extractions/{extraction}/verify', [CvController::class, 'verify']);
});
