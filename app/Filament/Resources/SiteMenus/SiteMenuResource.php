<?php

namespace App\Filament\Resources\SiteMenus;

use App\Filament\Resources\SiteMenus\Pages\CreateSiteMenu;
use App\Filament\Resources\SiteMenus\Pages\EditSiteMenu;
use App\Filament\Resources\SiteMenus\Pages\ListSiteMenus;
use App\Filament\Resources\SiteMenus\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\SiteMenus\Schemas\SiteMenuForm;
use App\Filament\Resources\SiteMenus\Tables\SiteMenusTable;
use App\Models\SiteMenu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SiteMenuResource extends Resource
{
    protected static ?string $modelLabel = 'منو';

    protected static ?string $pluralModelLabel = 'منوهای سایت';

    protected static ?string $navigationLabel = 'منوهای سایت';

    protected static ?string $model = SiteMenu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return SiteMenuForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteMenusTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteMenus::route('/'),
            'create' => CreateSiteMenu::route('/create'),
            'edit' => EditSiteMenu::route('/{record}/edit'),
        ];
    }
}
