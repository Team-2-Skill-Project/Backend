<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Application\JobApplicationController;

Route::middleware(['auth:api'])->prefix('applications')->group(function () {
    Route::get('/', [JobApplicationController::class, 'index']);
    Route::post('/', [JobApplicationController::class, 'store']);
    Route::get('/{jobApplication}', [JobApplicationController::class, 'show']);
    Route::patch('/{jobApplication}/status', [JobApplicationController::class, 'updateStatus']); // for admin or company
    Route::post('/{jobApplication}/withdraw', [JobApplicationController::class, 'withdraw']); // for candidate
});
