<?php

namespace App\Filament\Portal\Resources\PortalSourcingRequests\Pages;

use App\Filament\Portal\Resources\PortalSourcingRequests\PortalSourcingRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPortalSourcingRequests extends ListRecords
{
    protected static string $resource = PortalSourcingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
