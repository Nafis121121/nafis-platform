<?php

namespace App\Filament\Resources\AiProductDrafts;

use App\Filament\Resources\AiProductDrafts\Pages\CreateAiProductDraft;
use App\Filament\Resources\AiProductDrafts\Pages\EditAiProductDraft;
use App\Filament\Resources\AiProductDrafts\Pages\ListAiProductDrafts;
use App\Filament\Resources\AiProductDrafts\Schemas\AiProductDraftForm;
use App\Filament\Resources\AiProductDrafts\Tables\AiProductDraftsTable;
use App\Models\AiProductDraft;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AiProductDraftResource extends Resource
{
    protected static ?string $model = AiProductDraft::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return AiProductDraftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiProductDraftsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiProductDrafts::route('/'),
            'create' => CreateAiProductDraft::route('/create'),
            'edit' => EditAiProductDraft::route('/{record}/edit'),
        ];
    }
}
