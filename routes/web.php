<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PublicBookingController;

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
    return view('welcome');
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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
