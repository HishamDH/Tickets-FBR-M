<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\PartnerLoginController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Dashboard\PartnerDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Partner Routes
|--------------------------------------------------------------------------
|
| Here is where you can register partner routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "partner" middleware group. Now create something great!
|
*/

// Partner Authentication Routes - prefix and name already set in RouteServiceProvider

// Partner Login Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [PartnerLoginController::class, 'create'])->name('login');
    Route::post('login', [PartnerLoginController::class, 'store']);
    Route::get('register', [App\Http\Controllers\Auth\PartnerRegisterController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\PartnerRegisterController::class, 'store']);
});

// Partner Status Page (for pending approval)
Route::get('status', function () {
    return view('auth.partner.status');
})->name('status');

// Partner Protected Routes
Route::middleware(['auth', 'partner.status'])->group(function () {
        // Logout
        Route::post('logout', [PartnerLoginController::class, 'destroy'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');

        // Commission & Earnings Management
        Route::prefix('commissions')->name('commissions.')->group(function () {
            Route::get('/', function () {
                return view('partner.commissions.index');
            })->name('index');
            Route::get('/history', function () {
                return view('partner.commissions.history');
            })->name('history');
            Route::get('/reports', function () {
                return view('partner.commissions.reports');
            })->name('reports');
        });

        // Referral Management
        Route::prefix('referrals')->name('referrals.')->group(function () {
            Route::get('/', function () {
                return view('partner.referrals.index');
            })->name('index');
            Route::get('/create', function () {
                return view('partner.referrals.create');
            })->name('create');
            Route::post('/', function () {
                // Handle referral creation
            })->name('store');
            Route::get('/performance', function () {
                return view('partner.referrals.performance');
            })->name('performance');
        });

        // Partner Analytics & Reports
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [AnalyticsController::class, 'partnerAnalytics'])->name('index');
            Route::get('/commission', [AnalyticsController::class, 'partnerCommissionAnalytics'])->name('commission');
            Route::get('/referrals', [AnalyticsController::class, 'partnerReferralAnalytics'])->name('referrals');
            Route::get('/performance', [AnalyticsController::class, 'partnerPerformanceAnalytics'])->name('performance');
            Route::get('/export', [AnalyticsController::class, 'partnerExportData'])->name('export');

            // API Endpoints for Partner Analytics
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/dashboard-data', [AnalyticsController::class, 'getPartnerDashboardData'])->name('dashboard-data');
                Route::get('/commission-data', [AnalyticsController::class, 'getPartnerCommissionData'])->name('commission-data');
                Route::get('/referral-data', [AnalyticsController::class, 'getPartnerReferralData'])->name('referral-data');
                Route::get('/performance-data', [AnalyticsController::class, 'getPartnerPerformanceData'])->name('performance-data');
            });
        });

        // Withdrawals for Partners
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [App\Http\Controllers\WithdrawController::class, 'partnerIndex'])->name('index');
            Route::get('/create', [App\Http\Controllers\WithdrawController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\WithdrawController::class, 'store'])->name('store');
            Route::get('/{withdrawal}', [App\Http\Controllers\WithdrawController::class, 'show'])->name('show');
            Route::patch('/{withdrawal}/cancel', [App\Http\Controllers\WithdrawController::class, 'cancel'])->name('cancel');
        });

        // Support & Communication
        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [SupportController::class, 'partnerIndex'])->name('index');
            Route::get('/create', [SupportController::class, 'create'])->name('create');
            Route::post('/', [SupportController::class, 'store'])->name('store');
            Route::get('/{id}', [SupportController::class, 'show'])->name('show');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'partnerIndex'])->name('index');
            Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::delete('/clear', [NotificationController::class, 'clearAll'])->name('clear');
        });

        // Profile & Settings
        Route::get('/profile', function () {
            return view('partner.profile.index');
        })->name('profile.index');

        Route::get('/settings', function () {
            return view('partner.settings.index');
        })->name('settings.index');

        // Partner Marketing Tools
        Route::prefix('marketing')->name('marketing.')->group(function () {
            Route::get('/', function () {
                return view('partner.marketing.index');
            })->name('index');
            Route::get('/materials', function () {
                return view('partner.marketing.materials');
            })->name('materials');
            Route::get('/links', function () {
                return view('partner.marketing.links');
            })->name('links');
        });

        // Partner Chat & Messaging Routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
            Route::get('/conversations/{conversation}', [ChatController::class, 'getMessages'])->name('conversations.show');
            Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('conversations.messages.send');
            Route::post('/conversations/start', [ChatController::class, 'startConversation'])->name('conversations.start');
            Route::patch('/conversations/{conversation}/close', [ChatController::class, 'closeConversation'])->name('conversations.close');
            Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('messages.delete');

            // Partner Support Chat
            Route::prefix('support')->name('support.')->group(function () {
                Route::get('/', [ChatController::class, 'support'])->name('index');
                Route::post('/ticket', [ChatController::class, 'createSupportTicket'])->name('ticket.create');
            });
        });
    });
