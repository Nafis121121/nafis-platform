<?php

namespace App\Filament\Resources\AiProductDrafts\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\{TextInput,Textarea,Select,KeyValue,TagsInput};

class AiProductDraftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('source_url')->url()->required()->disabled(),
                Select::make('status')->options(collect(\App\Enums\AiProductDraftStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->value])->all())->required(),
                TextInput::make('name')->required(),
                TextInput::make('name_en'),
                TextInput::make('brand_name'),
                TextInput::make('sku'),
                TextInput::make('model_number'),
                TextInput::make('country_of_origin'),
                Select::make('category_id')->relationship('category', 'name_fa')->searchable()->preload(),
                Textarea::make('short_desc')->columnSpanFull(),
                Textarea::make('long_desc')->columnSpanFull(),
                KeyValue::make('specifications')->columnSpanFull(),
                TagsInput::make('images')->label('تصاویر استخراج‌شده')->columnSpanFull(),
                TextInput::make('seo_title'),
                TextInput::make('seo_slug'),
                Textarea::make('seo_desc')->columnSpanFull(),
            ]);
    }
}
