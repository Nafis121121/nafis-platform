<?php

namespace App\Filament\Resources\WholesaleRules;

use App\Filament\Resources\WholesaleRules\Pages\CreateWholesaleRule;
use App\Filament\Resources\WholesaleRules\Pages\EditWholesaleRule;
use App\Filament\Resources\WholesaleRules\Pages\ListWholesaleRules;
use App\Filament\Resources\WholesaleRules\Schemas\WholesaleRuleForm;
use App\Filament\Resources\WholesaleRules\Tables\WholesaleRulesTable;
use App\Models\WholesaleRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WholesaleRuleResource extends Resource
{
    protected static ?string $model = WholesaleRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WholesaleRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WholesaleRulesTable::configure($table);
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
            'index' => ListWholesaleRules::route('/'),
            'create' => CreateWholesaleRule::route('/create'),
            'edit' => EditWholesaleRule::route('/{record}/edit'),
        ];
    }
}
