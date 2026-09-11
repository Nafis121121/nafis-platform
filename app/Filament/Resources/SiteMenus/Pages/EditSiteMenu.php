<?php

namespace App\Filament\Resources\SiteMenus\Pages;

use App\Filament\Resources\SiteMenus\SiteMenuResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSiteMenu extends EditRecord
{
    protected static string $resource = SiteMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
