<?php

namespace App\Filament\Resources\InventoryStocks;

use App\Filament\Resources\InventoryStocks\Pages\ListInventoryStocks;
use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\Warehouse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class InventoryStockResource extends Resource
{
    protected static ?string $model = InventoryStock::class;
    protected static ?string $modelLabel = 'موجودی';
    protected static ?string $pluralModelLabel = 'موجودی انبار';
    protected static ?string $navigationLabel = 'موجودی انبار';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('warehouse.name')->label('انبار')->searchable()->sortable(),
            TextColumn::make('product.name_fa')->label('محصول')->searchable()->sortable(),
            TextColumn::make('variant.sku')->label('تنوع')->placeholder('—'),
            TextColumn::make('quantity_on_hand')->label('موجودی')->numeric()->sortable(),
            TextColumn::make('quantity_reserved')->label('رزرو')->numeric(),
            TextColumn::make('reorder_point')->label('نقطه سفارش')->numeric(),
            TextColumn::make('available_quantity')->label('قابل استفاده')->numeric(),
        ])->filters([
            SelectFilter::make('warehouse_id')->label('انبار')->options(fn (): array => Warehouse::query()->orderBy('name')->pluck('name', 'id')->all()),
            SelectFilter::make('category_id')->label('دسته‌بندی')->relationship('product.category', 'name_fa'),
        ])->defaultSort('quantity_on_hand');
    }

    public static function getPages(): array
    {
        return ['index' => ListInventoryStocks::route('/')];
    }
}
