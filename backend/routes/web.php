<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';




// Route::get('/register',[RegisterController::class,'showRegister'])->name('auth.register');
// Route::post('/register',[RegisterController::class,'register']);

// Route::get('/login',[LoginController::class,'showLogin'])->name('auth.login');
// Route::post('/login',[LoginController::class,'login']);


// Route::get('/forgot-password',[ForgotPasswordController::class,'showForgotPassword'])->name('forgot-password');
// Route::post('/forgot-password',[ForgotPasswordController::class,'sendResetLink']);

// Route::get('/verify-email',[VerifyEmailController::class,'showVerifyEmail'])->name('verify-email');
// Route::post('/verify-email',[VerifyEmailController::class,'verifyEmail']);

// Route::get('/reset-password/{token}',[ResetPasswordController::class,'showResetPassword'])->name('reset-password');
// Route::post('/reset-password',[ResetPasswordController::class,'resetPassword']);
