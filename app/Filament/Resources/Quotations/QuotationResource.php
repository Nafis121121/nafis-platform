<?php

namespace App\Filament\Resources\Quotations;

use App\Enums\QuotationCurrency;
use App\Enums\QuotationStatus;
use App\Filament\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\Pages\ListQuotations;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quotation;
use App\Models\User;
use App\Services\OrderService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;
    protected static ?string $modelLabel = 'استعلام قیمت';
    protected static ?string $pluralModelLabel = 'استعلام‌های قیمت';
    protected static ?string $navigationLabel = 'استعلام قیمت';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static ?string $recordTitleAttribute = 'reference_code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make([
                Step::make('مشتری و وضعیت')->schema([
                    Select::make('user_id')
                        ->label('مشتری')
                        ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()->preload()->required(),
                    Select::make('assigned_to')
                        ->label('کارشناس فروش')
                        ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()->preload()->nullable(),
                    Select::make('status')
                        ->label('وضعیت')
                        ->options(collect(QuotationStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])->all())
                        ->default(QuotationStatus::DRAFT->value)->required(),
                    DatePicker::make('valid_until')->label('اعتبار تا'),
                ])->columns(2),
                Step::make('اقلام کالا')->schema([
                    Repeater::make('items')
                        ->label('اقلام استعلام')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->label('محصول')
                                ->options(fn (): array => Product::query()->where('is_active', true)->orderBy('name_fa')->pluck('name_fa', 'id')->all())
                                ->searchable()->preload()->required(),
                            Select::make('product_variant_id')
                                ->label('تنوع')
                                ->options(fn (callable $get): array => ProductVariant::query()
                                    ->where('product_id', $get('product_id'))
                                    ->pluck('sku', 'id')->all())
                                ->searchable()->nullable(),
                            TextInput::make('item_title')->label('عنوان کالا')->required(),
                            TextInput::make('technical_description')->label('شرح فنی'),
                            TextInput::make('quantity')->label('تعداد')->numeric()->minValue(1)->default(1)->required()->live()
                                ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateItemTotals($get, $set)),
                            TextInput::make('unit_cost_currency')->label('قیمت خرید ارزی')->numeric()->minValue(0)->required()->live()
                                ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateItemTotals($get, $set)),
                            TextInput::make('unit_price_irr')->label('قیمت فروش واحد (ریال)')->numeric()->minValue(0)->required()->live()
                                ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateItemTotals($get, $set)),
                            TextInput::make('total_cost_currency')->label('جمع خرید ارزی')->numeric()->disabled()->dehydrated(),
                            TextInput::make('total_price_irr')->label('جمع فروش (ریال)')->numeric()->disabled()->dehydrated(),
                        ])->columns(2)->defaultItems(1)->addActionLabel('افزودن کالا')->reorderable(),
                ]),
                Step::make('هزینه و تسعیر')->schema([
                    Select::make('base_currency')->label('ارز پایه')->options(collect(QuotationCurrency::cases())->mapWithKeys(fn ($currency) => [$currency->value => $currency->value])->all())->default('USD')->required(),
                    TextInput::make('exchange_rate')->label('نرخ ارز به ریال')->numeric()->minValue(0)->default(1)->required()->live(),
                    TextInput::make('shipping_cost_base_currency')->label('حمل پایه ارزی')->numeric()->default(0)->live(),
                    TextInput::make('shipping_weight_kg')->label('وزن حمل (کیلوگرم)')->numeric()->default(0)->live(),
                    TextInput::make('shipping_rate_per_kg')->label('نرخ حمل هر کیلو')->numeric()->default(0)->live(),
                    TextInput::make('shipping_volume_cbm')->label('حجم حمل (CBM)')->numeric()->default(0)->live(),
                    TextInput::make('shipping_rate_per_cbm')->label('نرخ حمل هر CBM')->numeric()->default(0)->live(),
                    TextInput::make('inspection_fee_base_currency')->label('بازرسی ارزی')->numeric()->default(0),
                    TextInput::make('customs_duty_irr')->label('حقوق گمرکی (ریال)')->numeric()->default(0),
                    TextInput::make('handling_fee_irr')->label('هزینه خدمات (ریال)')->numeric()->default(0),
                    TextInput::make('margin_percentage')->label('درصد سود')->numeric()->minValue(0)->default(0)->live(),
                    TextInput::make('tax_irr')->label('مالیات (ریال)')->numeric()->default(0),
                ])->columns(3),
                Step::make('شرایط اعتباری')->schema([
                    Textarea::make('payment_terms')->label('شرایط پرداخت')->rows(4),
                    Textarea::make('customer_notes')->label('توضیحات برای مشتری')->rows(4),
                    Textarea::make('internal_notes')->label('یادداشت داخلی')->rows(4),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_code')->label('کد')->searchable()->sortable(),
                TextColumn::make('customer.name')->label('مشتری')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
                TextColumn::make('base_currency')->label('ارز'),
                TextColumn::make('final_total_irr')->label('مبلغ نهایی')->numeric()->sortable(),
                TextColumn::make('valid_until')->label('اعتبار تا')->date('Y/m/d')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('changeStatus')
                    ->label('تغییر وضعیت')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Select::make('status')->label('وضعیت جدید')->options(collect(QuotationStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])->all())->required(),
                    ])
                    ->action(fn (Quotation $record, array $data) => $record->update(['status' => $data['status']])),
                Action::make('convertToOrder')
                    ->label('تبدیل به سفارش')
                    ->icon('heroicon-o-shopping-cart')
                    ->requiresConfirmation()
                    ->visible(fn (Quotation $record): bool => $record->status === QuotationStatus::APPROVED)
                    ->action(fn (Quotation $record) => app(OrderService::class)->convertQuotationToOrder($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotations::route('/'),
            'create' => CreateQuotation::route('/create'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }

    private static function updateItemTotals(Get $get, Set $set): mixed
    {
        $quantity = (float) ($get('quantity') ?: 0);
        $cost = (float) ($get('unit_cost_currency') ?: 0);
        $price = (float) ($get('unit_price_irr') ?: 0);

        $set('total_cost_currency', round($quantity * $cost, 4));
        $set('total_price_irr', round($quantity * $price));

        return null;
    }
}
