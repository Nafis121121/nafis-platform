<?php

namespace App\Filament\Portal\Resources\PortalOrders\Pages;

use App\Filament\Portal\Resources\PortalOrders\PortalOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortalOrder extends EditRecord
{
    protected static string $resource = PortalOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
