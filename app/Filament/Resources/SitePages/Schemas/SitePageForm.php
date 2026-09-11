<?php

namespace App\Filament\Resources\SitePages\Schemas;

use App\Enums\ContentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SitePageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات صفحه')
                ->columns(2)
                ->schema([
                    TextInput::make('slug')->label('Slug')->required()->maxLength(160),
                    TextInput::make('route_path')->label('مسیر سفارشی')->helperText('مثال: /services/sourcing'),
                    TextInput::make('title_fa')->label('عنوان فارسی')->required(),
                    TextInput::make('title_en')->label('عنوان انگلیسی'),
                    Textarea::make('summary_fa')->label('خلاصه فارسی')->columnSpanFull(),
                    Textarea::make('summary_en')->label('خلاصه انگلیسی')->columnSpanFull(),
                    Select::make('status')->label('وضعیت')->options(ContentStatus::class)->default(ContentStatus::DRAFT->value)->required(),
                    TextInput::make('position')->label('ترتیب')->numeric()->default(0)->required(),
                    Toggle::make('is_system')->label('صفحه سیستمی')->default(false),
                    DateTimePicker::make('publish_at')->label('زمان انتشار صفحه'),
                    DateTimePicker::make('published_at')->label('آخرین انتشار')->disabled()->dehydrated(),
                ]),
            Section::make('SEO و اشتراک‌گذاری')
                ->columns(2)
                ->schema([
                    TextInput::make('seo_title')->label('Meta Title')->maxLength(70),
                    TextInput::make('canonical_url')->label('Canonical URL')->url()->maxLength(500),
                    Textarea::make('seo_description')->label('Meta Description')->rows(3)->maxLength(180)->columnSpanFull(),
                    TextInput::make('seo_keywords')->label('Keywords'),
                    Toggle::make('noindex')->label('Noindex')->default(false),
                    TextInput::make('og_title')->label('OG Title')->maxLength(90),
                    Textarea::make('og_description')->label('OG Description')->rows(3)->maxLength(220)->columnSpanFull(),
                    Select::make('og_image_id')
                        ->label('تصویر اشتراک‌گذاری')
                        ->relationship('ogImage', 'title')
                        ->getOptionLabelFromRecordUsing(fn ($record): string => $record->title
                            ?: $record->alt_fa
                            ?: $record->path
                            ?: 'تصویر بدون نام')
                        ->searchable()
                        ->preload(),
                ]),
        ]);
    }
}
