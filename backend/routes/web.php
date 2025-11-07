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
    Route::get('/jobs/{id}', JobShow::class)->name('show');
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

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/roles', RoleIndex::class)->name('admin.roles');
    Route::get('/roles/permissions', RolePermission::class)->name('admin.roles.permissions');
    Route::get('/permissions', PermissionIndex::class)->name('admin.permissions');
    Route::get('/users/assign', UserRolePermission::class)->name('admin.user.assign');
});




// Employer Info
Route::prefix('employer')->middleware(['auth'])->group(function () {
    Route::get('/', EmployerProfile::class)->name('employer.profile');
    Route::get('/edit', EditEmployerInfo::class)->name('employer.profile.edit');
});
require __DIR__.'/auth.php';

