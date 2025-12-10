<?php
use App\Domains\Applications\Controllers\Api\ApplicationController;
use App\Domains\Home\Controllers\Api\HomeController;
use App\Domains\Jobs\Controllers\Api\JobController;
use App\Domains\Jobs\Controllers\Api\SaveJobController;
use App\Domains\Jobs\Controllers\Api\SkillController;
use App\Domains\Notification\Controllers\Api\NotificationController;
use App\Domains\Users\Controllers\api\CandidateAuthController;
use App\Domains\Candidates\controllers\Api\CandidateInfoController;
use App\Domains\Employers\controllers\Api\EmployerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Domains\CompanyReviews\Controllers\Api\CompanyReviewsController;
use App\Domains\Contact\controllers\Api\ContactMessageController;
use App\Domains\Jobs\Controllers\Api\CategoryController;
use App\Domains\Jobs\Controllers\Api\JobFilterController;
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


    // employer api routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/employerinfo', [EmployerController::class, 'index']);
        Route::get('/employerinfo/{id}', [EmployerController::class, 'show']);
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

    // All notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    // Unread notifications
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    // Mark one notification as read
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    // Mark all notifications as read
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    // Delete one notification
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    // Delete all notifications
    Route::delete('/notifications', [NotificationController::class, 'clearAll']);

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
Route::get('/jobs', [JobController::class, 'index'])->middleware('auth:sanctum');
Route::get('/jobs/{id}', [JobController::class, 'show']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);

// Skills
Route::get('/skills', [SkillController::class, 'index']);

// Locations
Route::prefix('locations')->group(function () {
    Route::get('/countries', [LocationController::class, 'getCountries']);
    Route::get('/cities', [LocationController::class, 'getCities']);
});

// Contact Message Routs
Route::prefix('contact')->group(function () {
    Route::post('/', [ContactMessageController::class, 'store']);
    Route::get('/', [ContactMessageController::class, 'index'])->middleware('auth:sanctum');
});
// Skills Routs
Route::get('/skills', [SkillController::class, 'index']);


// Home Route
Route::get('/home', [HomeController::class, 'index']);

// cadidate api routes
Route::prefix('auth')->group(function () {
    Route::get('/candidatelist', [CandidateInfoController::class, 'index']);
    Route::get('/candidatelist/{id}', [CandidateInfoController::class, 'show']);
});

require __DIR__.'/dashboardApi.php';
Broadcast::routes(['middleware' => ['auth:sanctum']]);
