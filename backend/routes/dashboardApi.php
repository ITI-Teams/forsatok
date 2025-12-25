<?php

use App\Domains\Applications\Controllers\Dashboard\DashboardApplicationController;
use App\Domains\Applications\Controllers\Dashboard\FilterController;
use App\Domains\Candidates\Controllers\Api\CandidateSkillSearchController;
use App\Domains\CompanyReviews\Controllers\Dashboard\DashboardCompanyReviewController;
use App\Domains\Contact\Controllers\Dashboard\DashboardContactMessageController;
use App\Domains\Employers\Controllers\Dashboard\EmployerProfileController;
use App\Domains\Employers\Controllers\Dashboard\EmployerStatsController;
use App\Domains\Jobs\Controllers\Api\JobSkillSearchController;
use App\Domains\Jobs\Controllers\Dashboard\DashboardCategoryController;
use App\Domains\Jobs\Controllers\Dashboard\DashboardJobController;
use App\Domains\Jobs\Controllers\Dashboard\DashboardSkillController;
use App\Domains\Location\Controllers\Dashboard\DashboardCityController;
use App\Domains\Location\Controllers\Dashboard\DashboardCountryController;
use App\Domains\Notification\Controllers\Api\NotificationController;
use App\Domains\Shared\Controllers\Dashboard\AuditLogController;
use App\Domains\Shared\Controllers\Dashboard\RolePermissionController;
use App\Domains\Users\Controllers\Dashboard\AdminStatsController;
use App\Domains\Users\Controllers\Dashboard\DashboardAuthController;
use App\Domains\Users\Controllers\Dashboard\DashboardPermissionController;
use App\Domains\Users\Controllers\Dashboard\DashboardRoleController;
use App\Domains\Users\Controllers\Dashboard\DashboardUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dashboard API Routes
|--------------------------------------------------------------------------
|
| This file contains all dashboard API routes for admin and employer dashboards.
| All business logic has been moved to dedicated Controllers for better
| organization, testability, and maintainability.
|
*/

Route::prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {

        // ═══════════════════════════════════════════════════════════════════
        // AUTH ROUTES (Public)
        // ═══════════════════════════════════════════════════════════════════
        Route::prefix('auth')->name('auth.')->middleware('throttle:auth')->group(function () {
            Route::post('/register', [DashboardAuthController::class, 'register'])->name('register');
            Route::get('/email/verify/{id}/{hash}', [DashboardAuthController::class, 'verify'])->name('verification.verify.api')->middleware(['signed', 'throttle:6,1']);
            Route::post('/login', [DashboardAuthController::class, 'login'])->name('login');
            Route::post('/logout', [DashboardAuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
            Route::post('/forgot-password', [DashboardAuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('/reset-password', [DashboardAuthController::class, 'resetPassword'])->name('reset-password');
        });

        // ═══════════════════════════════════════════════════════════════════
        // AUTHENTICATED ROUTES
        // ═══════════════════════════════════════════════════════════════════
        Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

            // Profile
            Route::get('/profile', [DashboardAuthController::class, 'profile'])->name('profile');

            // Resend Verification
            Route::post('/email/resend-link', [DashboardAuthController::class, 'resendVerificationLink'])->name('verification.resend.link');

            // ───────────────────────────────────────────────────────────────
            // ADMIN ONLY ROUTES
            // ───────────────────────────────────────────────────────────────
            Route::middleware('role:admin')->group(function () {

                // Admin Stats
                Route::get('/stats/admin', [AdminStatsController::class, 'index'])->name('stats.admin');

                // Categories
                Route::prefix('categories')->name('categories.')->group(function () {
                    Route::get('/', [DashboardCategoryController::class, 'index'])->name('index');
                    Route::post('/', [DashboardCategoryController::class, 'store'])->name('store');
                    Route::get('/trashed', [DashboardCategoryController::class, 'trashed'])->name('trashed');
                    Route::post('/{id}/restore', [DashboardCategoryController::class, 'restore'])->name('restore');
                    Route::delete('/{id}/force', [DashboardCategoryController::class, 'forceDelete'])->name('force-delete');
                    Route::put('/{category}', [DashboardCategoryController::class, 'update'])->name('update');
                    Route::delete('/{category}', [DashboardCategoryController::class, 'destroy'])->name('destroy');
                });

                // Skills
                Route::prefix('skills')->name('skills.')->group(function () {
                    Route::get('/', [DashboardSkillController::class, 'index'])->name('index');
                    Route::post('/', [DashboardSkillController::class, 'store'])->name('store');
                    Route::get('/trashed', [DashboardSkillController::class, 'trashed'])->name('trashed');
                    Route::post('/{id}/restore', [DashboardSkillController::class, 'restore'])->name('restore');
                    Route::delete('/{id}/force', [DashboardSkillController::class, 'forceDelete'])->name('force-delete');
                    Route::put('/{skill}', [DashboardSkillController::class, 'update'])->name('update');
                    Route::delete('/{skill}', [DashboardSkillController::class, 'destroy'])->name('destroy');
                });

                // Countries
                Route::prefix('countries')->name('countries.')->group(function () {
                    Route::get('/', [DashboardCountryController::class, 'index'])->name('index');
                    Route::post('/', [DashboardCountryController::class, 'store'])->name('store');
                    Route::get('/trashed', [DashboardCountryController::class, 'trashed'])->name('trashed');
                    Route::post('/{id}/restore', [DashboardCountryController::class, 'restore'])->name('restore');
                    Route::delete('/{id}/force', [DashboardCountryController::class, 'forceDelete'])->name('force-delete');
                    Route::put('/{country}', [DashboardCountryController::class, 'update'])->name('update');
                    Route::delete('/{country}', [DashboardCountryController::class, 'destroy'])->name('destroy');
                });

                // Cities
                Route::prefix('cities')->name('cities.')->group(function () {
                    Route::get('/', [DashboardCityController::class, 'index'])->name('index');
                    Route::post('/', [DashboardCityController::class, 'store'])->name('store');
                    Route::get('/trashed', [DashboardCityController::class, 'trashed'])->name('trashed');
                    Route::post('/{id}/restore', [DashboardCityController::class, 'restore'])->name('restore');
                    Route::delete('/{id}/force', [DashboardCityController::class, 'forceDelete'])->name('force-delete');
                    Route::put('/{city}', [DashboardCityController::class, 'update'])->name('update');
                    Route::delete('/{city}', [DashboardCityController::class, 'destroy'])->name('destroy');
                });

                // Users
                Route::prefix('users')->name('users.')->group(function () {
                    Route::get('/', [DashboardUserController::class, 'index'])->name('index');
                    Route::post('/', [DashboardUserController::class, 'store'])->name('store');
                    Route::get('/trashed', [DashboardUserController::class, 'trashed'])->name('trashed');
                    Route::get('/rejected', [DashboardUserController::class, 'rejectedUsers'])->name('rejected');
                    Route::get('/rejection-history/{email}', [DashboardUserController::class, 'rejectionHistory'])->name('rejection-history');
                    Route::post('/{id}/restore', [DashboardUserController::class, 'restore'])->name('restore');
                    Route::delete('/{id}/force', [DashboardUserController::class, 'forceDelete'])->name('force-delete');
                    Route::put('/{user}', [DashboardUserController::class, 'update'])->name('update');
                    Route::post('/{user}/approve', [DashboardUserController::class, 'approve'])->name('approve');
                    Route::post('/{user}/reject', [DashboardUserController::class, 'reject'])->name('reject');
                    Route::post('/{user}/ban', [DashboardUserController::class, 'ban'])->name('ban');
                    Route::post('/{user}/unban', [DashboardUserController::class, 'unban'])->name('unban');
                    Route::delete('/{user}', [DashboardUserController::class, 'destroy'])->name('destroy');
                });

                // Roles
                Route::prefix('roles')->name('roles.')->group(function () {
                    Route::get('/', [DashboardRoleController::class, 'index'])->name('index');
                    Route::post('/', [DashboardRoleController::class, 'store'])->name('store');
                    Route::put('/{role}', [DashboardRoleController::class, 'update'])->name('update');
                    Route::delete('/{role}', [DashboardRoleController::class, 'destroy'])->name('destroy');
                });

                // Permissions
                Route::prefix('permissions')->name('permissions.')->group(function () {
                    Route::get('/', [DashboardPermissionController::class, 'index'])->name('index');
                    Route::post('/', [DashboardPermissionController::class, 'store'])->name('store');
                    Route::put('/{permission}', [DashboardPermissionController::class, 'update'])->name('update');
                    Route::delete('/{permission}', [DashboardPermissionController::class, 'destroy'])->name('destroy');
                });

                // Role-Permissions
                Route::get('/role-permissions', [RolePermissionController::class, 'rolePermissions'])->name('roles.permissions');
                Route::post('/role-permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.permissions.update');

                // User-Access
                Route::get('/user-access', [RolePermissionController::class, 'userAccess'])->name('users.access');
                Route::post('/user-access', [RolePermissionController::class, 'updateUserAccess'])->name('users.access.update');

                // Contact Messages (Admin)
                Route::prefix('contact-messages')->name('contact.')->group(function () {
                    Route::get('/', [DashboardContactMessageController::class, 'index'])->name('index');
                    Route::delete('/{message}', [DashboardContactMessageController::class, 'destroy'])->name('destroy');
                });

                // Audit Logs
                Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
            });

            // ───────────────────────────────────────────────────────────────
            // NOTIFICATIONS (All authenticated users)
            // ───────────────────────────────────────────────────────────────
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [NotificationController::class, 'index'])->name('index');
                Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
                Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
                Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
                Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
                Route::delete('/', [NotificationController::class, 'clearAll'])->name('clear');
            });

            // ───────────────────────────────────────────────────────────────
            // ADMIN | EMPLOYER ROUTES (Jobs)
            // ───────────────────────────────────────────────────────────────
            Route::middleware('role:admin|employer')->group(function () {
                Route::prefix('jobs')->name('jobs.')->group(function () {

                    // Jobs with manage permission
                    Route::middleware('permission:jobs.manage')->group(function () {
                        Route::post('/', [DashboardJobController::class, 'store'])->name('store');
                        Route::put('/{job}', [DashboardJobController::class, 'update'])->name('update');
                        Route::delete('/{job}', [DashboardJobController::class, 'destroy'])->name('destroy');
                        Route::get('/trashed', [DashboardJobController::class, 'trashed'])->name('trashed');
                        Route::post('/{id}/restore', [DashboardJobController::class, 'restore'])->name('restore');
                        Route::delete('/{id}/force', [DashboardJobController::class, 'forceDelete'])->name('force-delete');
                        Route::post('/{job}/approve', [DashboardJobController::class, 'approve'])->name('approve');
                        Route::post('/{job}/reject', [DashboardJobController::class, 'reject'])->name('reject');
                        Route::post('/{job}/resubmit', [DashboardJobController::class, 'resubmit'])->name('resubmit');
                    });

                    // Jobs with view permission
                    Route::middleware('permission:jobs.view')->group(function () {
                        Route::get('/', [DashboardJobController::class, 'index'])->name('index');
                        Route::get('/{job}', [DashboardJobController::class, 'show'])->name('show');
                    });
                });
            });

            // ───────────────────────────────────────────────────────────────
            // EMPLOYER ONLY ROUTES
            // ───────────────────────────────────────────────────────────────
            Route::middleware('role:employer')->group(function () {

                // Employer Stats
                Route::get('/stats/employer', [EmployerStatsController::class, 'index'])->name('stats.employer');

                // Applications
                Route::prefix('applications')->name('applications.')->group(function () {
                    Route::get('/filter', FilterController::class)->name('filter');
                    Route::get('/', [DashboardApplicationController::class, 'index'])->name('index');
                    Route::post('/', [DashboardApplicationController::class, 'store'])->name('store');
                    Route::get('/trashed', [DashboardApplicationController::class, 'trashed'])->name('trashed');
                    Route::delete('/trash/empty', [DashboardApplicationController::class, 'emptyTrash'])->name('empty-trash');
                    Route::get('/{application}', [DashboardApplicationController::class, 'show'])->name('show');
                    Route::put('/{application}', [DashboardApplicationController::class, 'update'])->name('update');
                    Route::delete('/{application}', [DashboardApplicationController::class, 'destroy'])->name('destroy');
                    Route::post('/{id}/restore', [DashboardApplicationController::class, 'restore'])->name('restore');
                    Route::delete('/{id}/force', [DashboardApplicationController::class, 'forceDelete'])->name('force-delete');
                });

                // Company Reviews
                Route::prefix('company-reviews')->name('reviews.')->group(function () {
                    Route::get('/', [DashboardCompanyReviewController::class, 'index'])->name('index');
                    Route::get('/trashed', [DashboardCompanyReviewController::class, 'trashed'])->name('trashed');
                    Route::post('/{review}/approve', [DashboardCompanyReviewController::class, 'approve'])->name('approve');
                    Route::post('/{review}/reject', [DashboardCompanyReviewController::class, 'reject'])->name('reject');
                    Route::delete('/{review}', [DashboardCompanyReviewController::class, 'destroy'])->name('destroy');
                    Route::post('/{id}/restore', [DashboardCompanyReviewController::class, 'restore'])->name('restore');
                    Route::delete('/{id}/force', [DashboardCompanyReviewController::class, 'forceDelete'])->name('force-delete');
                });

                // Contact Messages (Employer)
                Route::prefix('contact-messages')->group(function () {
                    Route::get('/', [DashboardContactMessageController::class, 'employerIndex'])->name('employer.contact.index');
                });

                // Employer Profile
                Route::prefix('employer')->name('employer.')->group(function () {
                    Route::get('/profile', [EmployerProfileController::class, 'show'])->name('profile');
                    Route::put('/profile', [EmployerProfileController::class, 'update'])->name('profile.update');
                });
            });

            // ───────────────────────────────────────────────────────────────
            // SHARED ROUTES (Admin | Employer)
            // ───────────────────────────────────────────────────────────────
            Route::prefix('contact-messages')->middleware('role:admin|employer')->group(function () {
                Route::get('/', [DashboardContactMessageController::class, 'sharedIndex'])->name('shared.contact.index');
            });
        });


        // Candidates skills search
        Route::prefix('candidates')->group(function () {
            //Search candidates by skills with OR logic and relevance scoring
            Route::post('/search-by-skills', [CandidateSkillSearchController::class, 'searchBySkills']);
            //Get statistics about skill matches
            Route::post('/skill-match-stats', [CandidateSkillSearchController::class, 'getSkillMatchStats']);
        });

        Route::prefix('jobs')->group(function () {
            //Search jobs by skills with OR logic and relevance scoring
            Route::post('/search-by-skills', [JobSkillSearchController::class, 'searchBySkills']);
            //Get statistics about skill matches for jobs
            Route::post('/skill-match-stats', [JobSkillSearchController::class, 'getSkillMatchStats']);
        });
    });
