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

        // Add user-specific stats
        if (Auth::guard('customer')->check()) {
            $data['userStats'] = $this->statsService->getCustomerStats(Auth::guard('customer')->id());
            $data['userType'] = 'customer';
        } elseif (Auth::check()) {
            $user = Auth::user();
            if ($user->user_type === 'merchant' || $user->merchant_status === 'approved') {
                $data['userStats'] = $this->statsService->getMerchantStats($user->id);
                $data['userType'] = 'merchant';
            } else {
                $data['userStats'] = $this->statsService->getSiteStats();
                $data['userType'] = 'admin';
            }
        } else {
            $data['userStats'] = [];
            $data['userType'] = 'guest';
        }

        $view->with($data);
    }
}