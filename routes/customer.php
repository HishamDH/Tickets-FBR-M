<?php

use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
|
| Here is where you can register customer routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "customer" middleware group. Now create something great!
|
*/

// Customer Authentication Routes
Route::prefix('customer')->name('customer.')->group(function () {
    // Customer Login Routes
    Route::middleware('guest:customer')->group(function () {
        Route::get('login', [CustomerLoginController::class, 'create'])->name('login');
        Route::post('login', [CustomerLoginController::class, 'store']);
        Route::get('register', function () {
            return view('auth.customer.register');
        })->name('register');
    });

    // Customer Protected Routes
    Route::middleware(['customer'])->group(function () {
        // Logout
        Route::post('logout', [CustomerLoginController::class, 'destroy'])->name('logout');
        
        // Dashboard
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        
        // Bookings Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [CustomerBookingController::class, 'index'])->name('index');
            Route::get('/create', [CustomerBookingController::class, 'create'])->name('create');
            Route::post('/', [CustomerBookingController::class, 'store'])->name('store');
            Route::get('/{id}', [CustomerBookingController::class, 'show'])->name('show');
            Route::post('/{id}/cancel', [CustomerBookingController::class, 'cancel'])->name('cancel');
            Route::get('/{id}/receipt', [CustomerBookingController::class, 'receipt'])->name('receipt');
        });
        
        // Services Browsing
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [CustomerServiceController::class, 'index'])->name('index');
            Route::get('/{id}', [CustomerServiceController::class, 'show'])->name('show');
            Route::post('/{id}/book', [CustomerServiceController::class, 'book'])->name('book');
            Route::post('/{id}/favorite', [CustomerServiceController::class, 'toggleFavorite'])->name('favorite');
        });
        
        // Payments & History
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('/', [CustomerPaymentController::class, 'index'])->name('index');
            Route::get('/{id}', [CustomerPaymentController::class, 'show'])->name('show');
            Route::post('/process', [CustomerPaymentController::class, 'process'])->name('process');
            Route::get('/history', [CustomerPaymentController::class, 'history'])->name('history');
        });
        
        // Support & Communication
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [SupportController::class, 'customerIndex'])->name('index');
            Route::get('/create', [SupportController::class, 'create'])->name('create');
            Route::post('/', [SupportController::class, 'store'])->name('store');
            Route::get('/{id}', [SupportController::class, 'show'])->name('show');
            Route::post('/{id}/reply', [SupportController::class, 'reply'])->name('reply');
        });
        
        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'customerIndex'])->name('index');
            Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::delete('/clear', [NotificationController::class, 'clearAll'])->name('clear');
        });
        
        // Profile & Settings
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        
        // Favorites
        Route::get('/favorites', function () {
            return view('customer.favorites.index');
        })->name('favorites.index');
        
        // Reviews
        Route::get('/reviews', function () {
            return view('customer.reviews.index');
        })->name('reviews.index');
    });
});