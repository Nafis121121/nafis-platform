<?php

namespace App\Filament\Resources\CustomerInteractions;

use App\Filament\Resources\CustomerInteractions\Pages\CreateCustomerInteraction;
use App\Filament\Resources\CustomerInteractions\Pages\EditCustomerInteraction;
use App\Filament\Resources\CustomerInteractions\Pages\ListCustomerInteractions;
use App\Filament\Resources\CustomerInteractions\Schemas\CustomerInteractionForm;
use App\Filament\Resources\CustomerInteractions\Tables\CustomerInteractionsTable;
use App\Models\CustomerInteraction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CustomerInteractionResource extends Resource
{
    protected static ?string $model = CustomerInteraction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CustomerInteractionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomerInteractionsTable::configure($table);
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
            'index' => ListCustomerInteractions::route('/'),
            'create' => CreateCustomerInteraction::route('/create'),
            'edit' => EditCustomerInteraction::route('/{record}/edit'),
        ];
    }
}
