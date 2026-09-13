<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\CmsService;
use App\Services\CurrencyExchangeService;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;

class ManageSiteSettings extends Page
{
    protected string $view = 'filament.pages.manage-site-settings';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $title = 'تنظیمات عمومی سایت';
    protected static ?string $navigationLabel = 'تنظیمات سایت';
    protected static string|UnitEnum|null $navigationGroup = 'مدیریت محتوا و سایت';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->isRole('super_admin', 'content_manager') ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncExchangeRates')
                ->label('دریافت فوری نرخ ارز')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    app(CurrencyExchangeService::class)->refreshFromProvider();
                    $this->form->fill(SiteSetting::current()->toArray());

                    Notification::make()
                        ->title('درخواست به‌روزرسانی نرخ ارز ارسال شد.')
                        ->body('در صورت فعال بودن حالت خودکار، نرخ‌ها به‌روزرسانی شدند؛ در غیر این صورت نرخ‌های دستی حفظ شدند.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('تنظیمات')->tabs([
                    Tabs\Tab::make('عمومی')
                        ->schema([
                            Toggle::make('cms_enabled')->label('فعال‌سازی CMS')->default(true),
                            TextInput::make('brand.name')->label('نام برند')->required()->maxLength(120),
                            TextInput::make('brand.tagline')->label('شعار برند')->maxLength(180),
                            Textarea::make('brand.topbar')->label('متن نوار بالای سایت')->rows(2),
                            Toggle::make('brand.topbarActive')->label('نمایش نوار بالای سایت')->default(true),
                            TextInput::make('brand.logoUrl')->label('مسیر / URL لوگو')->maxLength(500),
                        ]),
                    Tabs\Tab::make('نرخ‌های پایه بازرگانی')
                        ->schema([
                            Toggle::make('pricing.rate_mode')
                                ->label('دریافت خودکار نرخ ارز')
                                ->helperText('در حالت روشن، نرخ‌ها به‌صورت زمان‌بندی‌شده از منبع وب دریافت می‌شوند. در حالت خاموش، فقط مقادیر دستی زیر ملاک محاسبات خواهند بود.')
                                ->afterStateHydrated(fn ($component, $state) => $component->state($state === 'auto'))
                                ->dehydrateStateUsing(fn ($state) => $state ? 'auto' : 'manual')
                                ->live(),
                            Placeholder::make('pricing.rate_last_synced_at')
                                ->label('آخرین به‌روزرسانی نرخ')
                                ->content(function () {
                                    $syncedAt = SiteSetting::current()->pricing['rate_last_synced_at'] ?? null;

                                    return $syncedAt
                                        ? \Illuminate\Support\Carbon::parse($syncedAt)->translatedFormat('Y/m/d H:i')
                                        : 'هنوز به‌روزرسانی نشده است';
                                }),
                            TextInput::make('pricing.exchange_rate_cny')
                                ->label('نرخ حواله یوان (CNY به ریال)')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                                ->stripCharacters(',')
                                ->numeric()
                                ->helperText('نرخ روز حواله یوان چین به ریال ایران'),
                            TextInput::make('pricing.exchange_rate_aed')
                                ->label('نرخ حواله درهم (AED به ریال)')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                                ->stripCharacters(',')
                                ->numeric()
                                ->helperText('نرخ روز حواله درهم امارات به ریال ایران'),
                            TextInput::make('pricing.exchange_rate_usd')
                                ->label('نرخ حواله دلار (USD به ریال)')
                                ->mask(RawJs::make('$money($input, \'.\', \',\', 0)'))
                                ->stripCharacters(',')
                                ->numeric()
                                ->helperText('نرخ روز دلار به ریال ایران'),
                            TextInput::make('pricing.default_margin_percentage')
                                ->label('درصد سود پیش‌فرض بازرگانی')
                                ->numeric()
                                ->suffix('%')
                                ->minValue(0)
                                ->maxValue(100)
                                ->helperText('درصد مارجین استاندارد روی استعلام‌ها'),
                            TextInput::make('pricing.shipping_rate_per_kg')
                                ->label('نرخ پایه حمل هوایی (هر کیلو)')
                                ->numeric()
                                ->helperText('نرخ ارزی پایه به ازای هر کیلوگرم'),
                            TextInput::make('pricing.shipping_rate_per_cbm')
                                ->label('نرخ پایه حمل دریایی (هر CBM)')
                                ->numeric()
                                ->helperText('نرخ ارزی پایه به ازای هر متر مکعب'),
                        ])->columns(2),
                    Tabs\Tab::make('رنگ و ظاهر')
                        ->schema([
                            ColorPicker::make('theme.primary')->label('رنگ اصلی'),
                            ColorPicker::make('theme.primaryDeep')->label('رنگ اصلی تیره'),
                            ColorPicker::make('theme.gold')->label('رنگ طلایی'),
                            TextInput::make('theme.fontScale')->label('مقیاس فونت')->numeric()->minValue(80)->maxValue(130)->suffix('%'),
                        ]),
                    Tabs\Tab::make('اطلاعات تماس')
                        ->schema([
                            TextInput::make('contact.phoneDisplay')->label('شماره نمایشی'),
                            TextInput::make('contact.phoneIntl')->label('شماره بین‌المللی')->tel(),
                            TextInput::make('contact.email')->label('ایمیل')->email(),
                            Textarea::make('contact.address')->label('آدرس دفتر مرکزی')->rows(3),
                            TextInput::make('contact.whatsapp')->label('واتس‌اپ')->url(),
                            TextInput::make('contact.telegram')->label('تلگرام')->url(),
                            TextInput::make('contact.instagram')->label('اینستاگرام')->url(),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            TextInput::make('seo.siteTitle')->label('عنوان پیش‌فرض سایت')->maxLength(70),
                            Textarea::make('seo.description')->label('توضیحات متا')->rows(3)->maxLength(180),
                            TextInput::make('seo.keywords')->label('کلمات کلیدی'),
                            TextInput::make('seo.ogImage')->label('تصویر پیش‌فرض اشتراک‌گذاری')->maxLength(500),
                        ]),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $setting = SiteSetting::current();
        $setting->update($data);

        app(CmsService::class)->clearCache();

        Notification::make()
            ->title('تنظیمات با موفقیت ذخیره شد.')
            ->success()
            ->send();
    }
}
