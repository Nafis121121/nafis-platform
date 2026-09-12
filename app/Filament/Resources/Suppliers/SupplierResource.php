<?php

namespace App\Filament\Resources\Suppliers;

use App\Enums\SupplierStatus;
use App\Filament\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\Resources\Suppliers\RelationManagers\ProductsRelationManager;
use App\Models\Supplier;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use UnitEnum;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;
    protected static ?string $modelLabel = 'تأمین‌کننده';
    protected static ?string $pluralModelLabel = 'تأمین‌کنندگان';
    protected static ?string $navigationLabel = 'تأمین‌کنندگان';
    protected static string|UnitEnum|null $navigationGroup = 'انبار و لجستیک';
    protected static ?int $navigationSort = 4;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $recordTitleAttribute = 'company_name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات شرکت')->columns(2)->schema([
                TextInput::make('company_name')->label('نام شرکت')->required(),
                TextInput::make('country')->label('کشور')->required(),
                TextInput::make('city')->label('شهر'),
                TextInput::make('contact_person')->label('شخص رابط'),
                TextInput::make('phone')->label('تلفن'),
                TextInput::make('email')->label('ایمیل')->email(),
                TextInput::make('wechat_id')->label('WeChat'),
                TextInput::make('whatsapp')->label('WhatsApp'),
                Textarea::make('factory_address')->label('آدرس کارخانه')->columnSpanFull(),
            ]),
            Section::make('ارزیابی و شرایط تجاری')->columns(2)->schema([
                Select::make('rating')->label('رتبه کیفی')->options(['A' => 'A - عالی', 'B' => 'B - خوب', 'C' => 'C - ضعیف'])->nullable(),
                Select::make('status')->label('وضعیت همکاری')->options(collect(SupplierStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all())->default('pending')->required(),
                Select::make('default_currency')->label('ارز پیش‌فرض')->options(['USD' => 'USD', 'CNY' => 'CNY', 'AED' => 'AED'])->default('USD')->required(),
                Textarea::make('default_payment_terms')->label('شرایط پرداخت'),
                Textarea::make('evaluation_notes')->label('یادداشت ارزیابی')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('company_name')->label('شرکت')->searchable()->sortable(),
            TextColumn::make('country')->label('کشور')->searchable(),
            TextColumn::make('contact_person')->label('رابط'),
            TextColumn::make('rating')->label('رتبه')->badge(),
            TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
            TextColumn::make('products_count')->label('محصولات')->counts('products'),
        ])->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return [ProductsRelationManager::class]; }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
