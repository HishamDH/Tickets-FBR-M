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
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Add these routes back for test compatibility
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

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

    // Logout for all roles
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

// Customer Authentication Routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('register', [CustomerRegisterController::class, 'create'])
            ->name('register');

        Route::post('register', [CustomerRegisterController::class, 'store']);

        Route::get('login', [CustomerLoginController::class, 'create'])
            ->name('login');

        Route::post('login', [CustomerLoginController::class, 'store']);
    });

    Route::middleware('auth:customer')->group(function () {
        Route::post('logout', [CustomerLoginController::class, 'destroy'])
            ->name('logout');
    });
});

// Merchant Authentication Routes
Route::prefix('merchant')->name('merchant.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('register', [MerchantRegisterController::class, 'create'])
            ->name('register');

        Route::post('register', [MerchantRegisterController::class, 'store']);

        Route::get('login', [MerchantLoginController::class, 'create'])
            ->name('login');

        Route::post('login', [MerchantLoginController::class, 'store']);
    });

    Route::middleware('auth:merchant')->group(function () {
        Route::post('logout', [MerchantLoginController::class, 'destroy'])
            ->name('logout');
    });

    // Status page for all authenticated merchants
    Route::middleware('auth')->group(function () {
        Route::get('status', function () {
            return view('auth.merchant.status');
        })->name('status');
    });
});

// Filament Merchant Auth Routes for compatibility
Route::prefix('merchant/auth')->name('filament.merchant.auth.')->group(function () {
    Route::get('login', [MerchantLoginController::class, 'create'])
        ->name('login');

    Route::post('login', [MerchantLoginController::class, 'store']);

    Route::get('register', [MerchantRegisterController::class, 'create'])
        ->name('register');

    Route::post('register', [MerchantRegisterController::class, 'store']);
});

// Partner Authentication Routes
Route::prefix('partner')->name('partner.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('register', [PartnerRegisterController::class, 'create'])
            ->name('register');

        Route::post('register', [PartnerRegisterController::class, 'store']);

        Route::get('login', [PartnerLoginController::class, 'create'])
            ->name('login');

        Route::post('login', [PartnerLoginController::class, 'store']);
    });

    Route::middleware('auth:partner')->group(function () {
        Route::post('logout', [PartnerLoginController::class, 'destroy'])
            ->name('logout');
    });

    // Status page for all authenticated partners
    Route::middleware('auth')->group(function () {
        Route::get('status', function () {
            return view('auth.partner.status');
        })->name('status');
    });
});

