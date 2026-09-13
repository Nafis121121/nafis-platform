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
use App\Models\SiteSetting;
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
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use UnitEnum;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;
    protected static ?string $modelLabel = 'استعلام قیمت';
    protected static ?string $pluralModelLabel = 'استعلام‌های قیمت';
    protected static ?string $navigationLabel = 'استعلام‌های قیمت';
    protected static string|UnitEnum|null $navigationGroup = 'سفارشات و بازرگانی';
    protected static ?int $navigationSort = 2;
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
                                ->searchable()->preload()->required()
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                    if ($state) {
                                        $product = Product::find($state);
                                        if ($product) {
                                            $set('item_title', $product->name_fa);
                                            if ((float) $product->base_price > 0) {
                                                $set('unit_cost_currency', (float) $product->base_price);
                                                self::updateItemTotals($get, $set);
                                            }
                                        }
                                    }
                                }),
                            Select::make('product_variant_id')
                                ->label('تنوع')
                                ->options(fn (callable $get): array => ProductVariant::query()
                                    ->where('product_id', $get('product_id'))
                                    ->pluck('sku', 'id')->all())
                                ->searchable()->nullable(),
                            TextInput::make('item_title')->label('عنوان کالا')->required(),
                            TextInput::make('technical_description')->label('شرح فنی'),
                            TextInput::make('quantity')
                                ->label('تعداد')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                                ->stripCharacters(',')
                                ->default(1)
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateItemTotals($get, $set)),
                            TextInput::make('unit_cost_currency')
                                ->label('قیمت خرید ارزی')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 4)'))
                                ->stripCharacters(',')
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateItemTotals($get, $set)),
                            TextInput::make('unit_price_irr')
                                ->label('قیمت فروش واحد (ریال)')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                                ->stripCharacters(',')
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set): mixed => self::updateItemTotals($get, $set, true)),
                            TextInput::make('total_cost_currency')
                                ->label('جمع خرید ارزی')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 4)'))
                                ->stripCharacters(',')
                                ->disabled()
                                ->dehydrated(),
                            TextInput::make('total_price_irr')
                                ->label('جمع فروش (ریال)')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                                ->stripCharacters(',')
                                ->disabled()
                                ->dehydrated(),
                        ])->columns(2)->defaultItems(1)->addActionLabel('افزودن کالا')->reorderable(),
                ]),
                Step::make('هزینه و تسعیر')->schema([
                    Select::make('base_currency')
                        ->label('ارز پایه')
                        ->options(collect(QuotationCurrency::cases())->mapWithKeys(fn ($currency) => [$currency->value => $currency->value])->all())
                        ->default('USD')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            $pricing = SiteSetting::current()->pricing ?? [];
                            if ($state === 'CNY') {
                                $set('exchange_rate', $pricing['exchange_rate_cny'] ?? 125000);
                            } elseif ($state === 'AED') {
                                $set('exchange_rate', $pricing['exchange_rate_aed'] ?? 245000);
                            } elseif ($state === 'USD') {
                                $set('exchange_rate', $pricing['exchange_rate_usd'] ?? 900000);
                            } elseif ($state === 'IRR') {
                                $set('exchange_rate', 1);
                            }
                        }),
                    TextInput::make('exchange_rate')
                        ->label('نرخ ارز به ریال')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                        ->stripCharacters(',')
                        ->default(fn () => SiteSetting::current()->pricing['exchange_rate_usd'] ?? 900000)
                        ->required()
                        ->live(),
                    TextInput::make('shipping_cost_base_currency')
                        ->label('حمل پایه ارزی')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 4)'))
                        ->stripCharacters(',')
                        ->default(0)
                        ->live(),
                    TextInput::make('shipping_weight_kg')
                        ->label('وزن حمل (کیلوگرم)')
                        ->numeric()
                        ->default(0)
                        ->live(),
                    TextInput::make('shipping_rate_per_kg')
                        ->label('نرخ حمل هر کیلو')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 4)'))
                        ->stripCharacters(',')
                        ->default(fn () => SiteSetting::current()->pricing['shipping_rate_per_kg'] ?? 5.5)
                        ->live(),
                    TextInput::make('shipping_volume_cbm')
                        ->label('حجم حمل (CBM)')
                        ->numeric()
                        ->default(0)
                        ->live(),
                    TextInput::make('shipping_rate_per_cbm')
                        ->label('نرخ حمل هر CBM')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 4)'))
                        ->stripCharacters(',')
                        ->default(fn () => SiteSetting::current()->pricing['shipping_rate_per_cbm'] ?? 180)
                        ->live(),
                    TextInput::make('inspection_fee_base_currency')
                        ->label('بازرسی ارزی')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 4)'))
                        ->stripCharacters(',')
                        ->default(0),
                    TextInput::make('customs_duty_irr')
                        ->label('حقوق گمرکی (ریال)')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                        ->stripCharacters(',')
                        ->default(0),
                    TextInput::make('handling_fee_irr')
                        ->label('هزینه خدمات (ریال)')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                        ->stripCharacters(',')
                        ->default(0),
                    TextInput::make('margin_percentage')
                        ->label('درصد سود')
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => SiteSetting::current()->pricing['default_margin_percentage'] ?? 15)
                        ->live(),
                    TextInput::make('tax_irr')
                        ->label('مالیات (ریال)')
                        ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                        ->stripCharacters(',')
                        ->default(0),
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
                TextColumn::make('reference_code')->label('کد استعلام')->searchable()->sortable(),
                TextColumn::make('customer.name')->label('مشتری')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
                TextColumn::make('base_currency')->label('ارز'),
                TextColumn::make('final_total_irr')->label('مبلغ نهایی (ریال)')->numeric()->sortable(),
                TextColumn::make('valid_until')->label('اعتبار تا')->date('Y/m/d')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('downloadPdf')
                    ->label('دانلود PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn (Quotation $record) => app(\App\Services\QuotationPdfService::class)->download($record)),
                Action::make('sendWhatsapp')
                    ->label('واتساپ')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->openUrlInNewTab()
                    ->url(function (Quotation $record) {
                        $record->loadMissing('customer');
                        $phone = $record->customer?->phone;
                        $cleanPhone = $phone ? preg_replace('/^0/', '98', preg_replace('/[^0-9]/', '', $phone)) : '';
                        $text = "پیش‌فاکتور شماره {$record->reference_code} بازرگانی نفیس تجارت\n"
                              . "مبلغ کل: " . number_format((float) $record->final_total_irr) . " ریال\n"
                              . "مشاهده در پورتال: " . url('/portal');

                        return $cleanPhone
                            ? "https://wa.me/{$cleanPhone}?text=" . urlencode($text)
                            : "https://wa.me/?text=" . urlencode($text);
                    }),
                Action::make('sendTelegram')
                    ->label('تلگرام')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->openUrlInNewTab()
                    ->url(function (Quotation $record) {
                        $text = "پیش‌فاکتور شماره {$record->reference_code} بازرگانی نفیس تجارت\n"
                              . "مبلغ کل: " . number_format((float) $record->final_total_irr) . " ریال";
                        $url = url('/portal');

                        return "https://t.me/share/url?url=" . urlencode($url) . "&text=" . urlencode($text);
                    }),
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

    private static function updateItemTotals(Get $get, Set $set, bool $isPriceManual = false): mixed
    {
        $quantity = (float) str_replace(',', '', (string) ($get('quantity') ?: 0));
        $cost = (float) str_replace(',', '', (string) ($get('unit_cost_currency') ?: 0));
        $currentUnitPrice = (float) str_replace(',', '', (string) ($get('unit_price_irr') ?: 0));

        $exchangeRate = (float) str_replace(',', '', (string) ($get('../../exchange_rate') ?: $get('exchange_rate') ?: 0));
        $margin = (float) ($get('../../margin_percentage') ?: $get('margin_percentage') ?: 0);
        $baseCurrency = $get('../../base_currency') ?: $get('base_currency') ?: 'USD';

        $pricing = SiteSetting::current()->pricing ?? [];

        if ($exchangeRate <= 0) {
            $exchangeRate = match ($baseCurrency) {
                'CNY' => (float) ($pricing['exchange_rate_cny'] ?? 125000),
                'AED' => (float) ($pricing['exchange_rate_aed'] ?? 245000),
                'USD' => (float) ($pricing['exchange_rate_usd'] ?? 900000),
                default => 1,
            };
        }

        if ($margin <= 0 && !empty($pricing['default_margin_percentage'])) {
            $margin = (float) $pricing['default_margin_percentage'];
        }

        if (! $isPriceManual && $cost > 0 && $exchangeRate > 0) {
            $currentUnitPrice = round($cost * $exchangeRate * (1 + ($margin / 100)));
            $set('unit_price_irr', $currentUnitPrice);
        }

        $set('total_cost_currency', round($quantity * $cost, 4));
        $set('total_price_irr', round($quantity * $currentUnitPrice));

        return null;
    }
}
