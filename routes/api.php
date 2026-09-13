<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\JobPostController;
use App\Http\Controllers\Admin\SkillAliasController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SkillMergeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailPasswordResetController;
use App\Http\Controllers\Candidate\CandidateSkillController;
use App\Http\Controllers\Candidate\CareerPreferenceController;
use App\Http\Controllers\Candidate\EducationController;
use App\Http\Controllers\Candidate\ExperienceController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\ProjectController;
use App\Http\Controllers\JobFeedController;
use App\Http\Controllers\SavedJobController;
use App\Http\Controllers\SkillSearchController;
use App\Http\Middleware\EnsureActiveAdmin;
use App\Http\Middleware\EnsureActiveCandidate;
use App\Http\Middleware\EnsureActiveUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1,api-login:');
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:api');
Route::get('/candidate/profile', [ProfileController::class, 'show'])->middleware(['auth:api', EnsureActiveCandidate::class]);
Route::patch('/candidate/profile', [ProfileController::class, 'update'])->middleware(['auth:api', EnsureActiveCandidate::class]);
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

require __DIR__.'/cv.php';
