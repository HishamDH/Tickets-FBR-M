<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Determine the appropriate login route based on the request path
        $path = $request->path();
        
        if (str_starts_with($path, 'customer/') || str_starts_with($path, 'customer')) {
            return route('customer.login');
        }
        
        if (str_starts_with($path, 'merchant/') || str_starts_with($path, 'merchant')) {
            return route('merchant.login');
        }
        
        if (str_starts_with($path, 'partner/') || str_starts_with($path, 'partner')) {
            return route('partner.login');
        }
        
        if (str_starts_with($path, 'admin/') || str_starts_with($path, 'admin')) {
            return route('filament.admin.auth.login');
        }
        
        // Default to customer login for any other paths
        return route('customer.login');
    }
}
