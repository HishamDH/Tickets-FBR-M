<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\CustomerRegisterController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\MerchantLoginController;
use App\Http\Controllers\Auth\MerchantRegisterController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PartnerLoginController;
use App\Http\Controllers\Auth\PartnerRegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordForceChangeController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Password reset routes only (no general login/register)
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Password force change routes (excluded from password expiry middleware)
    Route::get('password/force-change', [PasswordForceChangeController::class, 'show'])
        ->name('password.force-change');
    
    Route::post('password/force-change', [PasswordForceChangeController::class, 'update'])
        ->name('password.force-change.update');

    // Logout for all roles
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Password expiry routes - must check password expiry for authenticated users
Route::middleware(['auth', 'password.expiry'])->group(function () {
    // All authenticated routes that require password expiry check will be handled by middleware
    // The middleware will redirect to password change if needed
});

// Filament merchant logout route (the one Filament expects at /merchant/logout)
Route::post('merchant/logout', [MerchantLoginController::class, 'destroy'])
    ->name('filament.merchant.auth.logout');

