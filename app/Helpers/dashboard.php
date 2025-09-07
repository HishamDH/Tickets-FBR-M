<?php

if (!function_exists('dashboard_route')) {
    function dashboard_route($userId) {
        $user = \App\Models\User::find($userId);
        if (!$user) return '/';

        switch ($user->user_type) {
            case 'admin':
                return '/admin/dashboard';
            case 'merchant':
                if ($user->merchant_status === 'approved') {
                    return '/merchant/dashboard';
                }
                return route('merchant.status');
            case 'customer':
                return '/customer/dashboard';
            case 'partner':
                return '/partner/dashboard';
            default:
                return '/';
        }
    }
}
