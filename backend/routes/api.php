<?php
use App\Domains\Applications\Controllers\Api\ApplicationController;
use App\Domains\Home\Controllers\Api\HomeController;
use App\Domains\Jobs\Controllers\Api\JobController;
use App\Domains\Jobs\Controllers\Api\SaveJobController;
use App\Domains\Users\Controllers\api\CandidateAuthController;
use App\Domains\Candidates\Controllers\Api\CandidateInfoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Domains\CompanyReviews\Controllers\Api\CompanyReviewsController;
use App\Domains\Contact\Controllers\Api\ContactMessageController;
use App\Domains\Jobs\Controllers\Api\CategoryController;
use App\Domains\Jobs\Controllers\Api\JobFilterController;
use App\Domains\Jobs\Controllers\Api\SkillController;
use App\Domains\Location\Controllers\Api\LocationController;
use App\Domains\Candidates\Controllers\Api\CandidateSearchController;
use App\Domains\Employers\Controllers\Api\CompanySearchController;

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
    // cadidate api routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/candidate/info', [CandidateInfoController::class, 'showProfile']);
        Route::post('/candidate/info', [CandidateInfoController::class, 'update']);
    });
});

// Public candidates routes 
Route::get('/candidates/search', [CandidateSearchController::class, 'search']);
Route::get('/candidates/filter-options', [CandidateSearchController::class, 'getFilterOptions']);
// Public candidate profile routes
Route::get('/candidates', [CandidateInfoController::class, 'index']);
Route::get('/candidates/{id}', [CandidateInfoController::class, 'show']);

// Public companies routes
Route::get('/companies/search', [CompanySearchController::class, 'search']);
Route::get('/companies/filter-options', [CompanySearchController::class, 'getFilterOptions']);

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

// Jobs Routes
Route::get('/jobs/filter-options', [JobFilterController::class, 'getFilterOptions']);
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);

// Locations
Route::prefix('locations')->group(function () {
    Route::get('/countries', [LocationController::class, 'getCountries']);
    Route::get('/cities', [LocationController::class, 'getCities']);
});

// Contact Message Routs
Route::prefix('contact')->group(function () {
    Route::post('/', [ContactMessageController::class, 'store']);
    Route::get('/', [ContactMessageController::class, 'index'])
        ->middleware('auth:sanctum');
});
// Skills Routs
Route::get('/skills', [SkillController::class, 'index']);


// Home Route
Route::get('/home', [HomeController::class, 'index']);


