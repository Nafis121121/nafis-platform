<?php

namespace App\Filament\Resources\ProductVariants;

use App\Filament\Resources\ProductVariants\Pages\CreateProductVariant;
use App\Filament\Resources\ProductVariants\Pages\EditProductVariant;
use App\Filament\Resources\ProductVariants\Pages\ListProductVariants;
use App\Models\Product;
use App\Models\ProductVariant;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use UnitEnum;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;
    protected static ?string $modelLabel = 'تنوع محصول';
    protected static ?string $pluralModelLabel = 'تنوع‌های محصولات';
    protected static ?string $navigationLabel = 'تنوع‌های محصولات';
    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ و محصولات';
    protected static ?int $navigationSort = 5;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $recordTitleAttribute = 'sku';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات تنوع')->columns(2)->schema([
                Select::make('product_id')
                    ->label('محصول')
                    ->options(fn (): array => Product::query()->orderBy('name_fa')->pluck('name_fa', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('sku')->label('SKU')->required()->unique(ignoreRecord: true)->maxLength(100),
                TextInput::make('name_fa')->label('نام فارسی'),
                TextInput::make('name_en')->label('نام انگلیسی'),
                KeyValue::make('option_values')->label('مشخصات تنوع')->keyLabel('ویژگی')->valueLabel('مقدار')->columnSpanFull(),
                Select::make('price_currency')
                    ->label('ارز')
                    ->options(['USD' => 'دلار (USD)', 'CNY' => 'یوان (CNY)', 'AED' => 'درهم (AED)'])
                    ->default('USD')
                    ->required(),
                TextInput::make('price')->label('قیمت')->numeric()->minValue(0),
                TextInput::make('moq')->label('MOQ')->numeric()->minValue(1),
                TextInput::make('weight_kg')->label('وزن (کیلوگرم)')->numeric()->minValue(0),
                Toggle::make('is_active')->label('فعال')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->label('SKU')->searchable()->sortable(),
                TextColumn::make('product.name_fa')->label('محصول')->searchable()->sortable(),
                TextColumn::make('name_fa')->label('نام تنوع')->searchable(),
                TextColumn::make('price')->label('قیمت')->money(fn (ProductVariant $record): string => $record->price_currency),
                TextColumn::make('moq')->label('MOQ'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductVariants::route('/'),
            'create' => CreateProductVariant::route('/create'),
            'edit' => EditProductVariant::route('/{record}/edit'),
        ];
    }
}
