<?php

namespace App\Filament\Merchant\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getTitle(): string
    {
        return 'Merchant Dashboard';
    }

    public function getHeading(): string
    {
        return 'Business Overview';
    }

    public function getSubheading(): ?string
    {
        return 'Manage your services, bookings, and analytics';
    }
}
