<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailPasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1,api-login:');
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:api');
Route::post('/auth/verify-email-otp', [AuthController::class, 'verifyEmailOtp'])->middleware('throttle:password-reset-verification');
Route::post('/auth/resend-email-otp', [AuthController::class, 'resendEmailOtp'])->middleware('throttle:email-verification-otp');
Route::post('/auth/forgot-password', [EmailPasswordResetController::class, 'store'])
    ->middleware('throttle:password-reset-otp');
Route::post('/auth/forgot-password/verify-otp', [EmailPasswordResetController::class, 'verifyOtp'])
    ->middleware('throttle:password-reset-verification');
Route::post('/auth/reset-password', [EmailPasswordResetController::class, 'resetPassword'])
    ->middleware('throttle:password-reset-verification');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
