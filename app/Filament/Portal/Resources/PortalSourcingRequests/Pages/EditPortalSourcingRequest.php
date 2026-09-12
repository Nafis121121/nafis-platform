<?php

namespace App\Filament\Portal\Resources\PortalSourcingRequests\Pages;

use App\Filament\Portal\Resources\PortalSourcingRequests\PortalSourcingRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortalSourcingRequest extends EditRecord
{
    protected static string $resource = PortalSourcingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
