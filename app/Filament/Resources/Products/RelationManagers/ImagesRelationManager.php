<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Models\ProductImage;
use App\Services\AiProductImporterService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'گالری و تصاویر محصول';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('uploaded_file')
                ->label('بارگذاری فایل تصویر از کامپیوتر')
                ->disk('public')
                ->directory('products')
                ->image()
                ->imageEditor()
                ->helperText('فایل تصویر را از سیستم خود انتخاب کنید (در صورت انتخاب فایل، اولویت با این گزینه است).'),
            TextInput::make('url')
                ->label('یا آدرس اینترنتی تصویر (URL)')
                ->placeholder('https://... یا لینک تصویر')
                ->helperText('در صورتی که فایل بارگذاری نمی‌کنید، لینک تصویر را وارد کنید.'),
            TextInput::make('alt_text')
                ->label('متن جایگزین (Alt / سئو)')
                ->maxLength(255),
            TextInput::make('sort_order')
                ->label('ترتیب نمایش')
                ->numeric()
                ->default(0),
            Toggle::make('is_primary')
                ->label('تصویر شاخص اصلی')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('resolved_url')
                    ->label('پیش‌نمایش')
                    ->square(),
                TextColumn::make('url')
                    ->label('آدرس / فایل')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('alt_text')->label('متن جایگزین')->placeholder('—'),
                IconColumn::make('is_primary')->label('تصویر شاخص')->boolean(),
                TextColumn::make('sort_order')->label('ترتیب')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label('افزودن تصویر جدید')
                    ->mutateFormDataUsing(function (array $data): array {
                        if (! empty($data['uploaded_file'])) {
                            $data['url'] = $data['uploaded_file'];
                        }
                        unset($data['uploaded_file']);
                        return $data;
                    }),
            ])
            ->recordActions([
                Action::make('openFull')
                    ->label('مشاهده و دانلود')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (ProductImage $record): string => $record->resolved_url, shouldOpenInNewTab: true),
                Action::make('downloadToLocal')
                    ->label('ذخیره در سرور')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->visible(fn (ProductImage $record): bool => str_starts_with($record->url, 'http://') || str_starts_with($record->url, 'https://') || str_starts_with($record->url, '//'))
                    ->action(function (ProductImage $record) {
                        $localPath = app(AiProductImporterService::class)->downloadImageToStorage($record->url);
                        if ($localPath) {
                            $record->update(['url' => $localPath]);
                            Notification::make()->title('تصویر با موفقیت در هاست ذخیره شد.')->success()->send();
                        } else {
                            Notification::make()->title('خطا در دانلود تصویر از منبع خارجی')->danger()->send();
                        }
                    }),
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (! empty($data['uploaded_file'])) {
                            $data['url'] = $data['uploaded_file'];
                        }
                        unset($data['uploaded_file']);
                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
