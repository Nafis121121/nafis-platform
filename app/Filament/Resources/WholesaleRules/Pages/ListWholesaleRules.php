<?php

namespace App\Filament\Resources\WholesaleRules\Pages;

use App\Filament\Resources\WholesaleRules\WholesaleRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWholesaleRules extends ListRecords
{
    protected static string $resource = WholesaleRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
