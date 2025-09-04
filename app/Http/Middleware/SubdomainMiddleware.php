<?php

namespace App\Http\Middleware;

use App\Models\Merchant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class SubdomainMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);
        
        if ($subdomain && $subdomain !== 'www') {
            $merchant = $this->findMerchantBySubdomain($subdomain);
            
            if ($merchant) {
                // Share merchant data with all views
                View::share('currentMerchant', $merchant);
                
                // Set merchant context in config
                Config::set('app.current_merchant', $merchant);
                
                // Add merchant to request
                $request->attributes->set('merchant', $merchant);
                
                return $next($request);
            }
            
            // If subdomain doesn't exist, redirect to main domain
            return redirect()->to($request->getScheme() . '://' . config('app.main_domain') . $request->getRequestUri());
        }
        
        return $next($request);
    }
    
    private function extractSubdomain($host)
    {
        $mainDomain = config('app.main_domain', 'localhost');
        
        // Remove main domain from host to get subdomain
        if (str_ends_with($host, '.' . $mainDomain)) {
            return str_replace('.' . $mainDomain, '', $host);
        }
        
        // For local development (like merchant.localhost:8000)
        if (str_contains($host, '.localhost')) {
            return explode('.', $host)[0];
        }
        
        return null;
    }
    
    private function findMerchantBySubdomain($subdomain)
    {
        return Merchant::where('subdomain', $subdomain)
            ->where('verification_status', 'approved')
            ->with('user')
            ->first();
    }
}