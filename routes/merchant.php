<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\MerchantLoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Dashboard\MerchantDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
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
            Route::post('/process-payment', [App\Http\Controllers\PosController::class, 'processPayment'])->name('process-payment');
            Route::get('/analytics', [App\Http\Controllers\PosController::class, 'analytics'])->name('analytics');
            
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
    });
});
