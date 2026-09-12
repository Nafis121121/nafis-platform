<?php

namespace App\Filament\Portal\Resources\PortalQuotations\Pages;

use App\Filament\Portal\Resources\PortalQuotations\PortalQuotationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPortalQuotation extends EditRecord
{
    protected static string $resource = PortalQuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
