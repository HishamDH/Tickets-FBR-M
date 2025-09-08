<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Services\SiteStatsService;
use Illuminate\Support\Facades\Auth;

class LayoutComposer
{
    protected $statsService;

    public function __construct(SiteStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $data = [
            'siteStats' => $this->statsService->getSiteStats(),
            'cartStats' => $this->statsService->getCartStats(),
            'notificationCount' => $this->statsService->getNotificationCount(),
            'popularServices' => $this->statsService->getPopularServices(),
            'recentActivity' => $this->statsService->getRecentActivity(),
        ];

        // Add user-specific stats and determine user type
        $data['userType'] = 'guest';
        $data['userStats'] = [];
        
        // Check customer guard first
        if (Auth::guard('customer')->check()) {
            $data['userStats'] = $this->statsService->getCustomerStats(Auth::guard('customer')->id());
            $data['userType'] = 'customer';
        } 
        // Check regular auth guard 
        elseif (Auth::check()) {
            $user = Auth::user();
            
            // Determine user type based on user_type field or current route
            if ($user->user_type === 'merchant' || request()->is('merchant/*')) {
                $data['userStats'] = $this->statsService->getMerchantStats($user->id);
                $data['userType'] = 'merchant';
            } elseif ($user->user_type === 'partner' || request()->is('partner/*')) {
                $data['userStats'] = $this->statsService->getPartnerStats($user->id);
                $data['userType'] = 'partner';
            } elseif ($user->user_type === 'admin' || request()->is('admin/*')) {
                $data['userStats'] = $this->statsService->getAdminStats($user->id);
                $data['userType'] = 'admin';
            } else {
                // Default based on route prefix
                $routePrefix = request()->segment(1);
                switch($routePrefix) {
                    case 'merchant':
                        $data['userType'] = 'merchant';
                        $data['userStats'] = $this->statsService->getMerchantStats($user->id);
                        break;
                    case 'partner':
                        $data['userType'] = 'partner';
                        $data['userStats'] = $this->statsService->getPartnerStats($user->id);
                        break;
                    case 'admin':
                        $data['userType'] = 'admin';
                        $data['userStats'] = $this->statsService->getAdminStats($user->id);
                        break;
                    default:
                        $data['userType'] = 'admin';
                        $data['userStats'] = $this->statsService->getSiteStats();
                }
            }
        }

        $view->with($data);
    }
}