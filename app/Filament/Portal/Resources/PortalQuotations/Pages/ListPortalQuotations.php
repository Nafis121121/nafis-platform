<?php

namespace App\Filament\Portal\Resources\PortalQuotations\Pages;

use App\Filament\Portal\Resources\PortalQuotations\PortalQuotationResource;
use Filament\Resources\Pages\ListRecords;

class ListPortalQuotations extends ListRecords
{
    protected static string $resource = PortalQuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
