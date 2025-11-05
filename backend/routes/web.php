<?php

use App\Livewire\Admin\Permissions\PermissionIndex;
use App\Livewire\Admin\Roles\RoleIndex;
use App\Livewire\Admin\Roles\RolePermission;
use App\Livewire\Admin\Users\UserRolePermission;
use App\Livewire\Category\CategoryForm;
use App\Livewire\Category\CategoryList;
use App\Livewire\Category\CategoryTrash;
use App\Livewire\Employers\EditEmployerInfo;
use App\Livewire\Employers\EmployerProfile;
use Illuminate\Support\Facades\Route;
use App\Livewire\User\UserForm;
use App\Livewire\User\UserList;
use App\Livewire\User\UserTrash;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/admin', function () {
    // return view('dashboard.index');
    // return view ('livewire.jobs.index');
    // return view ('livewire.category.category-form');

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


Route::prefix('users')->group(function () {
    Route::get('/', UserList::class)->name('users.index');
    Route::get('/create', UserForm::class)->name('users.create');
    Route::get('/edit/{user}', UserForm::class)->name('users.edit');
    Route::get('/trash', UserTrash::class)->name('users.trash');
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

