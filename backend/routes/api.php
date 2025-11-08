<?php

use App\Domains\Applications\Controllers\Api\ApplicationController;
use App\Domains\Jobs\Controllers\Api\SaveJobController;
use App\Domains\Users\Controllers\api\CandidateAuthController;
use App\Http\Controllers\Api\CandidateInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('auth')->group(function () {
    Route::post('candidate/register', [CandidateAuthController::class, 'register']);
    Route::post('candidate/login', [CandidateAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('candidate/forgot-password', [CandidateAuthController::class, 'forgotPassword']);
    Route::post('candidate/reset-password', [CandidateAuthController::class, 'resetPassword']);
    Route::post('candidate/send-verification-code', [CandidateAuthController::class, 'sendVerificationCode']);
    Route::post('candidate/verify-code', [CandidateAuthController::class, 'verifyCode']);
});
// Protected (requires token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('candidate/logout', [CandidateAuthController::class, 'logout']);
    // routes for applications
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::get('/applications/stats', [ApplicationController::class, 'stats']);
    Route::get('/applications/available-jobs', [ApplicationController::class, 'availableJobs']);
    Route::get('/applications/{id}', [ApplicationController::class, 'show']);
    Route::post('/applications', [ApplicationController::class, 'store']);
    // Save Job Routs
    Route::prefix('jobs')->group(function () {
        Route::get('/saved', [SaveJobController::class, 'index']);
        Route::post('/save', [SaveJobController::class, 'store']);
        Route::delete('/unsave/{id}', [SaveJobController::class, 'destroy']);
    });

// cadidate api routes
Route::get('/api/candidate-info', [CandidateInfoController::class, 'show']);
Route::post('/api/candidate-info', [CandidateInfoController::class, 'update']);
});


