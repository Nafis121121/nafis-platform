<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\ImagesRelationManager;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $modelLabel = 'محصول';
    protected static ?string $pluralModelLabel = 'محصولات';
    protected static ?string $navigationLabel = 'محصولات';
    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ و محصولات';
    protected static ?int $navigationSort = 1;
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
                    ->relationship('category', 'name_fa')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name_fa')->label('نام فارسی دسته‌بندی')->required(),
                        TextInput::make('name_en')->label('نام انگلیسی'),
                        TextInput::make('slug')->label('Slug')->required(),
                    ])
                    ->createOptionUsing(fn (array $data): string => Category::create([
                        'name_fa' => $data['name_fa'],
                        'name_en' => $data['name_en'] ?? null,
                        'slug' => \Illuminate\Support\Str::slug($data['slug'] ?: $data['name_fa']),
                        'is_active' => true,
                    ])->id),
                Select::make('brand_id')
                    ->label('برند')
                    ->relationship('brand', 'name_fa')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->createOptionForm([
                        TextInput::make('name_fa')->label('نام برند')->required(),
                        TextInput::make('name_en')->label('نام لاتین'),
                        TextInput::make('slug')->label('Slug')->required(),
                    ])
                    ->createOptionUsing(fn (array $data): string => Brand::create([
                        'name_fa' => $data['name_fa'],
                        'name_en' => $data['name_en'] ?? null,
                        'slug' => \Illuminate\Support\Str::slug($data['slug'] ?: $data['name_fa']),
                        'is_active' => true,
                    ])->id),
                Textarea::make('description_fa')->label('توضیحات فارسی')->rows(4)->columnSpanFull(),
                Textarea::make('description_en')->label('توضیحات انگلیسی')->rows(3)->columnSpanFull(),
                TextInput::make('seo_title')->label('عنوان SEO'),
                TextInput::make('seo_slug')->label('Slug SEO'),
                Textarea::make('seo_desc')->label('توضیحات SEO')->rows(3)->columnSpanFull(),
                TextInput::make('image_alt')->label('متن جایگزین تصویر'),
                Select::make('status')
                    ->label('وضعیت محصول')
                    ->options(['draft' => 'پیش‌نویس', 'active' => 'فعال'])
                    ->default('active')
                    ->required(),
                Select::make('catalog_visibility')
                    ->label('نمایش در کاتالوگ')
                    ->options(['hidden' => 'مخفی', 'visible' => 'قابل نمایش'])
                    ->default('visible')
                    ->required(),
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
                Toggle::make('homepage_featured')->label('نمایش در اسلایدر نمونه محصولات'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images.resolved_url')
                    ->label('تصویر')
                    ->circular()
                    ->getStateUsing(fn (Product $record) => $record->images->first()?->resolved_url),
                TextColumn::make('name_fa')->label('محصول')->searchable()->sortable(),
                TextColumn::make('base_sku')->label('SKU')->searchable(),
                TextColumn::make('category.name_fa')->label('دسته‌بندی')->sortable(),
                TextColumn::make('brand.name_fa')->label('برند')->placeholder('—')->sortable(),
                TextColumn::make('base_price')->label('قیمت پایه')->money(fn (Product $record): string => $record->base_currency),
                TextColumn::make('variants_count')->label('تنوع‌ها')->counts('variants')->sortable(),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('catalog_visibility')->label('کاتالوگ')->badge(),
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
        return [
            ImagesRelationManager::class,
            VariantsRelationManager::class,
        ];
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
