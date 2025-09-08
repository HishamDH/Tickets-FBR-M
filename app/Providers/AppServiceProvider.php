<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default locale from config
        $locale = config('app.locale', 'en');
        app()->setLocale($locale);
        
        // Set default timezone for PHP and Carbon to avoid timezone parsing errors
        try {
            date_default_timezone_set('Asia/Riyadh');
        } catch (\Exception $e) {
            // Fallback to UTC if Asia/Riyadh fails
            date_default_timezone_set('UTC');
        }
    }
}
