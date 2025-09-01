<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament-panels::pages.dashboard';

    public function getTitle(): string
    {
        return 'Admin Dashboard';
    }

    public function getHeading(): string
    {
        return 'Welcome to Admin Dashboard';
    }

    public function getSubheading(): ?string
    {
        return 'Manage your tickets platform from here';
    }
}
