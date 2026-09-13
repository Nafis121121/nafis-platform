<?php

namespace App\Filament\Portal\Resources\PortalOrders\Pages;

use App\Filament\Portal\Resources\PortalOrders\PortalOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListPortalOrders extends ListRecords
{
    protected static string $resource = PortalOrderResource::class;
    protected static ?string $title = 'سفارش‌های من';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
