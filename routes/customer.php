<?php

use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupportController;
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

// Customer Authentication Routes - prefix and name already set in RouteServiceProvider

// Customer Login Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [CustomerLoginController::class, 'create'])->name('login');
    Route::post('login', [CustomerLoginController::class, 'store']);
    Route::get('register', [App\Http\Controllers\Auth\CustomerRegisterController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\CustomerRegisterController::class, 'store']);
});

// Customer Protected Routes
Route::middleware(['auth:customer'])->group(function () {
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
            Route::get('/{service}', [CustomerServiceController::class, 'show'])->name('show');
            Route::post('/{service}/book', [CustomerServiceController::class, 'book'])->name('book');
            Route::post('/{service}/favorite', [CustomerServiceController::class, 'toggleFavorite'])->name('favorite');
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

        // Shopping Cart Management
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [App\Http\Controllers\CartController::class, 'show'])->name('index');
            Route::get('/api', [App\Http\Controllers\CartController::class, 'index'])->name('api');
            Route::post('/', [App\Http\Controllers\CartController::class, 'store'])->name('store');
            Route::put('/{cartItem}', [App\Http\Controllers\CartController::class, 'update'])->name('update');
            Route::delete('/{cartItem}', [App\Http\Controllers\CartController::class, 'destroy'])->name('destroy');
            Route::delete('/', [App\Http\Controllers\CartController::class, 'clear'])->name('clear');
            Route::get('/count', [App\Http\Controllers\CartController::class, 'count'])->name('count');
            Route::post('/merge', [App\Http\Controllers\CartController::class, 'merge'])->name('merge');
            Route::get('/validate', [App\Http\Controllers\CartController::class, 'validateCart'])->name('validate');
        });

        // Checkout Process
        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::get('/', [App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Customer\CheckoutController::class, 'store'])->name('store');
            Route::get('/confirmation', [App\Http\Controllers\Customer\CheckoutController::class, 'confirmation'])->name('confirmation');
        });

        // Customer Booking Routes  
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('/{offering}', [App\Http\Controllers\Frontend\BookingController::class, 'show'])->name('show');
            Route::post('/{offering}', [App\Http\Controllers\Frontend\BookingController::class, 'store'])->name('store');
        });

        // Customer Booking Management
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/{booking}/confirmation', [App\Http\Controllers\Frontend\BookingController::class, 'confirmation'])->name('confirmation');
            Route::patch('/{booking}/cancel', [App\Http\Controllers\Frontend\BookingController::class, 'cancel'])->name('cancel');
        });

        // Reviews Management
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/service/{service}/create', [App\Http\Controllers\ReviewController::class, 'create'])->name('create');
            Route::post('/service/{service}', [App\Http\Controllers\ReviewController::class, 'store'])->name('store');
            Route::get('/service/{service}', [App\Http\Controllers\ReviewController::class, 'index'])->name('index');
            Route::get('/my-reviews', [App\Http\Controllers\ReviewController::class, 'userReviews'])->name('user');
            Route::get('/{review}/edit', [App\Http\Controllers\ReviewController::class, 'edit'])->name('edit');
            Route::put('/{review}', [App\Http\Controllers\ReviewController::class, 'update'])->name('update');
            Route::delete('/{review}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('delete');
        });

        // Favorites
        Route::get('/favorites', function () {
            return view('customer.favorites.index');
        })->name('favorites.index');

        // Customer Reviews Page
        Route::get('/my-reviews', function () {
            return view('customer.reviews.index');
        })->name('my-reviews.index');

        // Customer Chat & Messaging Routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
            Route::get('/conversations/{conversation}', [ChatController::class, 'getMessages'])->name('conversations.show');
            Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('conversations.messages.send');
            Route::post('/conversations/start', [ChatController::class, 'startConversation'])->name('conversations.start');
            Route::patch('/conversations/{conversation}/close', [ChatController::class, 'closeConversation'])->name('conversations.close');
            Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('messages.delete');

            // Customer Support Chat
            Route::prefix('support')->name('support.')->group(function () {
                Route::get('/', [ChatController::class, 'support'])->name('index');
                Route::post('/ticket', [ChatController::class, 'createSupportTicket'])->name('ticket.create');
            });
        });
    });
