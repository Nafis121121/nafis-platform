<?php

namespace App\Filament\Resources\SiteMenus\Pages;

use App\Filament\Resources\SiteMenus\SiteMenuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteMenus extends ListRecords
{
    protected static string $resource = SiteMenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
