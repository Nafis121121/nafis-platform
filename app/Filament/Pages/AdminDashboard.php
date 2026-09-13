<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class AdminDashboard extends BaseDashboard
{
    protected static ?string $title = 'داشبورد مدیریت';
    protected static ?string $navigationLabel = 'داشبورد';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;
}
