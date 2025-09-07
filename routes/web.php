<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\CustomerDashboardController as DashboardCustomerDashboardController;
use App\Http\Controllers\Dashboard\MerchantDashboardController;
use App\Http\Controllers\Dashboard\PartnerDashboardController;
use App\Http\Controllers\Dashboard\UserDashboardController;
use App\Http\Controllers\Frontend\BookingController as FrontendBookingController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\MerchantStorefrontController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\WelcomeController;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Frontend Public Routes
Route::get('/', [WelcomeController::class, 'index'])->name('home'); // Changed from 'welcome' to 'home'
Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome'); // Keep welcome as alias
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

// Shopping Cart API Routes moved to routes/customer.php

// Public Services Routes
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');

// Review Routes moved to routes/customer.php

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

// Language Switcher Route
Route::get('/language/{lang}', [App\Http\Controllers\LanguageController::class, 'switchLang'])->name('language.switch');

// This route is moved to the end to avoid conflicts with merchant auth routes

// Public Booking Confirmation
Route::get('/booking/confirmation/{bookingNumber}', [PublicBookingController::class, 'confirmation'])
    ->name('public.booking.confirmation');

// Customer Frontend Booking Routes moved to routes/customer.php

// Checkout Routes moved to routes/customer.php



// Legacy booking routes
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/{service_id}', [BookingController::class, 'create'])->name('booking.create');

    Route::get('/cart', function () {
        return view('cart');
    })->name('cart.index');
});

// Dashboard routes are now user-type specific:
// /customer/dashboard, /merchant/dashboard, /partner/dashboard, /admin/dashboard
// No general /dashboard route exists

// Admin Dashboard Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/revenue-report', [AdminDashboardController::class, 'revenueReport'])->name('revenue-report');
    Route::get('/merchants-report', [AdminDashboardController::class, 'merchantsReport'])->name('merchants-report');
    Route::get('/partners-report', [AdminDashboardController::class, 'partnersReport'])->name('partners-report');
    Route::get('/services-analytics', [AdminDashboardController::class, 'servicesAnalytics'])->name('services-analytics');
});

// Merchant Dashboard Routes - Removed duplicate routes, using routes/merchant.php instead
// Route::middleware(['auth:merchant', 'verified'])->prefix('merchant')->name('merchant.')->group(function () {
//     Merchant routes are now handled in routes/merchant.php
// });

// Withdrawal Management Routes moved to routes/merchant.php

// Partner Dashboard Routes - Removed duplicate routes, using routes/partner.php instead
// Route::middleware(['auth', 'verified', 'role:partner'])->prefix('partner')->name('partner.')->group(function () {
//     Partner routes are now handled in routes/partner.php
// });

// Public Partner Invitation Routes (no auth required)
Route::prefix('invitation')->name('partner.invitation.')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\PartnerInvitationController::class, 'show'])->name('show');
    Route::post('/{token}/accept', [\App\Http\Controllers\PartnerInvitationController::class, 'accept'])->name('accept');
    Route::get('/success', [\App\Http\Controllers\PartnerInvitationController::class, 'success'])->name('success');
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

// POS System Routes moved to routes/merchant.php

// Analytics Routes moved to specific user-type files:
// - Merchant analytics moved to routes/merchant.php
// - Admin analytics moved to routes/admin.php
// - Partner analytics moved to routes/partner.php (to be created)

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

// Design Showcase
Route::get('/design-showcase', function () {
    return view('design-showcase');
})->name('design.showcase');

// Public Booking Routes - صفحات الحجز العامة للتجار (moved here to avoid auth route conflicts)
Route::prefix('merchant/{merchant}')->group(function () {
    Route::get('/', [PublicBookingController::class, 'show'])->name('merchant.booking');
    Route::get('/service/{service}', [PublicBookingController::class, 'service'])->name('merchant.service.booking');
    Route::post('/service/{service}/book', [PublicBookingController::class, 'book'])->name('merchant.book');
    Route::get('/booking/{booking}/confirmation', [PublicBookingController::class, 'confirmation'])->name('merchant.booking.confirmation');
});

// Merchant Storefronts - Public Routes (should be last to avoid conflicts)
Route::get('/merchants/directory', [MerchantStorefrontController::class, 'search'])->name('merchants.directory');
Route::get('/store/{slug}', [MerchantStorefrontController::class, 'show'])->name('storefront.show');
Route::get('/store/{slug}/contact', [MerchantStorefrontController::class, 'contact'])->name('storefront.contact');
Route::post('/store/{slug}/contact', [MerchantStorefrontController::class, 'contact'])->name('storefront.contact.submit');
Route::get('/store/{slug}/offering/{offering}', [MerchantStorefrontController::class, 'showOffering'])->name('storefront.offering');
Route::get('/api/merchant/{slug}/info', [MerchantStorefrontController::class, 'apiInfo'])->name('api.merchant.info');

require __DIR__.'/auth.php';
