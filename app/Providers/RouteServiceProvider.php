<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Auth Routes (important to load these first)
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            // Admin Routes
            Route::middleware(['web'])
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            // Merchant Routes (load before web.php to avoid conflicts)
            Route::middleware(['web'])
                ->prefix('merchant')
                ->name('merchant.')
                ->group(base_path('routes/merchant.php'));

            // Customer Routes
            Route::middleware(['web'])
                ->prefix('customer')
                ->name('customer.')
                ->group(base_path('routes/customer.php'));

            // Partner Routes
            Route::middleware(['web'])
                ->prefix('partner')
                ->name('partner.')
                ->group(base_path('routes/partner.php'));

            // Web Routes (load last to avoid conflicts with specific prefixed routes)
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Subdomain Routes
            if (file_exists(base_path('routes/subdomain.php'))) {
                require base_path('routes/subdomain.php');
            }
        });
    }
}
