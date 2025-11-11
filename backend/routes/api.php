<?php
use App\Domains\Applications\Controllers\Api\ApplicationController;
use App\Domains\Candidates\controllers\Api\CandidateListController;
use App\Domains\Home\Controllers\Api\HomeController;
use App\Domains\Jobs\Controllers\Api\JobController;
use App\Domains\Jobs\Controllers\Api\SaveJobController;
use App\Domains\Users\Controllers\api\CandidateAuthController;
use App\Domains\Candidates\Controllers\Api\CandidateInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Domains\CompanyReviews\Controllers\Api\CompanyReviewsController;
use App\Domains\Contact\Controllers\Api\ContactMessageController;
use App\Domains\Users\Controllers\api\LinkedinController;

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
    // // linkedin api routes
    // Route::get('/linkedin/redirect', [LinkedinController::class, 'redirect']);
    // Route::get('/linkedin/callback', [LinkedinController::class, 'callback']);
    // cadidate api routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/candidate/info', [CandidateInfoController::class, 'showProfile']);
        Route::post('/candidate/info', [CandidateInfoController::class, 'update']);
        Route::get('/candidatelist', [CandidateInfoController::class, 'index']);
        Route::get('/candidatelist/{id}', [CandidateInfoController::class, 'show']);
    });
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


});

 //  company reviews
Route::prefix('company-reviews')->group(function () {
    Route::get('/company/{companyId}', [CompanyReviewsController::class, 'showCompanyReviews']);
    Route::post('/', [CompanyReviewsController::class, 'store']);
    Route::put('/{id}', [CompanyReviewsController::class, 'update']);
    Route::delete('/{id}', [CompanyReviewsController::class, 'destroy']);
});

// Jobs Routs
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

// Contact Message Routs
Route::prefix('contact')->group(function () {
    Route::post('/', [ContactMessageController::class, 'store']);
    Route::get('/', [ContactMessageController::class, 'index'])
         ->middleware('auth:sanctum');
});



// Home Route
Route::get('/home', [HomeController::class, 'index']);


