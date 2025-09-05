<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Chat/Messaging System
    Route::prefix('chat')->group(function () {
        Route::get('conversations', [ChatController::class, 'index']);
        Route::post('conversations', [ChatController::class, 'startConversation']);
        Route::get('conversations/{conversation}/messages', [ChatController::class, 'getMessages']);
        Route::post('conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
        Route::patch('conversations/{conversation}/close', [ChatController::class, 'closeConversation']);
        Route::get('unread-count', [ChatController::class, 'getUnreadCount']);
    });

    // POS System
    Route::prefix('pos')->middleware('permission:pos_view')->group(function () {
        Route::get('offerings', [PosController::class, 'getOfferings']);
        Route::get('customers/search', [PosController::class, 'searchCustomers']);
        Route::post('reservations', [PosController::class, 'createReservation']);
        Route::get('reservations/{reservation}/qr', [PosController::class, 'generateQRCode']);
        Route::post('reservations/{reservation}/verify', [PosController::class, 'verifyQRCode']);
        Route::patch('reservations/{reservation}/attendance', [PosController::class, 'markAttendance']);
        Route::get('sales-summary', [PosController::class, 'getSalesSummary']);
    });

    // Withdrawal System
    Route::prefix('withdrawals')->middleware('permission:withdrawals_view')->group(function () {
        Route::get('/', [WithdrawController::class, 'index']);
        Route::post('/', [WithdrawController::class, 'requestWithdrawal']);
        Route::patch('{withdrawal}/approve', [WithdrawController::class, 'approveWithdrawal'])
            ->middleware('permission:withdrawals_approve');
        Route::patch('{withdrawal}/reject', [WithdrawController::class, 'rejectWithdrawal'])
            ->middleware('permission:withdrawals_approve');
        Route::get('wallet', [WithdrawController::class, 'getWalletInfo']);
    });

    // Cart System
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('add', [CartController::class, 'addToCart']);
        Route::patch('update', [CartController::class, 'updateCart']);
        Route::delete('remove', [CartController::class, 'removeFromCart']);
        Route::delete('clear', [CartController::class, 'clearCart']);
        Route::post('validate', [CartController::class, 'validateCart']);
        Route::post('merge', [CartController::class, 'mergeCart']);
    });

    // Support System
    Route::prefix('support')->group(function () {
        Route::get('tickets', [SupportController::class, 'index']);
        Route::post('tickets', [SupportController::class, 'store']);
        Route::delete('tickets/{ticket}', [SupportController::class, 'destroy']);
    });

    // Seat Booking System
    Route::prefix('seats')->group(function () {
        Route::get('venue-layout/{offeringId}', [App\Http\Controllers\Api\SeatBookingController::class, 'getVenueLayout']);
        Route::post('reserve', [App\Http\Controllers\Api\SeatBookingController::class, 'reserveSeats']);
        Route::delete('reservation/{bookingId}', [App\Http\Controllers\Api\SeatBookingController::class, 'cancelReservation']);
        Route::get('booking/{bookingId}', [App\Http\Controllers\Api\SeatBookingController::class, 'getBookingDetails']);
    });

    // Services API
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']); // List services with filters
        Route::get('categories', [ServiceController::class, 'categories']); // Get service categories
        Route::get('featured', [ServiceController::class, 'featured']); // Get featured services
        Route::get('search', [ServiceController::class, 'search']); // Search services
        Route::get('{service}', [ServiceController::class, 'show']); // Get single service
        
        // Merchant-only routes
        Route::middleware('role:Merchant')->group(function () {
            Route::post('/', [ServiceController::class, 'store']); // Create service
            Route::put('{service}', [ServiceController::class, 'update']); // Update service
            Route::delete('{service}', [ServiceController::class, 'destroy']); // Delete service
        });
    });

    // Bookings API
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index']); // List bookings
        Route::post('/', [BookingController::class, 'store']); // Create booking
        Route::get('statistics', [BookingController::class, 'statistics']); // Get booking statistics
        Route::get('{booking}', [BookingController::class, 'show']); // Get single booking
        Route::put('{booking}', [BookingController::class, 'update']); // Update booking
        Route::post('{booking}/cancel', [BookingController::class, 'cancel']); // Cancel booking
        
        // Merchant-only routes
        Route::middleware('role:Merchant')->group(function () {
            Route::post('{booking}/confirm', [BookingController::class, 'confirm']); // Confirm booking
            Route::post('{booking}/complete', [BookingController::class, 'complete']); // Complete booking
        });
    });
});

// Public API routes (no authentication required)
Route::prefix('public')->group(function () {
    // Public services
    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceController::class, 'index']); // List public services
        Route::get('categories', [ServiceController::class, 'categories']); // Get service categories
        Route::get('featured', [ServiceController::class, 'featured']); // Get featured services
        Route::get('search', [ServiceController::class, 'search']); // Search services
        Route::get('{service}', [ServiceController::class, 'show']); // Get single service
    });
});
