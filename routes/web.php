<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\AnalyticsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // Get featured services for homepage
    $services = \App\Models\Service::where('is_featured', true)
                                  ->orWhere('is_active', true)
                                  ->take(3)
                                  ->get();
    
    // If no services exist, provide demo data
    if ($services->isEmpty()) {
        $services = collect([
            [
                'name' => 'تنظيم الحفلات',
                'price' => 5000,
                'image' => '🎉',
                'badge' => 'شائع',
                'features' => ['تخطيط شامل', 'ديكورات فاخرة', 'تنسيق الطعام']
            ],
            [
                'name' => 'الخدمات التقنية',
                'price' => 3000,
                'image' => '💻',
                'badge' => 'متقدم',
                'features' => ['أجهزة صوتية', 'شاشات عرض', 'دعم تقني']
            ],
            [
                'name' => 'التصوير والإنتاج',
                'price' => 8000,
                'image' => '📸',
                'badge' => 'محترف',
                'features' => ['كاميرات 4K', 'مونتاج احترافي', 'فريق متخصص']
            ]
        ]);
    }
    
    return view('home', compact('services'));
});

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/booking/{service_id}', [BookingController::class, 'create'])->name('booking.create');

// Public Booking Routes - صفحات الحجز العامة للتجار
Route::prefix('merchant/{merchant}')->group(function () {
    Route::get('/', [PublicBookingController::class, 'show'])->name('merchant.booking');
    Route::get('/service/{service}', [PublicBookingController::class, 'service'])->name('merchant.service.booking');
    Route::post('/service/{service}/book', [PublicBookingController::class, 'book'])->name('merchant.book');
    Route::get('/booking/{booking}/confirmation', [PublicBookingController::class, 'confirmation'])->name('merchant.booking.confirmation');
});

// Booking Confirmation
Route::get('/booking/confirmation/{bookingNumber}', [PublicBookingController::class, 'confirmation'])
    ->name('booking.confirmation');

Route::get('/cart', function () {
    return view('cart');
})->middleware(['auth'])->name('cart.index');

Route::get('/checkout', function () {
    return view('checkout');
})->middleware(['auth'])->name('checkout.index');

Route::get('/checkout/confirmation/{reservation}', function (App\Models\PaidReservation $reservation) {
    // Add authorization check to ensure only the user who made the reservation can see this page
    if ($reservation->user_id !== auth()->id()) {
        abort(403);
    }
    return view('checkout-confirmation', ['reservation' => $reservation]);
})->middleware(['auth'])->name('checkout.confirmation');

Route::get('/dashboard', function () {
    $user = Auth::user();
    
    // إعادة توجيه حسب نوع المستخدم
    switch ($user->user_type) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'merchant':
            return redirect()->route('merchant.dashboard');
        case 'customer':
            return redirect()->route('customer.dashboard');
        case 'partner':
            return redirect()->route('partner.dashboard');
        default:
            // Dummy data for now. Later, this will be a real query.
            $bookings = collect([
                (object)['service_name' => 'Gourmet Catering', 'booking_date' => now()->addDays(10), 'status' => 'Confirmed'],
                (object)['service_name' => 'Luxury Wedding Hall', 'booking_date' => now()->subDays(5), 'status' => 'Completed'],
            ]);
            return view('dashboard', ['bookings' => $bookings]);
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Dashboard Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dashboard\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/revenue-report', [App\Http\Controllers\Dashboard\AdminDashboardController::class, 'revenueReport'])->name('revenue-report');
    Route::get('/merchants-report', [App\Http\Controllers\Dashboard\AdminDashboardController::class, 'merchantsReport'])->name('merchants-report');
    Route::get('/partners-report', [App\Http\Controllers\Dashboard\AdminDashboardController::class, 'partnersReport'])->name('partners-report');
    Route::get('/services-analytics', [App\Http\Controllers\Dashboard\AdminDashboardController::class, 'servicesAnalytics'])->name('services-analytics');
});

// Merchant Dashboard Routes  
Route::middleware(['auth', 'verified'])->prefix('merchant')->name('merchant.dashboard.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'index'])->name('index');
    Route::get('/services', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'services'])->name('services');
    Route::get('/bookings', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'bookingDetails'])->name('booking-details');
    Route::patch('/bookings/{booking}/status', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'updateBookingStatus'])->name('update-booking-status');
    Route::get('/revenue-report', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'revenueReport'])->name('revenue-report');
    Route::get('/analytics', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'analytics'])->name('analytics');
    
    // Payment Gateway Settings
    Route::get('/payment-settings', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'paymentSettings'])->name('payment-settings');
    Route::post('/payment-gateway/{gateway}', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'updatePaymentGateway'])->name('update-payment-gateway');
    Route::post('/payment-gateway/{gateway}/test', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'testPaymentGateway'])->name('test-payment-gateway');
});

// Customer Routes - العملاء
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');
    
    // Services
    Route::get('/services', [App\Http\Controllers\Customer\ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [App\Http\Controllers\Customer\ServiceController::class, 'show'])->name('services.show');
    Route::post('/services/{service}/favorite', [App\Http\Controllers\Customer\ServiceController::class, 'addToFavorites'])->name('services.favorite');
    Route::delete('/services/{service}/favorite', [App\Http\Controllers\Customer\ServiceController::class, 'removeFromFavorites'])->name('services.unfavorite');
    
    // Bookings
    Route::get('/bookings', [App\Http\Controllers\Customer\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [App\Http\Controllers\Customer\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/services/{service}/book', [App\Http\Controllers\Customer\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/services/{service}/book', [App\Http\Controllers\Customer\BookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\Customer\BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{booking}/invoice', [App\Http\Controllers\Customer\BookingController::class, 'downloadInvoice'])->name('bookings.invoice');
    
    // AJAX endpoints for bookings
    Route::get('/services/{service}/slots', [App\Http\Controllers\Customer\BookingController::class, 'getAvailableSlots'])->name('services.slots');
    Route::post('/services/{service}/calculate-price', [App\Http\Controllers\Customer\BookingController::class, 'calculatePrice'])->name('services.calculate-price');
    
    // Payments
    Route::get('/bookings/{booking}/payment', [App\Http\Controllers\Customer\PaymentController::class, 'show'])->name('payments.show');
    Route::post('/bookings/{booking}/payment', [App\Http\Controllers\Customer\PaymentController::class, 'process'])->name('payments.process');
    Route::get('/bookings/{booking}/payment/success', [App\Http\Controllers\Customer\PaymentController::class, 'success'])->name('payments.success');
    Route::get('/bookings/{booking}/payment/failed', [App\Http\Controllers\Customer\PaymentController::class, 'failed'])->name('payments.failed');
    Route::get('/payments/history', [App\Http\Controllers\Customer\PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{payment}/receipt', [App\Http\Controllers\Customer\PaymentController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::post('/payments/{payment}/refund', [App\Http\Controllers\Customer\PaymentController::class, 'requestRefund'])->name('payments.refund');
});

// Customer Dashboard Routes
Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.dashboard.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'index'])->name('index');
    Route::get('/bookings', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'bookingDetails'])->name('booking-details');
    Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'cancelBooking'])->name('cancel-booking');
    Route::patch('/bookings/{booking}/reschedule', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'rescheduleRequest'])->name('reschedule-request');
    Route::patch('/bookings/{booking}/rate', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'rateService'])->name('rate-service');
    Route::get('/profile', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'profile'])->name('profile');
    Route::get('/bookings/{booking}/rebook', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'rebookService'])->name('rebook-service');
});

// Partner Dashboard Routes
Route::middleware(['auth', 'verified'])->prefix('partner')->name('partner.dashboard.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Dashboard\PartnerDashboardController::class, 'index'])->name('index');
    Route::get('/merchants', [App\Http\Controllers\Dashboard\PartnerDashboardController::class, 'merchants'])->name('merchants');
    Route::get('/merchants/{merchant}', [App\Http\Controllers\Dashboard\PartnerDashboardController::class, 'merchantDetails'])->name('merchant-details');
    Route::get('/commission-report', [App\Http\Controllers\Dashboard\PartnerDashboardController::class, 'commissionReport'])->name('commission-report');
    Route::get('/analytics', [App\Http\Controllers\Dashboard\PartnerDashboardController::class, 'analytics'])->name('analytics');
});

// Alternative route names for easier access
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/merchant/dashboard', [App\Http\Controllers\Dashboard\MerchantDashboardController::class, 'index'])->name('merchant.dashboard');
    Route::get('/customer/dashboard', [App\Http\Controllers\Dashboard\CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/partner/dashboard', [App\Http\Controllers\Dashboard\PartnerDashboardController::class, 'index'])->name('partner.dashboard');
});

// Reports Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Admin Reports
    Route::prefix('admin/reports')->middleware('role:admin')->group(function () {
        Route::get('/revenue', [App\Http\Controllers\ReportController::class, 'adminRevenueReport'])->name('admin.reports.revenue');
        Route::get('/merchant-performance', [App\Http\Controllers\ReportController::class, 'merchantPerformanceReport'])->name('admin.reports.merchant-performance');
    });
    
    // Merchant Reports
    Route::prefix('merchant/reports')->middleware('role:merchant')->group(function () {
        Route::get('/bookings', [App\Http\Controllers\ReportController::class, 'merchantBookingsReport'])->name('merchant.reports.bookings');
    });
    
    // Partner Reports
    Route::prefix('partner/reports')->middleware('role:partner')->group(function () {
        Route::get('/commission', [App\Http\Controllers\ReportController::class, 'partnerCommissionReport'])->name('partner.reports.commission');
    });
});

// Notifications Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
    Route::patch('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // New Advanced Notification Routes
    Route::post('/notifications/bulk-action', [App\Http\Controllers\NotificationController::class, 'bulkAction'])->name('notifications.bulk-action');
    Route::get('/notifications/preferences', function() {
        return view('notifications.preferences');
    })->name('notifications.preferences');
    Route::post('/notifications/preferences', [App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('notifications.update-preferences');
    
    // API endpoints for notifications
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('api.notifications.unread-count');
    Route::get('/api/notifications/recent', [App\Http\Controllers\NotificationController::class, 'getRecent'])->name('api.notifications.recent');
    Route::get('/api/notifications/realtime', [App\Http\Controllers\NotificationController::class, 'getRealtimeNotifications'])->name('notifications.realtime');
    
    // Admin notification management
    Route::prefix('admin/notifications')->middleware('role:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificationController::class, 'adminIndex'])->name('admin.notifications.index');
        Route::post('/send-bulk', [App\Http\Controllers\NotificationController::class, 'sendBulkNotification'])->name('admin.notifications.send-bulk');
    });
});

// Content Management Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Public content routes
    Route::get('/content', [App\Http\Controllers\ContentController::class, 'index'])->name('content.index');
    Route::get('/content/{slug}', [App\Http\Controllers\ContentController::class, 'show'])->name('content.show');
    Route::get('/search-content', [App\Http\Controllers\ContentController::class, 'searchContent'])->name('content.search');
    Route::get('/api/content/{slug}', [App\Http\Controllers\ContentController::class, 'getContent'])->name('api.content.get');
    
    // Admin content management
    Route::prefix('admin/content')->middleware('role:admin')->group(function () {
        Route::get('/', [App\Http\Controllers\ContentController::class, 'adminIndex'])->name('admin.content.index');
        Route::get('/create', [App\Http\Controllers\ContentController::class, 'create'])->name('admin.content.create');
        Route::post('/', [App\Http\Controllers\ContentController::class, 'store'])->name('admin.content.store');
        Route::get('/{slug}/edit', [App\Http\Controllers\ContentController::class, 'edit'])->name('admin.content.edit');
        Route::put('/{slug}', [App\Http\Controllers\ContentController::class, 'update'])->name('admin.content.update');
        Route::delete('/{slug}', [App\Http\Controllers\ContentController::class, 'destroy'])->name('admin.content.destroy');
        Route::post('/create-predefined', [App\Http\Controllers\ContentController::class, 'createPredefinedContent'])->name('admin.content.create-predefined');
    });
});

// Advanced Analytics Routes - نظام التحليلات المتقدم
Route::middleware(['auth', 'verified'])->prefix('analytics')->name('analytics.')->group(function () {
    // Main Analytics Dashboard
    Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('index');
    
    // Specialized Analytics Pages
    Route::get('/revenue', [App\Http\Controllers\AnalyticsController::class, 'revenue'])->name('revenue');
    Route::get('/customers', [App\Http\Controllers\AnalyticsController::class, 'customers'])->name('customers');
    Route::get('/merchants', [App\Http\Controllers\AnalyticsController::class, 'merchants'])->name('merchants');
    Route::get('/operations', [App\Http\Controllers\AnalyticsController::class, 'operations'])->name('operations');
    
    // Real-time Analytics APIs
    Route::get('/real-time', [App\Http\Controllers\AnalyticsController::class, 'realTimeData'])->name('real-time');
    Route::get('/predictive', [App\Http\Controllers\AnalyticsController::class, 'predictiveAnalytics'])->name('predictive');
    
    // Custom Query Builder
    Route::get('/custom-query', [App\Http\Controllers\AnalyticsController::class, 'customQuery'])->name('custom-query');
    Route::post('/custom-query/execute', [App\Http\Controllers\AnalyticsController::class, 'executeCustomQuery'])->name('custom-query.execute');
    
    // Export Functionality
    Route::get('/export', [App\Http\Controllers\AnalyticsController::class, 'export'])->name('export');
    Route::post('/export/schedule', [App\Http\Controllers\AnalyticsController::class, 'scheduleExport'])->name('export.schedule');
    
    // Dashboard Configuration
    Route::get('/dashboard/configure', [App\Http\Controllers\AnalyticsController::class, 'configureDashboard'])->name('dashboard.configure');
    Route::post('/dashboard/save-layout', [App\Http\Controllers\AnalyticsController::class, 'saveDashboardLayout'])->name('dashboard.save-layout');
    
    // Alert Management
    Route::post('/alerts/{alert}/dismiss', [App\Http\Controllers\AnalyticsController::class, 'dismissAlert'])->name('alerts.dismiss');
    Route::get('/alerts/configure', [App\Http\Controllers\AnalyticsController::class, 'configureAlerts'])->name('alerts.configure');
    Route::post('/alerts/save', [App\Http\Controllers\AnalyticsController::class, 'saveAlertSettings'])->name('alerts.save');
    
    // Performance Monitoring
    Route::get('/performance/monitor', [App\Http\Controllers\AnalyticsController::class, 'performanceMonitor'])->name('performance.monitor');
    Route::get('/performance/logs', [App\Http\Controllers\AnalyticsController::class, 'performanceLogs'])->name('performance.logs');
    
    // API Endpoints for Chart Data
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard-data', [App\Http\Controllers\AnalyticsController::class, 'getDashboardData'])->name('dashboard-data');
        Route::get('/revenue-data', [App\Http\Controllers\AnalyticsController::class, 'getRevenueData'])->name('revenue-data');
        Route::get('/customer-data', [App\Http\Controllers\AnalyticsController::class, 'getCustomerData'])->name('customer-data');
        Route::get('/merchant-data', [App\Http\Controllers\AnalyticsController::class, 'getMerchantData'])->name('merchant-data');
        Route::get('/operations-data', [App\Http\Controllers\AnalyticsController::class, 'getOperationsData'])->name('operations-data');
        Route::get('/chart-data/{type}', [App\Http\Controllers\AnalyticsController::class, 'getChartData'])->name('chart-data');
    });
    
    // Admin-only Advanced Features
    Route::middleware('role:admin')->group(function () {
        Route::get('/system-health', [App\Http\Controllers\AnalyticsController::class, 'systemHealth'])->name('system-health');
        Route::get('/advanced-reports', [App\Http\Controllers\AnalyticsController::class, 'advancedReports'])->name('advanced-reports');
        Route::post('/benchmark/run', [App\Http\Controllers\AnalyticsController::class, 'runBenchmark'])->name('benchmark.run');
        Route::get('/database-analytics', [App\Http\Controllers\AnalyticsController::class, 'databaseAnalytics'])->name('database-analytics');
    });
    
    // Role-based Access Control
    Route::middleware('role:merchant')->group(function () {
        Route::get('/merchant-specific', [App\Http\Controllers\AnalyticsController::class, 'merchantSpecificAnalytics'])->name('merchant-specific');
    });
    
    Route::middleware('role:partner')->group(function () {
        Route::get('/partner-analytics', [App\Http\Controllers\AnalyticsController::class, 'partnerAnalytics'])->name('partner-analytics');
    });
});

// Payment Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/payment/checkout/{booking}', function ($bookingId) {
        $booking = App\Models\Booking::findOrFail($bookingId);
        return view('payment.checkout', compact('booking'));
    })->name('payment.checkout');
    
    Route::post('/payment/webhook', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');
});

// This route will handle the redirect back from the payment gateway
Route::get('/payment/callback', [App\Http\Controllers\PaymentController::class, 'handleCallback'])->name('payment.callback');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
