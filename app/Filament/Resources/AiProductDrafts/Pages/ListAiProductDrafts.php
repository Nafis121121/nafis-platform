<?php

namespace App\Filament\Resources\AiProductDrafts\Pages;

use App\Filament\Resources\AiProductDrafts\AiProductDraftResource;
use Filament\Actions\Action;
use App\Services\AiProductImporterService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Throwable;

class ListAiProductDrafts extends ListRecords
{
    protected static string $resource = AiProductDraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('extractWithAi')
                ->label('استخراج با هوش مصنوعی')
                ->modalHeading('استخراج و تولید هوشمند کالا با AI')
                ->modalDescription('می‌توانید لینک صفحه محصول یا نام/مدل کالا را وارد کنید تا هوش مصنوعی اطلاعات و مشخصات فنی آن را تولید کند.')
                ->form([
                    TextInput::make('source_url')
                        ->label('آدرس منبع یا نام کالا')
                        ->placeholder('مثال: https://www.alibaba.com/... یا نام و مدل کالا')
                        ->required(),
                    Textarea::make('raw_text')
                        ->label('مشخصات یا توضیحات کالا (اختیاری)')
                        ->placeholder('می‌توانید عنوان، جدول مشخصات یا کاتالوگ را در اینجا پیست کنید.')
                        ->rows(4),
                ])
                ->action(function (array $data) {
                    try {
                        app(AiProductImporterService::class)->importFromUrl(
                            $data['source_url'],
                            $data['raw_text'] ?? null,
                            auth()->id()
                        );

                        Notification::make()
                            ->title('پیش‌نویس با موفقیت استخراج شد.')
                            ->success()
                            ->send();
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
