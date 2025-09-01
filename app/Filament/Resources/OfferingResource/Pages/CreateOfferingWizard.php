<?php

namespace App\Filament\Resources\OfferingResource\Pages;

use App\Filament\Resources\OfferingResource;
use Filament\Resources\Pages\Page;

class CreateOfferingWizard extends Page
{
    protected static string $resource = OfferingResource::class;

    protected static string $view = 'filament.resources.offering-resource.pages.create-offering-wizard';

    public static function getNavigationLabel(): string
    {
        return 'معالج إنشاء عرض جديد';
    }
}
