<?php

namespace App\Filament\Resources\Brands;

use App\Filament\Resources\Brands\Pages\CreateBrand;
use App\Filament\Resources\Brands\Pages\EditBrand;
use App\Filament\Resources\Brands\Pages\ListBrands;
use App\Models\Brand;
use App\Models\MediaAsset;
use BackedEnum;
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

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static ?string $modelLabel = 'برند';
    protected static ?string $pluralModelLabel = 'برندها';
    protected static ?string $navigationLabel = 'برندها';
    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ و محصولات';
    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;
    protected static ?string $recordTitleAttribute = 'name_fa';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات برند')->columns(2)->schema([
                TextInput::make('name_fa')->label('نام فارسی')->required()->maxLength(150),
                TextInput::make('name_en')->label('نام انگلیسی')->maxLength(150),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(160),
                Select::make('logo_media_id')
                    ->label('لوگو')
                    ->options(fn (): array => MediaAsset::query()->orderBy('title')->get()
                        ->mapWithKeys(fn (MediaAsset $asset): array => [
                            $asset->id => $asset->title ?: $asset->alt_fa ?: $asset->path ?: 'رسانه بدون نام',
                        ])->all())
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('website')->label('وب‌سایت')->url()->maxLength(255),
                TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0)->required(),
                Textarea::make('description_fa')->label('توضیحات فارسی')->columnSpanFull(),
                Textarea::make('description_en')->label('توضیحات انگلیسی')->columnSpanFull(),
                Toggle::make('is_active')->label('فعال')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name_fa')->label('نام')->searchable()->sortable(),
            TextColumn::make('name_en')->label('نام انگلیسی')->searchable(),
            TextColumn::make('products_count')->label('محصولات')->counts('products')->sortable(),
            TextColumn::make('sort_order')->label('ترتیب')->sortable(),
            IconColumn::make('is_active')->label('فعال')->boolean(),
        ])
            ->defaultSort('sort_order')
            ->recordActions([\Filament\Actions\EditAction::make()])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }
}
