<?php

namespace App\Filament\Resources\Warehouses\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';
    protected static ?string $title = 'موجودی جاری';

    public function form(Schema $schema): Schema { return $schema->components([]); }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('product.name_fa')->label('محصول')->searchable(),
            TextColumn::make('variant.sku')->label('تنوع')->placeholder('—'),
            TextColumn::make('quantity_on_hand')->label('موجودی'),
            TextColumn::make('quantity_reserved')->label('رزروشده'),
            TextColumn::make('reorder_point')->label('نقطه سفارش'),
        ]);
    }
}
