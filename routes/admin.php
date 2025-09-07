<?php

use App\Http\Controllers\Admin\UserActivationController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
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

        // User Activation Management
        Route::get('users/pending', [UserActivationController::class, 'pending'])->name('users.pending');
        Route::get('users/{user}', [UserActivationController::class, 'show'])->name('users.show');
        
        // Merchant Activation
        Route::post('merchants/{user}/approve', [UserActivationController::class, 'approveMerchant'])->name('merchants.approve');
        Route::post('merchants/{user}/reject', [UserActivationController::class, 'rejectMerchant'])->name('merchants.reject');
        
        // Partner Activation
        Route::post('partners/{user}/approve', [UserActivationController::class, 'approvePartner'])->name('partners.approve');
        Route::post('partners/{user}/reject', [UserActivationController::class, 'rejectPartner'])->name('partners.reject');
        
        // User Suspension
        Route::post('users/{user}/suspend', [UserActivationController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/unsuspend', [UserActivationController::class, 'unsuspend'])->name('users.unsuspend');

        // Analytics & Reports
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Advanced Admin Analytics
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::get('/system-health', [AnalyticsController::class, 'systemHealth'])->name('system-health');
            Route::get('/advanced-reports', [AnalyticsController::class, 'advancedReports'])->name('advanced-reports');
            Route::post('/benchmark/run', [AnalyticsController::class, 'runBenchmark'])->name('benchmark.run');
            Route::get('/database-analytics', [AnalyticsController::class, 'databaseAnalytics'])->name('database-analytics');
            Route::get('/merchants', [AnalyticsController::class, 'merchants'])->name('merchants');
            Route::get('/revenue', [AnalyticsController::class, 'revenue'])->name('revenue');
            Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
            Route::get('/operations', [AnalyticsController::class, 'operations'])->name('operations');
            Route::get('/real-time', [AnalyticsController::class, 'realTimeData'])->name('real-time');
            Route::get('/predictive', [AnalyticsController::class, 'predictiveAnalytics'])->name('predictive');
            Route::get('/custom-query', [AnalyticsController::class, 'customQuery'])->name('custom-query');
            Route::post('/custom-query/execute', [AnalyticsController::class, 'executeCustomQuery'])->name('custom-query.execute');
            Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
            Route::post('/export/schedule', [AnalyticsController::class, 'scheduleExport'])->name('export.schedule');
            Route::get('/dashboard/configure', [AnalyticsController::class, 'configureDashboard'])->name('dashboard.configure');
            Route::post('/dashboard/save-layout', [AnalyticsController::class, 'saveDashboardLayout'])->name('dashboard.save-layout');
            Route::post('/alerts/{alert}/dismiss', [AnalyticsController::class, 'dismissAlert'])->name('alerts.dismiss');
            Route::get('/alerts/configure', [AnalyticsController::class, 'configureAlerts'])->name('alerts.configure');
            Route::post('/alerts/save', [AnalyticsController::class, 'saveAlertSettings'])->name('alerts.save');
            Route::get('/performance/monitor', [AnalyticsController::class, 'performanceMonitor'])->name('performance.monitor');
            Route::get('/performance/logs', [AnalyticsController::class, 'performanceLogs'])->name('performance.logs');

            // API Endpoints for Admin
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('dashboard-data');
                Route::get('/revenue-data', [AnalyticsController::class, 'getRevenueData'])->name('revenue-data');
                Route::get('/customer-data', [AnalyticsController::class, 'getCustomerData'])->name('customer-data');
                Route::get('/merchant-data', [AnalyticsController::class, 'getMerchantData'])->name('merchant-data');
                Route::get('/operations-data', [AnalyticsController::class, 'getOperationsData'])->name('operations-data');
                Route::get('/chart-data/{type}', [AnalyticsController::class, 'getChartData'])->name('chart-data');
            });
        });

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

        // Admin Chat & Messaging Routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
            Route::get('/conversations/{conversation}', [ChatController::class, 'getMessages'])->name('conversations.show');
            Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('conversations.messages.send');
            Route::post('/conversations/start', [ChatController::class, 'startConversation'])->name('conversations.start');
            Route::patch('/conversations/{conversation}/close', [ChatController::class, 'closeConversation'])->name('conversations.close');
            Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('messages.delete');

            // Admin Support Chat Management
            Route::prefix('support')->name('support.')->group(function () {
                Route::get('/', [ChatController::class, 'support'])->name('index');
                Route::post('/ticket', [ChatController::class, 'createSupportTicket'])->name('ticket.create');
            });
        });
    });
});
