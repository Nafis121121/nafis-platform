<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $modelLabel = 'محصول';
    protected static ?string $pluralModelLabel = 'محصولات';
    protected static ?string $navigationLabel = 'محصولات';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;
    protected static ?string $recordTitleAttribute = 'name_fa';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('مشخصات اصلی')->columns(2)->schema([
                TextInput::make('name_fa')->label('نام فارسی')->required()->maxLength(200),
                TextInput::make('name_en')->label('نام انگلیسی')->maxLength(200),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(200),
                TextInput::make('base_sku')->label('SKU پایه')->unique(ignoreRecord: true)->maxLength(100),
                Select::make('category_id')
                    ->label('دسته‌بندی')
                    ->options(fn (): array => Category::query()->where('is_active', true)->orderBy('name_fa')->pluck('name_fa', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('brand_id')
                    ->label('برند')
                    ->options(fn (): array => Brand::query()->where('is_active', true)->orderBy('name_fa')->pluck('name_fa', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Textarea::make('description_fa')->label('توضیحات فارسی')->columnSpanFull(),
                Textarea::make('description_en')->label('توضیحات انگلیسی')->columnSpanFull(),
                TextInput::make('seo_title')->label('عنوان SEO'),
                TextInput::make('seo_slug')->label('Slug SEO'),
                Textarea::make('seo_desc')->label('توضیحات SEO')->columnSpanFull(),
                TextInput::make('image_alt')->label('متن جایگزین تصویر'),
            ]),
            Section::make('قیمت و فروش')->columns(3)->schema([
                Select::make('base_currency')
                    ->label('ارز پایه')
                    ->options(['USD' => 'دلار (USD)', 'CNY' => 'یوان (CNY)', 'AED' => 'درهم (AED)'])
                    ->default('USD')
                    ->required(),
                TextInput::make('base_price')->label('قیمت پایه')->numeric()->minValue(0),
                TextInput::make('moq')->label('حداقل سفارش (MOQ)')->numeric()->minValue(1)->default(1)->required(),
                Toggle::make('is_active')->label('فعال')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_fa')->label('محصول')->searchable()->sortable(),
                TextColumn::make('base_sku')->label('SKU')->searchable(),
                TextColumn::make('category.name_fa')->label('دسته‌بندی')->sortable(),
                TextColumn::make('brand.name_fa')->label('برند')->placeholder('—')->sortable(),
                TextColumn::make('base_price')->label('قیمت پایه')->money(fn (Product $record): string => $record->base_currency),
                TextColumn::make('variants_count')->label('تنوع‌ها')->counts('variants')->sortable(),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [VariantsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
