<?php
use App\Livewire\Admin\Permissions\PermissionIndex;
use App\Livewire\Admin\Roles\RoleIndex;
use App\Livewire\Admin\Roles\RolePermission;
use App\Livewire\Admin\Users\UserRolePermission;
use App\Livewire\Applications\ApplicationForm;
use App\Livewire\Applications\ApplicationList;
use App\Livewire\Applications\ApplicationShow;
use App\Livewire\Applications\ApplicationTrash;
use App\Livewire\Category\CategoryForm;
use App\Livewire\Category\CategoryList;
use App\Livewire\Category\CategoryTrash;
use App\Livewire\Employers\EditEmployerInfo;
use App\Livewire\Employers\EmployerProfile;
use App\Livewire\Jobs\JobForm;
use Illuminate\Support\Facades\Route;
use App\Livewire\Jobs\JobList;
use App\Livewire\Jobs\JobShow;
use App\Livewire\Jobs\JobTrash;
use App\Livewire\User\UserForm;
use App\Livewire\User\UserList;
use App\Livewire\User\UserTrash;
use App\Livewire\Skills\SkillForm;
use App\Livewire\Skills\SkillList;
use App\Livewire\Skills\SkillTrash;
use App\Livewire\Location\CountryForm;
use App\Livewire\Location\CountryList;
use App\Livewire\Location\CountryTrash;
use App\Livewire\Location\CityForm;
use App\Livewire\Location\CityList;
use App\Livewire\Location\CityTrash;
use App\Livewire\CompanyReviews\ListCompanyReviews;
use App\Livewire\CompanyReviews\TrashCompanyReview;
use App\Livewire\Contact\ListContactMessages;
use App\Domains\Users\Controllers\api\LinkedinController;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name(name: 'profile');

Route::middleware(['web'])->group(function () {
    Route::get('/api/auth/linkedin/redirect', [LinkedinController::class, 'redirect']);
    Route::get('/api/auth/linkedin/callback', [LinkedinController::class, 'callback']);
});


// ADMIN routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('categories')->group(function () {
        Route::get('/', CategoryList::class)->name('categories.index');
        Route::get('/create', CategoryForm::class)->name('categories.create');
        Route::get('/edit/{category}', CategoryForm::class)->name('categories.edit');
        Route::get('/trash', CategoryTrash::class)->name('categories.trash');
    });

    Route::prefix('skills')->group(function () {
        Route::get('/', SkillList::class)->name('skills.index');
        Route::get('/create', SkillForm::class)->name('skills.create');
        Route::get('/edit/{skill}', SkillForm::class)->name('skills.edit');
        Route::get('/trash', SkillTrash::class)->name('skills.trash');
    });

    Route::prefix('countries')->group(function () {
        Route::get('/', CountryList::class)->name('countries.index');
        Route::get('/create', CountryForm::class)->name('countries.create');
        Route::get('/edit/{country}', CountryForm::class)->name('countries.edit');
        Route::get('/trash', CountryTrash::class)->name('countries.trash');
    });

    Route::prefix('cities')->group(function () {
        Route::get('/', CityList::class)->name('cities.index');
        Route::get('/create', CityForm::class)->name('cities.create');
        Route::get('/edit/{city}', CityForm::class)->name('cities.edit');
        Route::get('/trash', CityTrash::class)->name('cities.trash');
    });

    Route::prefix('admin')->group(function () {
        Route::get('/roles', RoleIndex::class)->name('admin.roles');
        Route::get('/roles/permissions', RolePermission::class)->name('admin.roles.permissions');
        Route::get('/permissions', PermissionIndex::class)->name('admin.permissions');
        Route::get('/users/assign', UserRolePermission::class)->name('admin.user.assign');
        Route::get('/contact-messages', ListContactMessages::class)->name('admin.contact-messages');
        Route::get('/profile', function () {return view('profile');})->name('admin.profile');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', UserList::class)->name('users.index');
        Route::get('/create', UserForm::class)->name('users.create');
        Route::get('/edit/{user}', UserForm::class)->name('users.edit');
        Route::get('/trash', UserTrash::class)->name('users.trash');
    });
});

// EMPLOYER routes
Route::middleware(['auth', 'role:employer'])->group(function () {
    Route::prefix('jobs')->group(function () {
        Route::get('/', JobList::class)->name('jobs.index');
        Route::get('/create', JobForm::class)->name('jobs.create');
        Route::get('/{job}/edit', JobForm::class)->name('jobs.edit');
        Route::get('/{id}', JobShow::class)->name('jobs.show');
        Route::get('/trash', JobTrash::class)->name('jobs.trash');
    });

    Route::prefix('job/application')->group(function () {
        Route::get('/', ApplicationList::class)->name('job.app.index');
        Route::get('/create', ApplicationForm::class)->name('job.app.create');
        Route::get('/{application}/edit', ApplicationForm::class)->name('job.app.edit');
        Route::get('/trash', ApplicationTrash::class)->name('job.app.trash');
        Route::get('/{id}', ApplicationShow::class)->name('job.app.show');
    });

    Route::prefix('company-reviews')->group(function () {
        Route::get('/', ListCompanyReviews::class)->name('company-reviews.index');
        Route::get('/trash', TrashCompanyReview::class)->name('company-reviews.trash');
    });

    Route::prefix('employer')->group(function () {
        Route::get('/', EmployerProfile::class)->name('employer.profile');
        Route::get('/edit', EditEmployerInfo::class)->name('employer.profile.edit');
    });
});
require __DIR__.'/auth.php';

