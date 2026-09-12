<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailPasswordResetController;
use App\Http\Controllers\Candidate\CandidateSkillController;
use App\Http\Controllers\Candidate\CareerPreferenceController;
use App\Http\Controllers\Candidate\EducationController;
use App\Http\Controllers\Candidate\ExperienceController;
use App\Http\Controllers\Candidate\ProfileController;
use App\Http\Controllers\Candidate\ProjectController;
use App\Http\Middleware\EnsureActiveCandidate;
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

Route::get('/candidate/career-preferences', [CareerPreferenceController::class, 'show'])->middleware(['auth:api', EnsureActiveCandidate::class]);
Route::patch('/candidate/career-preferences', [CareerPreferenceController::class, 'update'])->middleware(['auth:api', EnsureActiveCandidate::class]);

Route::middleware(['auth:api', EnsureActiveCandidate::class])->group(function (): void {
    Route::get('/candidate/educations', [EducationController::class, 'index']);
    Route::post('/candidate/educations', [EducationController::class, 'store']);
    Route::get('/candidate/educations/{education}', [EducationController::class, 'show']);
    Route::patch('/candidate/educations/{education}', [EducationController::class, 'update']);
    Route::delete('/candidate/educations/{education}', [EducationController::class, 'destroy']);
});

Route::middleware(['auth:api', EnsureActiveCandidate::class])->group(function (): void {
    Route::get('/candidate/experiences', [ExperienceController::class, 'index']);
    Route::post('/candidate/experiences', [ExperienceController::class, 'store']);
    Route::get('/candidate/experiences/{experience}', [ExperienceController::class, 'show']);
    Route::patch('/candidate/experiences/{experience}', [ExperienceController::class, 'update']);
    Route::delete('/candidate/experiences/{experience}', [ExperienceController::class, 'destroy']);
});

Route::middleware(['auth:api', EnsureActiveCandidate::class])->group(function (): void {
    Route::get('/candidate/projects', [ProjectController::class, 'index']);
    Route::post('/candidate/projects', [ProjectController::class, 'store']);
    Route::get('/candidate/projects/{project}', [ProjectController::class, 'show']);
    Route::patch('/candidate/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/candidate/projects/{project}', [ProjectController::class, 'destroy']);
});

Route::middleware(['auth:api', EnsureActiveCandidate::class])->group(function (): void {
    Route::get('/candidate/skills', [CandidateSkillController::class, 'index']);
    Route::post('/candidate/skills', [CandidateSkillController::class, 'store']);
    Route::delete('/candidate/skills/{candidateSkill}', [CandidateSkillController::class, 'destroy']);
});
