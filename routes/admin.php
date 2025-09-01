<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Login Routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'create'])->name('login');
        Route::post('login', [AdminLoginController::class, 'store']);
    });

    // Admin Protected Routes
    Route::middleware(['admin'])->group(function () {
        // Logout
        Route::post('logout', [AdminLoginController::class, 'destroy'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Analytics & Reports
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        
        // System Management
        Route::get('/users', function () {
            return view('admin.users.index');
        })->name('users.index');
        
        Route::get('/merchants', function () {
            return view('admin.merchants.index');
        })->name('merchants.index');
        
        Route::get('/services', function () {
            return view('admin.services.index');
        })->name('services.index');
        
        Route::get('/bookings', function () {
            return view('admin.bookings.index');
        })->name('bookings.index');
        
        Route::get('/payments', function () {
            return view('admin.payments.index');
        })->name('payments.index');
        
        Route::get('/support', function () {
            return view('admin.support.index');
        })->name('support.index');
        
        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'adminIndex'])->name('index');
            Route::post('/send', [NotificationController::class, 'sendToAll'])->name('send');
            Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        });
        
        // Settings
        Route::get('/settings', function () {
            return view('admin.settings.index');
        })->name('settings.index');
    });
});