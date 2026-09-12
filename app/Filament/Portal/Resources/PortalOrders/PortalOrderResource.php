<?php

namespace App\Filament\Portal\Resources\PortalOrders;

use App\Filament\Portal\Resources\PortalOrders\Pages\ListPortalOrders;
use App\Filament\Portal\Resources\PortalOrders\Schemas\PortalOrderForm;
use App\Filament\Portal\Resources\PortalOrders\Tables\PortalOrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortalOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isRole('customer') ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PortalOrderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortalOrdersTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
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
            'index' => ListPortalOrders::route('/'),
        ];
    }
}
