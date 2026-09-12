<?php

namespace App\Filament\Resources\AiProductDrafts;

use App\Filament\Resources\AiProductDrafts\Pages\CreateAiProductDraft;
use App\Filament\Resources\AiProductDrafts\Pages\EditAiProductDraft;
use App\Filament\Resources\AiProductDrafts\Pages\ListAiProductDrafts;
use App\Filament\Resources\AiProductDrafts\Schemas\AiProductDraftForm;
use App\Filament\Resources\AiProductDrafts\Tables\AiProductDraftsTable;
use App\Models\AiProductDraft;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class AiProductDraftResource extends Resource
{
    protected static ?string $model = AiProductDraft::class;
    protected static ?string $modelLabel = 'پیش‌نویس هوش مصنوعی';
    protected static ?string $pluralModelLabel = 'پیش‌نویس‌های هوش مصنوعی';
    protected static ?string $navigationLabel = 'پیش‌نویس‌های هوش مصنوعی';
    protected static string|UnitEnum|null $navigationGroup = 'کاتالوگ و محصولات';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    public static function getNavigationBadge(): ?string
    {
        $count = AiProductDraft::where('status', \App\Enums\AiProductDraftStatus::PENDING)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isRole('super_admin', 'sales_manager', 'content_manager', 'warehouse_staff');
    }

    public static function form(Schema $schema): Schema
    {
        return AiProductDraftForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AiProductDraftsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAiProductDrafts::route('/'),
            'create' => CreateAiProductDraft::route('/create'),
            'edit' => EditAiProductDraft::route('/{record}/edit'),
        ];
    }
}
