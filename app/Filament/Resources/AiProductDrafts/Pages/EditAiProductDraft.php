<?php

namespace App\Filament\Resources\AiProductDrafts\Pages;

use App\Filament\Resources\AiProductDrafts\AiProductDraftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiProductDraft extends EditRecord
{
    protected static string $resource = AiProductDraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
