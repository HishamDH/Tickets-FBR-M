<?php

use App\Http\Controllers\Auth\MerchantLoginController;
use App\Http\Controllers\Dashboard\MerchantDashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Merchant Routes
|--------------------------------------------------------------------------
|
| Here is where you can register merchant routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "merchant" middleware group. Now create something great!
|
*/

// Merchant Authentication Routes
Route::prefix('merchant')->name('merchant.')->group(function () {
    // Merchant Login Routes
    Route::middleware('guest:merchant')->group(function () {
        Route::get('login', [MerchantLoginController::class, 'create'])->name('login');
        Route::post('login', [MerchantLoginController::class, 'store']);
    });

    // Merchant Protected Routes
    Route::middleware(['merchant', 'merchant.status'])->group(function () {
        // Logout
        Route::post('logout', [MerchantLoginController::class, 'destroy'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('dashboard');
        
        // Services Management
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [ServiceController::class, 'merchantIndex'])->name('index');
            Route::get('/create', [ServiceController::class, 'create'])->name('create');
            Route::post('/', [ServiceController::class, 'store'])->name('store');
            Route::get('/{id}', [ServiceController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ServiceController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ServiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // Bookings Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'merchantIndex'])->name('index');
            Route::get('/{id}', [BookingController::class, 'show'])->name('show');
            Route::patch('/{id}/status', [BookingController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/confirm', [BookingController::class, 'confirm'])->name('confirm');
            Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        });
        
        // Payments & Analytics
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [PaymentController::class, 'merchantIndex'])->name('index');
            Route::get('/analytics', [AnalyticsController::class, 'merchantAnalytics'])->name('analytics');
            Route::get('/reports', [AnalyticsController::class, 'merchantReports'])->name('reports');
        });
        
        // Support & Communication
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [SupportController::class, 'merchantIndex'])->name('index');
            Route::get('/create', [SupportController::class, 'create'])->name('create');
            Route::post('/', [SupportController::class, 'store'])->name('store');
            Route::get('/{id}', [SupportController::class, 'show'])->name('show');
        });
        
        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'merchantIndex'])->name('index');
            Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::delete('/clear', [NotificationController::class, 'clearAll'])->name('clear');
        });
        
        // Profile & Settings
        Route::get('/profile', function () {
            return view('merchant.profile.index');
        })->name('profile.index');
        
        Route::get('/settings', function () {
            return view('merchant.settings.index');
        })->name('settings.index');
    });
});