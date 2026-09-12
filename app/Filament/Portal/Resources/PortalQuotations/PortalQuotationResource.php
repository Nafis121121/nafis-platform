<?php

namespace App\Filament\Portal\Resources\PortalQuotations;

use App\Filament\Portal\Resources\PortalQuotations\Pages\ListPortalQuotations;
use App\Filament\Portal\Resources\PortalQuotations\Schemas\PortalQuotationForm;
use App\Filament\Portal\Resources\PortalQuotations\Tables\PortalQuotationsTable;
use App\Models\Quotation;
use App\Enums\QuotationStatus;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortalQuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isRole('customer') ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PortalQuotationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortalQuotationsTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->where('status', QuotationStatus::APPROVED);
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
            'index' => ListPortalQuotations::route('/'),
        ];
    }
}
