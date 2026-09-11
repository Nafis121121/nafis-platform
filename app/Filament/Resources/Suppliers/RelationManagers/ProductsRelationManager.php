<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';
    protected static ?string $title = 'محصولات قابل تأمین';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('product_id')->label('محصول')->options(fn (): array => Product::query()->orderBy('name_fa')->pluck('name_fa', 'id')->all())->searchable()->preload()->required(),
            Select::make('product_variant_id')->label('تنوع')->options(fn (callable $get): array => ProductVariant::query()->where('product_id', $get('product_id'))->pluck('sku', 'id')->all())->searchable()->nullable(),
            TextInput::make('supplier_sku')->label('کد کارخانه'),
            TextInput::make('moq')->label('MOQ')->numeric()->default(1)->required(),
            TextInput::make('cost_price')->label('قیمت تأمین')->numeric()->required(),
            Select::make('currency')->label('ارز')->options(['USD' => 'USD', 'CNY' => 'CNY', 'AED' => 'AED'])->default('USD')->required(),
            TextInput::make('lead_time_days')->label('زمان آماده‌سازی (روز)')->numeric(),
            TextInput::make('product_url')->label('لینک محصول')->url(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('product.name_fa')->label('محصول')->searchable(),
            TextColumn::make('supplier_sku')->label('کد کارخانه'),
            TextColumn::make('cost_price')->label('قیمت'),
            TextColumn::make('currency')->label('ارز'),
            TextColumn::make('moq')->label('MOQ'),
            TextColumn::make('lead_time_days')->label('زمان (روز)'),
        ])->headerActions([CreateAction::make()->label('افزودن محصول')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
