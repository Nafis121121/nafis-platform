<?php

namespace App\Filament\Portal\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class PortalDashboard extends BaseDashboard
{
    protected static ?string $title = 'داشبورد پرتال مشتریان';
    protected static ?string $navigationLabel = 'داشبورد';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;
}
