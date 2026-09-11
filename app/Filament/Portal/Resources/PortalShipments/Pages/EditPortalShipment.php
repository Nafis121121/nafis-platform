<?php

namespace App\Filament\Portal\Resources\PortalShipments\Pages;

use App\Filament\Portal\Resources\PortalShipments\PortalShipmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortalShipment extends EditRecord
{
    protected static string $resource = PortalShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
