<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getTitle(): string
    {
        return 'Customer Dashboard';
    }

    public function getHeading(): string
    {
        return 'Welcome Back!';
    }

    public function getSubheading(): ?string
    {
        return 'Manage your bookings and discover new services';
    }
}
