<?php

use App\Http\Controllers\SubdomainStorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subdomain Routes
|--------------------------------------------------------------------------
|
| These routes handle merchant subdomain storefronts
| Pattern: {merchant}.domain.com
|
*/

Route::domain('{subdomain}.' . config('app.main_domain'))
    ->middleware(['web', 'subdomain'])
    ->group(function () {
        
        // Merchant Storefront Homepage
        Route::get('/', [SubdomainStorefrontController::class, 'index'])
            ->name('subdomain.home');
        
        // Services listing
        Route::get('/services', [SubdomainStorefrontController::class, 'services'])
            ->name('subdomain.services');
        
        // Individual service page
        Route::get('/service/{service}', [SubdomainStorefrontController::class, 'service'])
            ->name('subdomain.service');
        
        // Booking process
        Route::get('/book/{service}', [SubdomainStorefrontController::class, 'bookingForm'])
            ->name('subdomain.booking.form');
        
        Route::post('/book/{service}', [SubdomainStorefrontController::class, 'processBooking'])
            ->name('subdomain.booking.process');
        
        // Booking confirmation
        Route::get('/booking/{booking}/confirmation', [SubdomainStorefrontController::class, 'bookingConfirmation'])
            ->name('subdomain.booking.confirmation');
        
        // About the merchant
        Route::get('/about', [SubdomainStorefrontController::class, 'about'])
            ->name('subdomain.about');
        
        // Contact the merchant
        Route::get('/contact', [SubdomainStorefrontController::class, 'contact'])
            ->name('subdomain.contact');
        
        Route::post('/contact', [SubdomainStorefrontController::class, 'submitContact'])
            ->name('subdomain.contact.submit');
        
        // Gallery
        Route::get('/gallery', [SubdomainStorefrontController::class, 'gallery'])
            ->name('subdomain.gallery');
        
        // Reviews and ratings
        Route::get('/reviews', [SubdomainStorefrontController::class, 'reviews'])
            ->name('subdomain.reviews');
        
        // Live chat (if authenticated)
        Route::middleware(['auth'])->group(function () {
            Route::get('/chat', [SubdomainStorefrontController::class, 'chat'])
                ->name('subdomain.chat');
                
            Route::post('/chat/message', [SubdomainStorefrontController::class, 'sendMessage'])
                ->name('subdomain.chat.send');
        });
        
        // API endpoints for the subdomain
        Route::prefix('api')->name('subdomain.api.')->group(function () {
            Route::get('/info', [SubdomainStorefrontController::class, 'apiInfo'])
                ->name('info');
            
            Route::get('/services', [SubdomainStorefrontController::class, 'apiServices'])
                ->name('services');
                
            Route::get('/availability/{service}', [SubdomainStorefrontController::class, 'apiAvailability'])
                ->name('availability');
        });
    });