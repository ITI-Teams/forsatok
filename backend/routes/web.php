<?php

use App\Domains\Jobs\Actions\job\ShowJobAction;
use App\Http\Controllers\Api\CandidateInfoController;
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
use App\Http\Controllers\Auth\VerifyEmailController;
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
use App\Livewire\Location\LocationIndex;

use App\Livewire\CompanyReviews\ListCompanyReviews;
use App\Livewire\CompanyReviews\TrashCompanyReview;
use App\Livewire\Contact\ListContactMessages;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/admin', function () {

})->name('admin');

Route::get('/list', function () {
    return view('list');
})->name('list');
Route::get('/form', function () {
    return view('form');
})->name('form');
// Categories routes
Route::prefix('categories')->group(function () {
    Route::get('/', CategoryList::class)->name('categories.index');
    Route::get('/create', CategoryForm::class)->name('categories.create');
    Route::get('/edit/{category}', CategoryForm::class)->name('categories.edit');
    Route::get('/trash', CategoryTrash::class)->name('categories.trash');
});
// Skills routes
Route::prefix('skills')->group(function () {
    Route::get('/', SkillList::class)->name('skills.index');
    Route::get('/create', SkillForm::class)->name('skills.create');
    Route::get('/edit/{skill}', SkillForm::class)->name('skills.edit');
    Route::get('/trash', SkillTrash::class)->name('skills.trash');
});
// Company Reviews routes
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

Route::prefix('company-reviews')->group(function () {
    Route::get('/', ListCompanyReviews::class)->name('company-reviews.index');
    Route::get('/trash', TrashCompanyReview::class)->name('company-reviews.trash');
});
// Users routes
Route::prefix('users')->group(function () {
    Route::get('/', UserList::class)->name('users.index');
    Route::get('/create', UserForm::class)->name('users.create');
    Route::get('/edit/{user}', UserForm::class)->name('users.edit');
    Route::get('/trash', UserTrash::class)->name('users.trash');
});
// job routes
Route::prefix('jobs')->middleware(['auth'])->name('jobs.')->group(function () {
    Route::get('/', JobList::class)->name('index');
    Route::get('/create', JobForm::class)->name('create');
    Route::get('/{job}/edit', JobForm::class)->name('edit');
    Route::get('/{id}', JobShow::class)->name('show');
    Route::get('/trash', JobTrash::class)->name('trash');
});
// job Applications routes
Route::prefix('job/application')->middleware(['auth'])->name('job.app.')->group(function () {
    Route::get('/', ApplicationList::class)->name('index');
    Route::get('/create', ApplicationForm::class)->name('create');
    Route::get('/{application}/edit', ApplicationForm::class)->name('edit');
    Route::get('/trash', ApplicationTrash::class)->name('trash');
    Route::get('/{id}', ApplicationShow::class)->name('show');

});
// Roles and Permissions
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/roles', RoleIndex::class)->name('admin.roles');
    Route::get('/roles/permissions', RolePermission::class)->name('admin.roles.permissions');
    Route::get('/permissions', PermissionIndex::class)->name('admin.permissions');
    Route::get('/users/assign', UserRolePermission::class)->name('admin.user.assign');
});
// Contact Messages
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/contact-messages', ListContactMessages::class)
         ->name('admin.contact-messages');
});
// Employer Info
Route::prefix('employer')->middleware(['auth'])->group(function () {
    Route::get('/', EmployerProfile::class)->name('employer.profile');
    Route::get('/edit', EditEmployerInfo::class)->name('employer.profile.edit');
});
require __DIR__.'/auth.php';

