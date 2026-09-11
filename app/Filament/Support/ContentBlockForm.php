<?php

namespace App\Filament\Support;

use App\Enums\BlockType;
use App\Enums\ContentStatus;
use App\Models\MediaAsset;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class ContentBlockForm
{
    public static function schema(): array
    {
        return [
            Section::make('تنظیمات بلوک')
                ->columns(2)
                ->schema([
                    Select::make('type')
                        ->label('نوع بلوک')
                        ->options(
                            collect(BlockType::cases())
                                ->mapWithKeys(fn ($case) => [
                                    $case->value => $case->label(),
                            ])
                            ->toArray()
                      )  
                        ->required()
                        ->live(),

                    TextInput::make('block_key')
                        ->label('کلید داخلی')
                        ->helperText('مثال: home_hero. در هر صفحه باید یکتا باشد.')
                        ->maxLength(120),

                    Select::make('status')
                        ->label('وضعیت')
                        ->options(
                            collect(ContentStatus::cases())
                                ->mapWithKeys(fn ($case) => [
                                    $case->value => $case->label(),
                                ])
                                ->toArray()
                        )
                        ->required(),

                    TextInput::make('position')
                        ->label('ترتیب نمایش')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    DateTimePicker::make('starts_at')
                        ->label('شروع نمایش'),

                    DateTimePicker::make('ends_at')
                        ->label('پایان نمایش'),
                ]),

            Section::make('محتوای پیش‌نویس')
                ->description('این داده‌ها تا زمان انتشار روی سایت عمومی نمایش داده نمی‌شوند.')
                ->columns(2)
                ->schema([
                    ...self::heroFields(),
                    ...self::bannerSliderFields(),
                    ...self::featureGridFields(),
                    ...self::statsFields(),
                    ...self::logosFields(),
                    ...self::faqFields(),
                    ...self::ctaFields(),
                    ...self::contactFields(),
                    ...self::richTextFields(),
                ]),
        ];
    }

    /**
     * بررسی نوع بلوک.
     *
     * Filament ممکن است مقدار type را به‌صورت string
     * یا به‌صورت Enum برگرداند، بنابراین هر دو حالت
     * در اینجا پشتیبانی می‌شوند.
     */
    private static function is(BlockType|string $type): \Closure
    {
        $expectedValue = $type instanceof BlockType
            ? $type->value
            : $type;

        return function (Get $get) use ($expectedValue): bool {
            $value = $get('type');

            if ($value instanceof BackedEnum) {
                $value = $value->value;
            }

            return (string) $value === (string) $expectedValue;
        };
    }

    /**
     * گزینه‌های Media Library.
     */
    private static function mediaOptions(): array
{
    return MediaAsset::query()
        ->latest()
        ->get()
        ->filter(fn (MediaAsset $asset): bool => filled($asset->resolved_url))
        ->mapWithKeys(function (MediaAsset $asset): array {

            $label = $asset->title
                ?: $asset->alt_fa
                ?: $asset->path
                ?: 'تصویر بدون نام';

            return [
                $asset->resolved_url => $label,
            ];
        })
        ->toArray();
}         
    /**
     * Hero
     */
    private static function heroFields(): array
    {
        $visible = self::is(BlockType::HERO);

        return [
            TextInput::make('draft_data.badge')
                ->label('Badge')
                ->visible($visible),

            TextInput::make('draft_data.title')
                ->label('عنوان اصلی')
                ->required()
                ->visible($visible),

            TextInput::make('draft_data.highlight')
                ->label('بخش برجسته عنوان')
                ->visible($visible),

            Textarea::make('draft_data.subtitle')
                ->label('زیرعنوان')
                ->rows(4)
                ->columnSpanFull()
                ->visible($visible),

            TextInput::make('draft_data.ctaPrimary')
                ->label('متن دکمه اصلی')
                ->visible($visible),

            TextInput::make('draft_data.ctaPrimaryUrl')
                ->label('لینک دکمه اصلی')
                ->visible($visible),

            TextInput::make('draft_data.ctaSecondary')
                ->label('متن دکمه دوم')
                ->visible($visible),

            TextInput::make('draft_data.ctaSecondaryUrl')
                ->label('لینک دکمه دوم')
                ->visible($visible),

            Select::make('draft_data.imageUrl')
                ->label('تصویر از کتابخانه رسانه')
                ->options(fn (): array => self::mediaOptions())
                ->searchable()
                ->preload()
                ->visible($visible),

            TextInput::make('draft_data.overlay')
                ->label('شدت Overlay')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->default(45)
                ->visible($visible),
        ];
    }

    /**
     * Banner Slider
     */
    private static function bannerSliderFields(): array
    {
        $visible = self::is(BlockType::BANNER_SLIDER);

        return [
            Repeater::make('draft_data.slides')
                ->label('اسلایدها')
                ->schema([
                    TextInput::make('title')
                        ->label('عنوان')
                        ->required(),

                    TextInput::make('subtitle')
                        ->label('زیرعنوان'),

                    Select::make('imageUrl')
                        ->label('تصویر')
                        ->options(fn (): array => self::mediaOptions())
                        ->searchable()
                        ->preload(),

                    TextInput::make('link')
                        ->label('لینک'),

                    Toggle::make('active')
                        ->label('فعال')
                        ->default(true),
                ])
                ->columns(2)
                ->collapsible()
                ->cloneable()
                ->reorderable()
                ->columnSpanFull()
                ->visible($visible),
        ];
    }

    /**
     * Feature Grid
     */
    private static function featureGridFields(): array
    {
        $visible = self::is(BlockType::FEATURE_GRID);

        return [
            TextInput::make('draft_data.eyebrow')
                ->label('تیتر کوچک')
                ->visible($visible),

            TextInput::make('draft_data.title')
                ->label('عنوان')
                ->visible($visible),

            Textarea::make('draft_data.description')
                ->label('توضیح')
                ->rows(3)
                ->columnSpanFull()
                ->visible($visible),

            Repeater::make('draft_data.items')
                ->label('کارت‌ها')
                ->schema([
                    TextInput::make('step')
                        ->label('شماره / مرحله'),

                    TextInput::make('title')
                        ->label('عنوان')
                        ->required(),

                    TextInput::make('en')
                        ->label('عنوان انگلیسی'),

                    Textarea::make('description')
                        ->label('توضیح')
                        ->rows(3),

                    Repeater::make('points')
                        ->label('نکات')
                        ->simple(
                            TextInput::make('point')
                                ->label('نکته')
                        ),
                ])
                ->columns(2)
                ->collapsible()
                ->cloneable()
                ->reorderable()
                ->columnSpanFull()
                ->visible($visible),
        ];
    }

    /**
     * Stats
     */
    private static function statsFields(): array
    {
        $visible = self::is(BlockType::STATS);

        return [
            Repeater::make('draft_data.items')
                ->label('آمارها')
                ->schema([
                    TextInput::make('title')
                        ->label('مقدار / عنوان')
                        ->required(),

                    TextInput::make('subtitle')
                        ->label('توضیح')
                        ->required(),
                ])
                ->columns(2)
                ->reorderable()
                ->cloneable()
                ->columnSpanFull()
                ->visible($visible),
        ];
    }

    /**
     * Logos
     */
    private static function logosFields(): array
    {
        $visible = self::is(BlockType::LOGOS);

        return [
            TextInput::make('draft_data.title')
                ->label('عنوان')
                ->visible($visible),

            Repeater::make('draft_data.logos')
                ->label('لوگو / شریک')
                ->schema([
                    TextInput::make('name')
                        ->label('نام')
                        ->required(),

                    Select::make('imageUrl')
                        ->label('لوگو')
                        ->options(fn (): array => self::mediaOptions())
                        ->searchable()
                        ->preload(),

                    TextInput::make('link')
                        ->label('لینک'),
                ])
                ->columns(2)
                ->reorderable()
                ->cloneable()
                ->columnSpanFull()
                ->visible($visible),
        ];
    }

    /**
     * FAQ
     */
    private static function faqFields(): array
    {
        $visible = self::is(BlockType::FAQ);

        return [
            TextInput::make('draft_data.title')
                ->label('عنوان')
                ->visible($visible),

            Repeater::make('draft_data.items')
                ->label('پرسش‌ها')
                ->schema([
                    TextInput::make('question')
                        ->label('پرسش')
                        ->required(),

                    Textarea::make('answer')
                        ->label('پاسخ')
                        ->rows(4)
                        ->required(),
                ])
                ->reorderable()
                ->cloneable()
                ->collapsible()
                ->columnSpanFull()
                ->visible($visible),
        ];
    }

    /**
     * CTA
     */
    private static function ctaFields(): array
    {
        $visible = self::is(BlockType::CTA);

        return [
            TextInput::make('draft_data.title')
                ->label('عنوان')
                ->required()
                ->visible($visible),

            Textarea::make('draft_data.description')
                ->label('توضیح')
                ->rows(3)
                ->columnSpanFull()
                ->visible($visible),

            TextInput::make('draft_data.primaryText')
                ->label('متن دکمه اصلی')
                ->visible($visible),

            TextInput::make('draft_data.primaryLink')
                ->label('لینک دکمه اصلی')
                ->visible($visible),

            TextInput::make('draft_data.secondaryText')
                ->label('متن دکمه دوم')
                ->visible($visible),

            TextInput::make('draft_data.secondaryLink')
                ->label('لینک دکمه دوم')
                ->visible($visible),
        ];
    }

    /**
     * Contact
     */
    private static function contactFields(): array
    {
        $visible = self::is(BlockType::CONTACT);

        return [
            TextInput::make('draft_data.title')
                ->label('عنوان')
                ->visible($visible),

            Textarea::make('draft_data.address')
                ->label('آدرس')
                ->rows(3)
                ->columnSpanFull()
                ->visible($visible),

            TextInput::make('draft_data.phoneDisplay')
                ->label('شماره نمایشی')
                ->visible($visible),

            TextInput::make('draft_data.phoneIntl')
                ->label('شماره بین‌المللی')
                ->visible($visible),

            TextInput::make('draft_data.email')
                ->label('ایمیل')
                ->email()
                ->visible($visible),

            TextInput::make('draft_data.whatsapp')
                ->label('واتس‌اپ')
                ->url()
                ->visible($visible),

            TextInput::make('draft_data.telegram')
                ->label('تلگرام')
                ->url()
                ->visible($visible),

            TextInput::make('draft_data.instagram')
                ->label('اینستاگرام')
                ->url()
                ->visible($visible),
        ];
    }

    /**
     * Rich Text
     */
    private static function richTextFields(): array
    {
        $visible = self::is(BlockType::RICH_TEXT);

        return [
            TextInput::make('draft_data.title')
                ->label('عنوان')
                ->visible($visible),

            Textarea::make('draft_data.content')
                ->label('متن')
                ->rows(12)
                ->columnSpanFull()
                ->visible($visible),
        ];
    }
}
