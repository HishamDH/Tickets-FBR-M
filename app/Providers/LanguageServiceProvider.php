<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LanguageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share language direction with all views
        View::composer('*', function ($view) {
            $view->with('direction', getLanguageDirection());
            $view->with('isRtl', isRtl());
            $view->with('currentLocale', App::getLocale());
            $view->with('availableLanguages', getAvailableLanguages());
        });
    }
}
