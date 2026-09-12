<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $modelLabel = 'دسته‌بندی';
    protected static ?string $pluralModelLabel = 'دسته‌بندی‌ها';
    protected static ?string $navigationLabel = 'دسته‌بندی‌ها';
    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ و محصولات';
    protected static ?int $navigationSort = 3;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;
    protected static ?string $recordTitleAttribute = 'name_fa';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات دسته‌بندی')->columns(2)->schema([
                TextInput::make('name_fa')->label('نام فارسی')->required()->maxLength(150),
                TextInput::make('name_en')->label('نام انگلیسی')->maxLength(150),
                TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true)->maxLength(160),
                Select::make('parent_id')
                    ->label('دسته‌بندی والد')
                    ->options(fn (?Category $record): array => Category::query()
                        ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                        ->orderBy('name_fa')
                        ->pluck('name_fa', 'id')
                        ->all())
                    ->searchable()
                    ->nullable(),
                Textarea::make('description_fa')->label('توضیحات فارسی')->columnSpanFull(),
                Textarea::make('description_en')->label('توضیحات انگلیسی')->columnSpanFull(),
                TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0)->required(),
                Toggle::make('is_active')->label('فعال')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name_fa')->label('نام')->searchable()->sortable(),
            TextColumn::make('slug')->label('Slug')->searchable(),
            TextColumn::make('parent.name_fa')->label('والد')->placeholder('—'),
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
