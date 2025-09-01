<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupportController;

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
});
