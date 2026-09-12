<?php

namespace App\Filament\Portal\Resources\PortalShipments\Pages;

use App\Filament\Portal\Resources\PortalShipments\PortalShipmentResource;
use Filament\Resources\Pages\ListRecords;

class ListPortalShipments extends ListRecords
{
    protected static string $resource = PortalShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
