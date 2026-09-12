<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\AiProductDrafts\AiProductDraftResource;
use App\Services\AiProductImporterService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Throwable;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('extractWithAi')
                ->label('ساخت پیش‌نویس با هوش مصنوعی')
                ->modalHeading('استخراج و تولید هوشمند مشخصات کالا با AI')
                ->modalDescription('می‌توانید لینک صفحه محصول (علی‌بابا، ۱۶۸۸، آمازون و...) یا نام و مدل کالا را وارد کنید تا هوش مصنوعی اطلاعات جامع، مشخصات فنی و ترجمه سئو شده را تولید کند.')
                ->form([
                    TextInput::make('source_url')
                        ->label('لینک صفحه محصول یا نام/مدل کالا')
                        ->placeholder('مثال: https://www.alibaba.com/product-detail/... یا نام مدل کالا')
                        ->required(),
                    Textarea::make('raw_text')
                        ->label('مشخصات یا توضیحات تکمیلی کالا (اختیاری)')
                        ->placeholder('می‌توانید جدول مشخصات، متن کاتالوگ یا ویژگی‌های خاص را اینجا پیست کنید.')
                        ->rows(4),
                ])
                ->action(function (array $data) {
                    try {
                        $draft = app(AiProductImporterService::class)->importFromUrl(
                            $data['source_url'],
                            $data['raw_text'] ?? null,
                            auth()->id()
                        );

                        Notification::make()
                            ->title('پیش‌نویس با موفقیت استخراج و تولید شد.')
                            ->success()
                            ->send();

                        return redirect(AiProductDraftResource::getUrl('edit', ['record' => $draft]));
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('خطا در استخراج هوش مصنوعی')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
