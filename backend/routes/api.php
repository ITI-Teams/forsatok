<?php

use App\Domains\Users\Controllers\api\CandidateAuthController;
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

    // Protected (requires token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('candidate/logout', [CandidateAuthController::class, 'logout']);
    });
});
