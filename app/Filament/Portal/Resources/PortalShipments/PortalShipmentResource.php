<?php

namespace App\Filament\Portal\Resources\PortalShipments;

use App\Filament\Portal\Resources\PortalShipments\Pages\ListPortalShipments;
use App\Filament\Portal\Resources\PortalShipments\Schemas\PortalShipmentForm;
use App\Filament\Portal\Resources\PortalShipments\Tables\PortalShipmentsTable;
use App\Models\Shipment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortalShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;
    protected static ?string $modelLabel = 'محموله بار';
    protected static ?string $pluralModelLabel = 'محموله‌ها و رهگیری بار';
    protected static ?string $navigationLabel = 'محموله‌ها و رهگیری بار';
    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isRole('customer', 'super_admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PortalShipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortalShipmentsTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereHas('order', fn ($query) => $query->where('user_id', auth()->id()));
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
            'index' => ListPortalShipments::route('/'),
        ];
    }
}
