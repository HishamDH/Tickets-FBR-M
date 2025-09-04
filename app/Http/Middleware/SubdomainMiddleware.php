<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class SubdomainMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);
        
        if ($subdomain && $subdomain !== 'www' && $subdomain !== 'admin') {
            $merchant = $this->findMerchantBySubdomain($subdomain);
            
            if ($merchant) {
                // Share merchant data with all views
                View::share('currentMerchant', $merchant);
                View::share('merchantBranding', $merchant->branding ?? []);
                
                // Set merchant context in config
                Config::set('app.current_merchant', $merchant);
                Config::set('app.merchant_subdomain', $subdomain);
                
                // Add merchant to request
                $request->attributes->set('merchant', $merchant);
                $request->attributes->set('subdomain', $subdomain);
                
                // Apply merchant branding if exists
                $this->applyMerchantBranding($merchant);
                
                return $next($request);
            }
            
            // If subdomain doesn't exist, redirect to main domain with 404
            abort(404, 'المتجر غير موجود أو غير مفعل');
        }
        
        return $next($request);
    }
    
    private function extractSubdomain($host)
    {
        $mainDomain = config('app.main_domain', 'shobaktickets.com');
        
        // Remove port from host if exists
        $host = explode(':', $host)[0];
        
        // Remove main domain from host to get subdomain
        if (str_ends_with($host, '.' . $mainDomain)) {
            return str_replace('.' . $mainDomain, '', $host);
        }
        
        // For local development (like merchant.localhost)
        if (str_contains($host, '.localhost')) {
            return explode('.', $host)[0];
        }
        
        // For development with custom hosts
        if (str_contains($host, '.test') || str_contains($host, '.local')) {
            $parts = explode('.', $host);
            return $parts[0];
        }
        
        return null;
    }
    
    private function findMerchantBySubdomain($subdomain)
    {
        // Cache merchant data for 15 minutes to improve performance
        return Cache::remember("merchant.subdomain.{$subdomain}", 900, function () use ($subdomain) {
            return User::where('user_type', 'merchant')
                ->where('subdomain', $subdomain)
                ->where('status', 'active')
                ->with(['merchantProfile', 'services'])
                ->first();
        });
    }
    
    private function applyMerchantBranding($merchant)
    {
        if ($merchant->branding) {
            $branding = is_string($merchant->branding) 
                ? json_decode($merchant->branding, true) 
                : $merchant->branding;
                
            if ($branding) {
                // Apply custom colors
                if (isset($branding['primary_color'])) {
                    Config::set('theme.primary_color', $branding['primary_color']);
                }
                
                if (isset($branding['secondary_color'])) {
                    Config::set('theme.secondary_color', $branding['secondary_color']);
                }
                
                // Apply custom logo
                if (isset($branding['logo_url'])) {
                    Config::set('theme.logo_url', $branding['logo_url']);
                }
                
                // Apply custom theme
                if (isset($branding['theme'])) {
                    Config::set('theme.name', $branding['theme']);
                }
                
                View::share('merchantTheme', $branding);
            }
        }
    }
}