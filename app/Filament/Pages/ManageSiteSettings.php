<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\CmsService;
use BackedEnum;
use UnitEnum;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends Page
{
    protected string $view = 'filament.pages.manage-site-settings';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;
    protected static ?string $title = 'تنظیمات عمومی سایت';
    protected static ?string $navigationLabel = 'تنظیمات سایت';
    protected static string|UnitEnum|null $navigationGroup = 'مدیریت محتوا و سایت';
    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
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
