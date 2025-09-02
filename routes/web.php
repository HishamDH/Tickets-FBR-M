<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Dashboard\UserDashboardController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\CustomerDashboardController as DashboardCustomerDashboardController;
use App\Http\Controllers\Dashboard\MerchantDashboardController;
use App\Http\Controllers\Dashboard\PartnerDashboardController;
use App\Http\Controllers\Frontend\BookingController as FrontendBookingController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\MerchantStorefrontController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupportController;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/merchants', [HomeController::class, 'merchants'])->name('merchants.index');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/merchant/{id}', [HomeController::class, 'merchantShow'])->name('merchant.show');

// Test Multi-Guard Authentication
Route::get('/test-guards', function () {
    return view('test-guards');
})->name('test.guards');

// Marketing Pages
Route::get('/pricing', function () {
    return view('frontend.pricing');
})->name('pricing');

Route::get('/features', function () {
    return view('frontend.features');
})->name('features');

// Shopping Cart API Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::put('/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [CartController::class, 'destroy'])->name('destroy');
    Route::delete('/', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
    Route::post('/merge', [CartController::class, 'merge'])->name('merge');
    Route::get('/validate', [CartController::class, 'validateCart'])->name('validate');
});

// Public Services Routes
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');

// Sitemap
Route::get('/sitemap', function () {
    return view('sitemap');
})->name('sitemap');

// Legacy home route
Route::get('/old-home', function () {
    $services = \App\Models\Service::where('is_featured', true)
        ->orWhere('is_active', true)
        ->take(3)
        ->get();

    if ($services->isEmpty()) {
        $services = collect([
            [
                'name' => 'تنظيم الحفلات',
                'price' => 5000,
                'image' => '🎉',
                'badge' => 'شائع',
                'features' => ['تخطيط شامل', 'ديكورات فاخرة', 'تنسيق الطعام'],
            ],
            [
                'name' => 'الخدمات التقنية',
                'price' => 3000,
                'image' => '💻',
                'badge' => 'متقدم',
                'features' => ['أجهزة صوتية', 'شاشات عرض', 'دعم تقني'],
            ],
            [
                'name' => 'التصوير والإنتاج',
                'price' => 8000,
                'image' => '📸',
                'badge' => 'محترف',
                'features' => ['كاميرات 4K', 'مونتاج احترافي', 'فريق متخصص'],
            ],
        ]);
    }

    return view('home', compact('services'));
});

// Public Booking Routes - صفحات الحجز العامة للتجار
Route::prefix('merchant/{merchant}')->group(function () {
    Route::get('/', [PublicBookingController::class, 'show'])->name('merchant.booking');
    Route::get('/service/{service}', [PublicBookingController::class, 'service'])->name('merchant.service.booking');
    Route::post('/service/{service}/book', [PublicBookingController::class, 'book'])->name('merchant.book');
    Route::get('/booking/{booking}/confirmation', [PublicBookingController::class, 'confirmation'])->name('merchant.booking.confirmation');
});

// Public Booking Confirmation
Route::get('/booking/confirmation/{bookingNumber}', [PublicBookingController::class, 'confirmation'])
    ->name('public.booking.confirmation');

// Customer Frontend Booking Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/book/{offering}', [FrontendBookingController::class, 'show'])->name('booking.show');
    Route::post('/book/{offering}', [FrontendBookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/{booking}/confirmation', [FrontendBookingController::class, 'confirmation'])->name('booking.confirmation');
    Route::patch('/booking/{booking}/cancel', [FrontendBookingController::class, 'cancel'])->name('booking.cancel');
});

// Checkout Routes
Route::middleware(['auth'])->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('confirmation');
    Route::get('/payment/stripe/{order}', [CheckoutController::class, 'stripePayment'])->name('payment.stripe');
    Route::post('/payment/stripe/{order}', [CheckoutController::class, 'processStripePayment'])->name('payment.stripe.process');
    Route::get('/payment/paypal/{order}', [CheckoutController::class, 'paypalPayment'])->name('payment.paypal');
    Route::post('/payment/paypal/{order}', [CheckoutController::class, 'processPaypalPayment'])->name('payment.paypal.process');
});

// User Dashboard Routes
Route::middleware(['auth'])->prefix('dashboard/user')->name('dashboard.user.')->group(function () {
    Route::get('/', [UserDashboardController::class, 'index'])->name('index');
    Route::get('/orders', [UserDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [UserDashboardController::class, 'orderShow'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [UserDashboardController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Legacy booking routes
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{service_id}', [BookingController::class, 'create'])->name('booking.create');
    
    Route::get('/cart', function () {
        return view('cart');
    })->name('cart.index');
});

// Main Dashboard Route (redirects based on role)
Route::get('/dashboard', function () {
    $user = Auth::user();

    switch ($user->role ?? $user->user_type) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'merchant':
            return redirect()->route('merchant.dashboard');
        case 'customer':
        case 'user':
            return redirect()->route('dashboard.user.index');
        case 'partner':
            return redirect()->route('partner.dashboard');
        default:
            $bookings = collect([
                (object) ['service_name' => 'Gourmet Catering', 'booking_date' => now()->addDays(10), 'status' => 'Confirmed'],
                (object) ['service_name' => 'Luxury Wedding Hall', 'booking_date' => now()->subDays(5), 'status' => 'Completed'],
            ]);
            return view('dashboard', ['bookings' => $bookings]);
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Dashboard Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/revenue-report', [AdminDashboardController::class, 'revenueReport'])->name('revenue-report');
    Route::get('/merchants-report', [AdminDashboardController::class, 'merchantsReport'])->name('merchants-report');
    Route::get('/partners-report', [AdminDashboardController::class, 'partnersReport'])->name('partners-report');
    Route::get('/services-analytics', [AdminDashboardController::class, 'servicesAnalytics'])->name('services-analytics');
});

// Merchant Dashboard Routes
Route::middleware(['auth', 'verified', 'role:merchant'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/dashboard', [MerchantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/services', [MerchantDashboardController::class, 'services'])->name('services');
    Route::get('/bookings', [MerchantDashboardController::class, 'bookings'])->name('bookings');
    Route::get('/bookings/{booking}', [MerchantDashboardController::class, 'bookingDetails'])->name('booking-details');
    Route::patch('/bookings/{booking}/status', [MerchantDashboardController::class, 'updateBookingStatus'])->name('update-booking-status');
    Route::get('/revenue-report', [MerchantDashboardController::class, 'revenueReport'])->name('revenue-report');
    Route::get('/analytics', [MerchantDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/payment-settings', [MerchantDashboardController::class, 'paymentSettings'])->name('payment-settings');
    Route::post('/payment-gateway/{gateway}', [MerchantDashboardController::class, 'updatePaymentGateway'])->name('update-payment-gateway');
    Route::post('/payment-gateway/{gateway}/test', [MerchantDashboardController::class, 'testPaymentGateway'])->name('test-payment-gateway');
});

// Withdrawal Management Routes
Route::middleware(['auth', 'verified', 'role:merchant'])->prefix('withdrawals')->name('withdrawals.')->group(function () {
    Route::get('/', [\App\Http\Controllers\WithdrawController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\WithdrawController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\WithdrawController::class, 'store'])->name('store');
    Route::get('/{withdrawal}', [\App\Http\Controllers\WithdrawController::class, 'show'])->name('show');
    Route::patch('/{withdrawal}/cancel', [\App\Http\Controllers\WithdrawController::class, 'cancel'])->name('cancel');
});

// Chat & Messaging Routes
Route::middleware(['auth', 'verified'])->prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
    Route::get('/conversations/{conversation}', [ChatController::class, 'getMessages'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage'])->name('conversations.messages.send');
    Route::post('/conversations/start', [ChatController::class, 'startConversation'])->name('conversations.start');
    Route::patch('/conversations/{conversation}/close', [ChatController::class, 'closeConversation'])->name('conversations.close');
    Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('messages.delete');
    
    // Support specific routes
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [ChatController::class, 'support'])->name('index');
        Route::post('/ticket', [ChatController::class, 'createSupportTicket'])->name('ticket.create');
    });
});

// Customer Dashboard Routes
Route::middleware(['auth', 'verified', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Services
    Route::get('/services', [CustomerServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{service}', [CustomerServiceController::class, 'show'])->name('services.show');
    Route::post('/services/{service}/favorite', [CustomerServiceController::class, 'addToFavorites'])->name('services.favorite');
    Route::delete('/services/{service}/favorite', [CustomerServiceController::class, 'removeFromFavorites'])->name('services.unfavorite');

    // Bookings
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::get('/services/{service}/book', [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/services/{service}/book', [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/{booking}/invoice', [CustomerBookingController::class, 'downloadInvoice'])->name('bookings.invoice');

    // AJAX endpoints for bookings
    Route::get('/services/{service}/slots', [CustomerBookingController::class, 'getAvailableSlots'])->name('services.slots');
    Route::post('/services/{service}/calculate-price', [CustomerBookingController::class, 'calculatePrice'])->name('services.calculate-price');

    // Payments
    Route::get('/bookings/{booking}/payment', [CustomerPaymentController::class, 'show'])->name('payments.show');
    Route::post('/bookings/{booking}/payment', [CustomerPaymentController::class, 'process'])->name('payments.process');
    Route::get('/bookings/{booking}/payment/success', [CustomerPaymentController::class, 'success'])->name('payments.success');
    Route::get('/bookings/{booking}/payment/failed', [CustomerPaymentController::class, 'failed'])->name('payments.failed');
    Route::get('/payments/history', [CustomerPaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{payment}/receipt', [CustomerPaymentController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::post('/payments/{payment}/refund', [CustomerPaymentController::class, 'requestRefund'])->name('payments.refund');

    // Profile management
    Route::get('/profile', [DashboardCustomerDashboardController::class, 'profile'])->name('profile');
    Route::get('/bookings/{booking}/rebook', [DashboardCustomerDashboardController::class, 'rebookService'])->name('rebook-service');
    Route::patch('/bookings/{booking}/reschedule', [DashboardCustomerDashboardController::class, 'rescheduleRequest'])->name('reschedule-request');
    Route::patch('/bookings/{booking}/rate', [DashboardCustomerDashboardController::class, 'rateService'])->name('rate-service');
});

// Partner Dashboard Routes
Route::middleware(['auth', 'verified', 'role:partner'])->prefix('partner')->name('partner.')->group(function () {
    Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/merchants', [PartnerDashboardController::class, 'merchants'])->name('merchants');
    Route::get('/merchants/{merchant}', [PartnerDashboardController::class, 'merchantDetails'])->name('merchant-details');
    Route::get('/commission-report', [PartnerDashboardController::class, 'commissionReport'])->name('commission-report');
    Route::get('/analytics', [PartnerDashboardController::class, 'analytics'])->name('analytics');
});

// Reports Routes
Route::middleware(['auth', 'verified'])->prefix('reports')->name('reports.')->group(function () {
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/revenue', [ReportController::class, 'adminRevenueReport'])->name('revenue');
        Route::get('/merchant-performance', [ReportController::class, 'merchantPerformanceReport'])->name('merchant-performance');
    });

    Route::middleware('role:merchant')->prefix('merchant')->name('merchant.')->group(function () {
        Route::get('/bookings', [ReportController::class, 'merchantBookingsReport'])->name('bookings');
    });

    Route::middleware('role:partner')->prefix('partner')->name('partner.')->group(function () {
        Route::get('/commission', [ReportController::class, 'partnerCommissionReport'])->name('commission');
    });
});

// Notifications Routes
Route::middleware(['auth', 'verified'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
    Route::patch('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-action', [NotificationController::class, 'bulkAction'])->name('bulk-action');
    
    Route::get('/preferences', function () {
        return view('notifications.preferences');
    })->name('preferences');
    Route::post('/preferences', [NotificationController::class, 'updatePreferences'])->name('update-preferences');

    // API endpoints
    Route::get('/api/unread-count', [NotificationController::class, 'getUnreadCount'])->name('api.unread-count');
    Route::get('/api/recent', [NotificationController::class, 'getRecent'])->name('api.recent');
    Route::get('/api/realtime', [NotificationController::class, 'getRealtimeNotifications'])->name('api.realtime');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [NotificationController::class, 'adminIndex'])->name('index');
        Route::post('/send-bulk', [NotificationController::class, 'sendBulkNotification'])->name('send-bulk');
    });
});

// Content Management Routes
Route::middleware(['auth', 'verified'])->prefix('content')->name('content.')->group(function () {
    Route::get('/', [ContentController::class, 'index'])->name('index');
    Route::get('/{slug}', [ContentController::class, 'show'])->name('show');
    Route::get('/search', [ContentController::class, 'searchContent'])->name('search');
    Route::get('/api/{slug}', [ContentController::class, 'getContent'])->name('api.get');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [ContentController::class, 'adminIndex'])->name('index');
        Route::get('/create', [ContentController::class, 'create'])->name('create');
        Route::post('/', [ContentController::class, 'store'])->name('store');
        Route::get('/{slug}/edit', [ContentController::class, 'edit'])->name('edit');
        Route::put('/{slug}', [ContentController::class, 'update'])->name('update');
        Route::delete('/{slug}', [ContentController::class, 'destroy'])->name('destroy');
        Route::post('/create-predefined', [ContentController::class, 'createPredefinedContent'])->name('create-predefined');
    });
});

// Support Routes
Route::middleware(['auth', 'verified'])->prefix('support')->name('support.')->group(function () {
    Route::get('/', [SupportController::class, 'index'])->name('index');
    Route::get('/create', [SupportController::class, 'create'])->name('create');
    Route::post('/', [SupportController::class, 'store'])->name('store');
    Route::get('/{support}', [SupportController::class, 'show'])->name('show');
    Route::delete('/{support}', [SupportController::class, 'destroy'])->name('destroy');
});

// POS System Routes
Route::middleware(['auth', 'verified', 'role:merchant'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PosController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [\App\Http\Controllers\PosController::class, 'index'])->name('index');
    Route::post('/sales', [\App\Http\Controllers\PosController::class, 'processDirectSale'])->name('sales.process');
    Route::get('/customer-lookup', [\App\Http\Controllers\PosController::class, 'customerLookup'])->name('customer.lookup');
    Route::post('/attendance-check', [\App\Http\Controllers\PosController::class, 'attendanceCheck'])->name('attendance.check');
    Route::get('/reports', [\App\Http\Controllers\PosController::class, 'reports'])->name('reports');
    Route::get('/sales-history', [\App\Http\Controllers\PosController::class, 'salesHistory'])->name('sales.history');
    Route::get('/daily-summary', [\App\Http\Controllers\PosController::class, 'dailySummary'])->name('daily.summary');
    
    // API routes for POS
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/services', [\App\Http\Controllers\PosController::class, 'getServices'])->name('services');
        Route::post('/validate-qr', [\App\Http\Controllers\PosController::class, 'validateQr'])->name('validate-qr');
        Route::get('/customers/search', [\App\Http\Controllers\PosController::class, 'searchCustomers'])->name('customers.search');
        Route::post('/customers', [\App\Http\Controllers\PosController::class, 'createCustomer'])->name('customers.create');
    });
});

// Analytics Routes
Route::middleware(['auth', 'verified'])->prefix('analytics')->name('analytics.')->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('index');
    Route::get('/revenue', [AnalyticsController::class, 'revenue'])->name('revenue');
    Route::get('/customers', [AnalyticsController::class, 'customers'])->name('customers');
    Route::get('/merchants', [AnalyticsController::class, 'merchants'])->name('merchants');
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

    // API Endpoints
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/dashboard-data', [AnalyticsController::class, 'getDashboardData'])->name('dashboard-data');
        Route::get('/revenue-data', [AnalyticsController::class, 'getRevenueData'])->name('revenue-data');
        Route::get('/customer-data', [AnalyticsController::class, 'getCustomerData'])->name('customer-data');
        Route::get('/merchant-data', [AnalyticsController::class, 'getMerchantData'])->name('merchant-data');
        Route::get('/operations-data', [AnalyticsController::class, 'getOperationsData'])->name('operations-data');
        Route::get('/chart-data/{type}', [AnalyticsController::class, 'getChartData'])->name('chart-data');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/system-health', [AnalyticsController::class, 'systemHealth'])->name('system-health');
        Route::get('/advanced-reports', [AnalyticsController::class, 'advancedReports'])->name('advanced-reports');
        Route::post('/benchmark/run', [AnalyticsController::class, 'runBenchmark'])->name('benchmark.run');
        Route::get('/database-analytics', [AnalyticsController::class, 'databaseAnalytics'])->name('database-analytics');
    });

    Route::middleware('role:merchant')->group(function () {
        Route::get('/merchant-specific', [AnalyticsController::class, 'merchantSpecificAnalytics'])->name('merchant-specific');
    });

    Route::middleware('role:partner')->group(function () {
        Route::get('/partner-analytics', [AnalyticsController::class, 'partnerAnalytics'])->name('partner-analytics');
    });
});

// Payment Routes
Route::middleware(['auth'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/checkout/{booking}', function ($bookingId) {
        $booking = Booking::findOrFail($bookingId);
        return view('payment.checkout', compact('booking'));
    })->name('checkout');

    Route::post('/webhook', [PaymentController::class, 'webhook'])->name('webhook');
    Route::get('/callback', [PaymentController::class, 'handleCallback'])->name('callback');
});

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Merchant Storefronts - Public Routes (should be last to avoid conflicts)
Route::get('/merchants/directory', [MerchantStorefrontController::class, 'search'])->name('merchants.directory');
Route::get('/store/{slug}', [MerchantStorefrontController::class, 'show'])->name('storefront.show');
Route::get('/store/{slug}/contact', [MerchantStorefrontController::class, 'contact'])->name('storefront.contact');
Route::post('/store/{slug}/contact', [MerchantStorefrontController::class, 'contact'])->name('storefront.contact.submit');
Route::get('/store/{slug}/offering/{offering}', [MerchantStorefrontController::class, 'showOffering'])->name('storefront.offering');
Route::get('/api/merchant/{slug}/info', [MerchantStorefrontController::class, 'apiInfo'])->name('api.merchant.info');

require __DIR__.'/auth.php';
