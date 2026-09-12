<?php

namespace App\Filament\Resources\WholesaleRules\Pages;

use App\Filament\Resources\WholesaleRules\WholesaleRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWholesaleRule extends EditRecord
{
    protected static string $resource = WholesaleRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
