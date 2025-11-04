<?php

use App\Livewire\Category\CategoryForm;
use App\Livewire\Category\CategoryList;
use App\Livewire\Category\CategoryTrash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerifyEmailController;
use Livewire\Volt\Volt;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/admin', function () {
    return view('dashboard.index');
})->name('admin');

Route::prefix('categories')->group(function () {
    Route::get('/', CategoryList::class)->name('categories.index');
    Route::get('/create', CategoryForm::class)->name('categories.create');
    Route::get('/edit/{category}', CategoryForm::class)->name('categories.edit');
    Route::get('/trash', CategoryTrash::class)->name('categories.trash');
});
require __DIR__.'/auth.php';
