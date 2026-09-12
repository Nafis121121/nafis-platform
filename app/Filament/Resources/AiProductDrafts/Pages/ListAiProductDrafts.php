<?php

namespace App\Filament\Resources\AiProductDrafts\Pages;

use App\Filament\Resources\AiProductDrafts\AiProductDraftResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Services\AiProductImporterService;
use Filament\Resources\Pages\ListRecords;

class ListAiProductDrafts extends ListRecords
{
    protected static string $resource = AiProductDraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('extractWithAi')
                ->label('استخراج با هوش مصنوعی')
                ->form([\Filament\Forms\Components\TextInput::make('source_url')->label('آدرس منبع')->url()->required()])
                ->action(fn (array $data) => app(AiProductImporterService::class)->importFromUrl($data['source_url'], auth()->id())),
        ];
    }
}
