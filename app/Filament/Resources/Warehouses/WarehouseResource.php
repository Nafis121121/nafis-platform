<?php

namespace App\Filament\Resources\Warehouses;

use App\Enums\WarehouseType;
use App\Filament\Resources\Warehouses\Pages\CreateWarehouse;
use App\Filament\Resources\Warehouses\Pages\EditWarehouse;
use App\Filament\Resources\Warehouses\Pages\ListWarehouses;
use App\Filament\Resources\Warehouses\RelationManagers\StocksRelationManager;
use App\Models\Warehouse;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use UnitEnum;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;
    protected static ?string $modelLabel = 'انبار';
    protected static ?string $pluralModelLabel = 'انبارها';
    protected static ?string $navigationLabel = 'انبارها';
    protected static string|UnitEnum|null $navigationGroup = 'انبار و لجستیک';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات انبار')->columns(2)->schema([
                TextInput::make('name')->label('نام انبار')->required(),
                Select::make('type')->label('نوع')->options(collect(WarehouseType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()])->all())->required(),
                TextInput::make('country')->label('کشور')->required(),
                TextInput::make('city')->label('شهر'),
                Textarea::make('address')->label('آدرس')->columnSpanFull(),
                TextInput::make('manager_name')->label('مسئول'),
                TextInput::make('phone')->label('تلفن'),
                Toggle::make('is_active')->label('فعال')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('نام')->searchable()->sortable(),
            TextColumn::make('type')->label('نوع')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
            TextColumn::make('country')->label('کشور')->searchable(),
            TextColumn::make('city')->label('شهر'),
            TextColumn::make('stocks_count')->label('اقلام موجودی')->counts('stocks'),
            IconColumn::make('is_active')->label('فعال')->boolean(),
        ])->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return [StocksRelationManager::class]; }
    public static function getPages(): array
    {
        return ['index' => ListWarehouses::route('/'), 'create' => CreateWarehouse::route('/create'), 'edit' => EditWarehouse::route('/{record}/edit')];
    }
}
