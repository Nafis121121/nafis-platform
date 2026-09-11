<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('فایل رسانه')
                ->schema([
                    FileUpload::make('path')
                        ->label('آپلود تصویر')
                        ->image()
                        ->disk('public')
                        ->directory('site-media')
                        ->visibility('public')
                        ->preventFilePathTampering()
                        ->imagePreviewHeight('220')
                        ->openable()
                        ->downloadable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->columnSpanFull(),
                    Hidden::make('disk')->default('public'),
                    Hidden::make('bucket')->default('site-media'),
                    Hidden::make('folder')->default('general'),
                ]),
            Section::make('اطلاعات رسانه')
                ->columns(2)
                ->schema([
                    TextInput::make('title')->label('عنوان'),
                    TextInput::make('alt_fa')->label('Alt فارسی')->required(),
                    TextInput::make('alt_en')->label('Alt انگلیسی'),
                    Textarea::make('url')
                        ->label('URL خارجی (اختیاری)')
                        ->helperText('اگر URL خارجی وارد شود، در نمایش بر مسیر فایل اولویت دارد.')
                        ->columnSpanFull(),
                    TextInput::make('mime_type')->label('MIME')->disabled()->dehydrated(),
                    TextInput::make('size_bytes')->label('حجم (bytes)')->disabled()->dehydrated(),
                    TextInput::make('width')->label('عرض')->disabled()->dehydrated(),
                    TextInput::make('height')->label('ارتفاع')->disabled()->dehydrated(),
                ]),
        ]);
    }
}
