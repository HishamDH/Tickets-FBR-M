<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\MerchantLoginController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Merchant\BookingController;
use App\Http\Controllers\Dashboard\MerchantDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Merchant\PaymentController;
use App\Http\Controllers\Merchant\ServiceController;
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

// Merchant Authentication Routes - prefix and name already set in RouteServiceProvider

// Merchant Login Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [MerchantLoginController::class, 'create'])->name('login');
    Route::post('login', [MerchantLoginController::class, 'store']);
    Route::get('register', [App\Http\Controllers\Auth\MerchantRegisterController::class, 'create'])->name('register');
    Route::post('register', [App\Http\Controllers\Auth\MerchantRegisterController::class, 'store']);
});

// Merchant Status Page (for pending approval)
Route::get('status', function () {
    return view('auth.merchant.status');
})->name('status');

// Merchant Protected Routes
Route::middleware(['auth:web', 'merchant.status'])->group(function () {
    // Logout
    Route::post('logout', [MerchantLoginController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/', [MerchantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('dashboard.index');

    // Test route for debugging
    Route::get('/test-service-form', function () {
        return view('test-service-form');
    })->name('test.service.form');

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

        // Branding & Subdomain Management
        Route::prefix('branding')->name('branding.')->group(function () {
            Route::get('/', [App\Http\Controllers\Merchant\BrandingController::class, 'index'])->name('index');
            Route::post('/subdomain', [App\Http\Controllers\Merchant\BrandingController::class, 'updateSubdomain'])->name('subdomain.update');
            Route::post('/branding', [App\Http\Controllers\Merchant\BrandingController::class, 'updateBranding'])->name('update');
            Route::post('/social-links', [App\Http\Controllers\Merchant\BrandingController::class, 'updateSocialLinks'])->name('social-links.update');
            Route::post('/business-hours', [App\Http\Controllers\Merchant\BrandingController::class, 'updateBusinessHours'])->name('business-hours.update');
            Route::post('/seo', [App\Http\Controllers\Merchant\BrandingController::class, 'updateSeo'])->name('seo.update');
            Route::post('/toggle-store', [App\Http\Controllers\Merchant\BrandingController::class, 'toggleStore'])->name('toggle-store');
            Route::get('/preview', [App\Http\Controllers\Merchant\BrandingController::class, 'preview'])->name('preview');
            Route::get('/check-subdomain', [App\Http\Controllers\Merchant\BrandingController::class, 'checkSubdomain'])->name('check-subdomain');
        });

        // Staff Management System
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/', [App\Http\Controllers\Merchant\StaffController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Merchant\StaffController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Merchant\StaffController::class, 'store'])->name('store');
            Route::get('/{staff}', [App\Http\Controllers\Merchant\StaffController::class, 'show'])->name('show');
            Route::get('/{staff}/edit', [App\Http\Controllers\Merchant\StaffController::class, 'edit'])->name('edit');
            Route::put('/{staff}', [App\Http\Controllers\Merchant\StaffController::class, 'update'])->name('update');
            Route::delete('/{staff}', [App\Http\Controllers\Merchant\StaffController::class, 'destroy'])->name('destroy');
            
            // Scheduling
            Route::get('/schedules/manage', [App\Http\Controllers\Merchant\StaffController::class, 'schedules'])->name('schedules');
            Route::post('/shifts', [App\Http\Controllers\Merchant\StaffController::class, 'storeShift'])->name('shifts.store');
            
            // Clock in/out
            Route::post('/{staff}/clock-in', [App\Http\Controllers\Merchant\StaffController::class, 'clockIn'])->name('clock-in');
            Route::post('/{staff}/clock-out', [App\Http\Controllers\Merchant\StaffController::class, 'clockOut'])->name('clock-out');
            
            // Reports and analytics
            Route::get('/reports/performance', [App\Http\Controllers\Merchant\StaffController::class, 'reports'])->name('reports');
            Route::post('/reports/export', [App\Http\Controllers\Merchant\StaffController::class, 'exportReport'])->name('reports.export');
            
            // Search
            Route::get('/search/employees', [App\Http\Controllers\Merchant\StaffController::class, 'search'])->name('search');
        });

        // Venue Layout Management
        Route::prefix('venue-layout')->name('venue-layout.')->group(function () {
            Route::get('/{venueLayout}/designer', [App\Http\Controllers\Merchant\VenueLayoutController::class, 'designer'])->name('designer');
            Route::post('/{venueLayout}/update', [App\Http\Controllers\Merchant\VenueLayoutController::class, 'updateLayout'])->name('update');
            Route::get('/{venueLayout}/data', [App\Http\Controllers\Merchant\VenueLayoutController::class, 'getLayoutData'])->name('data');
            Route::get('/{venueLayout}/preview', [App\Http\Controllers\Merchant\VenueLayoutController::class, 'preview'])->name('preview');
        });

        // POS System
        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('/', [App\Http\Controllers\PosController::class, 'index'])->name('index');
            Route::get('/dashboard', [App\Http\Controllers\PosController::class, 'index'])->name('dashboard');
            Route::post('/process-payment', [App\Http\Controllers\PosController::class, 'processPayment'])->name('process-payment');
            Route::post('/sales', [App\Http\Controllers\PosController::class, 'processDirectSale'])->name('sales.process');
            Route::get('/customer-lookup', [App\Http\Controllers\PosController::class, 'customerLookup'])->name('customer.lookup');
            Route::post('/attendance-check', [App\Http\Controllers\PosController::class, 'attendanceCheck'])->name('attendance.check');
            Route::get('/reports', [App\Http\Controllers\PosController::class, 'reports'])->name('reports');
            Route::get('/sales-history', [App\Http\Controllers\PosController::class, 'salesHistory'])->name('sales.history');
            Route::get('/daily-summary', [App\Http\Controllers\PosController::class, 'dailySummary'])->name('daily.summary');
            Route::get('/analytics', [App\Http\Controllers\PosController::class, 'analytics'])->name('analytics');
            
            // API routes for POS
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/services', [App\Http\Controllers\PosController::class, 'getServices'])->name('services');
                Route::post('/validate-qr', [App\Http\Controllers\PosController::class, 'validateQr'])->name('validate-qr');
                Route::get('/customers/search', [App\Http\Controllers\PosController::class, 'searchCustomers'])->name('customers.search');
                Route::post('/customers', [App\Http\Controllers\PosController::class, 'createCustomer'])->name('customers.create');
            });
            
            // Printing routes
            Route::post('/print/ticket/{reservation}', [App\Http\Controllers\PosController::class, 'printTicket'])->name('print.ticket');
            Route::post('/print/receipt/{reservation}', [App\Http\Controllers\PosController::class, 'printReceipt'])->name('print.receipt');
            Route::post('/print/batch', [App\Http\Controllers\PosController::class, 'batchPrint'])->name('print.batch');
            Route::post('/print/daily-report', [App\Http\Controllers\PosController::class, 'printDailyReport'])->name('print.daily-report');
            Route::post('/print/test', [App\Http\Controllers\PosController::class, 'testPrinter'])->name('print.test');
            Route::post('/cash-drawer/open', [App\Http\Controllers\PosController::class, 'openCashDrawer'])->name('cash-drawer.open');
            
            // Offline mode routes
            Route::post('/offline/store', [App\Http\Controllers\PosController::class, 'storeOfflineTransaction'])->name('offline.store');
            Route::post('/offline/sync', [App\Http\Controllers\PosController::class, 'syncOfflineTransactions'])->name('offline.sync');
            Route::get('/offline/stats', [App\Http\Controllers\PosController::class, 'getOfflineStats'])->name('offline.stats');
            Route::get('/offline/transactions', [App\Http\Controllers\PosController::class, 'getOfflineTransactions'])->name('offline.transactions');
            Route::delete('/offline/clear-synced', [App\Http\Controllers\PosController::class, 'clearSyncedTransactions'])->name('offline.clear-synced');
            Route::post('/offline/export', [App\Http\Controllers\PosController::class, 'exportOfflineData'])->name('offline.export');
            Route::get('/offline/download/{filename}', [App\Http\Controllers\PosController::class, 'downloadOfflineExport'])->name('download-offline-export');
            Route::get('/connection/status', [App\Http\Controllers\PosController::class, 'checkConnectionStatus'])->name('connection.status');
        });

        // Withdrawal Management - Merchant Financial Wallet
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [App\Http\Controllers\WithdrawController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\WithdrawController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\WithdrawController::class, 'store'])->name('store');
            Route::get('/{withdrawal}', [App\Http\Controllers\WithdrawController::class, 'show'])->name('show');
            Route::patch('/{withdrawal}/cancel', [App\Http\Controllers\WithdrawController::class, 'cancel'])->name('cancel');
        });

        // Merchant Analytics & Reports
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::get('/revenue', [AnalyticsController::class, 'revenue'])->name('revenue');
            Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
            Route::get('/operations', [AnalyticsController::class, 'operations'])->name('operations');
            Route::get('/real-time', [AnalyticsController::class, 'realTimeData'])->name('real-time');
            Route::get('/predictive', [AnalyticsController::class, 'predictiveAnalytics'])->name('predictive');
            Route::get('/merchant-specific', [AnalyticsController::class, 'merchantSpecificAnalytics'])->name('merchant-specific');
            Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
            Route::post('/export/schedule', [AnalyticsController::class, 'scheduleExport'])->name('export.schedule');
            Route::get('/dashboard/configure', [AnalyticsController::class, 'configureDashboard'])->name('dashboard.configure');
            Route::post('/dashboard/save-layout', [AnalyticsController::class, 'saveDashboardLayout'])->name('dashboard.save-layout');
            Route::post('/alerts/{alert}/dismiss', [AnalyticsController::class, 'dismissAlert'])->name('alerts.dismiss');
            Route::get('/alerts/configure', [AnalyticsController::class, 'configureAlerts'])->name('alerts.configure');
            Route::post('/alerts/save', [AnalyticsController::class, 'saveAlertSettings'])->name('alerts.save');
            Route::get('/performance/monitor', [AnalyticsController::class, 'performanceMonitor'])->name('performance.monitor');
            Route::get('/performance/logs', [AnalyticsController::class, 'performanceLogs'])->name('performance.logs');

            // API Endpoints
            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('dashboard-data');
                Route::get('/revenue-data', [AnalyticsController::class, 'getRevenueData'])->name('revenue-data');
                Route::get('/customer-data', [AnalyticsController::class, 'getCustomerData'])->name('customer-data');
                Route::get('/merchant-data', [AnalyticsController::class, 'getMerchantData'])->name('merchant-data');
                Route::get('/operations-data', [AnalyticsController::class, 'getOperationsData'])->name('operations-data');
                Route::get('/chart-data/{type}', [AnalyticsController::class, 'getChartData'])->name('chart-data');
            });
        });

        // Merchant Chat & Messaging Routes
        Route::prefix('chat')->name('chat.')->group(function () {
            Route::get('/', [ChatController::class, 'index'])->name('index');
            Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
            Route::get('/conversations/{conversation}', [ChatController::class, 'getMessages'])->name('conversations.show');
            Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('conversations.messages.send');
            Route::post('/conversations/start', [ChatController::class, 'startConversation'])->name('conversations.start');
            Route::patch('/conversations/{conversation}/close', [ChatController::class, 'closeConversation'])->name('conversations.close');
            Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('messages.delete');

            // Merchant Support Chat
            Route::prefix('support')->name('support.')->group(function () {
                Route::get('/', [ChatController::class, 'support'])->name('index');
                Route::post('/ticket', [ChatController::class, 'createSupportTicket'])->name('ticket.create');
            });
        });
    });
