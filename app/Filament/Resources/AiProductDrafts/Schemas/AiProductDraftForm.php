<?php

namespace App\Filament\Resources\AiProductDrafts\Schemas;

use App\Models\Category;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AiProductDraftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اطلاعات منبع و وضعیت')->columns(2)->schema([
                    TextInput::make('source_url')->label('آدرس منبع (URL)')->url()->required()->disabled(),
                    Select::make('status')
                        ->label('وضعیت پیش‌نویس')
                        ->options(collect(\App\Enums\AiProductDraftStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])->all())
                        ->required(),
                ]),
                Section::make('مشخصات محصول')->columns(2)->schema([
                    TextInput::make('name')->label('نام فارسی کالا')->required(),
                    TextInput::make('name_en')->label('نام انگلیسی'),
                    TextInput::make('brand_name')->label('نام برند'),
                    TextInput::make('sku')->label('SKU'),
                    TextInput::make('model_number')->label('شماره مدل'),
                    TextInput::make('country_of_origin')->label('کشور سازنده'),
                    Select::make('category_id')
                        ->label('دسته‌بندی کالا')
                        ->relationship('category', 'name_fa')
                        ->searchable()
                        ->preload()
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
                    Textarea::make('short_desc')->label('خلاصه / توضیح کوتاه')->columnSpanFull(),
                    Textarea::make('long_desc')->label('توضیحات جامع محصول')->columnSpanFull(),
                    KeyValue::make('specifications')->label('مشخصات فنی و تجاری')->keyLabel('ویژگی')->valueLabel('مقدار')->columnSpanFull(),
                    TagsInput::make('images')->label('تصاویر استخراج‌شده (لینک‌ها)')->columnSpanFull(),
                ]),
                Section::make('تنظیمات سئو (SEO)')->columns(2)->schema([
                    TextInput::make('seo_title')->label('عنوان سئو'),
                    TextInput::make('seo_slug')->label('Slug سئو'),
                    Textarea::make('seo_desc')->label('توضیحات متا سئو')->columnSpanFull(),
                ]),
            ]);
    }
}

