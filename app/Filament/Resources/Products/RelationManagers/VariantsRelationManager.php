<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'تنوع‌های محصول';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')->label('SKU')->required()->unique(ignoreRecord: true)->maxLength(100),
            TextInput::make('name_fa')->label('نام فارسی'),
            TextInput::make('name_en')->label('نام انگلیسی'),
            KeyValue::make('option_values')->label('مشخصات تنوع')->keyLabel('ویژگی')->valueLabel('مقدار'),
            Select::make('price_currency')
                ->label('ارز')
                ->options(['USD' => 'دلار (USD)', 'CNY' => 'یوان (CNY)', 'AED' => 'درهم (AED)'])
                ->default('USD')
                ->required(),
            TextInput::make('price')->label('قیمت')->numeric()->minValue(0),
            TextInput::make('moq')->label('MOQ')->numeric()->minValue(1),
            TextInput::make('weight_kg')->label('وزن (کیلوگرم)')->numeric()->minValue(0),
            Toggle::make('is_active')->label('فعال')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('name_fa')->label('نام'),
                TextColumn::make('price')->label('قیمت')->money(fn ($record): string => $record->price_currency),
                TextColumn::make('moq')->label('MOQ'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->headerActions([CreateAction::make()->label('افزودن تنوع')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
