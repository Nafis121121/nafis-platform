<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Resources\Quotations\QuotationResource;
use App\Services\QuotationPricingService;
use App\Services\QuotationPdfService;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function afterSave(): void
    {
        app(QuotationPricingService::class)->recalculate($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('recalculate')
                ->label('محاسبه مجدد مبالغ')
                ->icon('heroicon-o-calculator')
                ->action(fn () => app(QuotationPricingService::class)->recalculate($this->record)),
            Action::make('downloadPdf')
                ->label('دانلود PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => app(QuotationPdfService::class)->download($this->record)),
            Action::make('sendWhatsapp')
                ->label('ارسال به واتساپ')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('success')
                ->openUrlInNewTab()
                ->url(function () {
                    $record = $this->record;
                    $record->loadMissing('customer');
                    $phone = $record->customer?->phone;
                    $cleanPhone = $phone ? preg_replace('/^0/', '98', preg_replace('/[^0-9]/', '', $phone)) : '';
                    $text = "پیش‌فاکتور شماره {$record->reference_code} بازرگانی نفیس تجارت\n"
                          . "مبلغ کل: " . number_format((float) $record->final_total_irr) . " ریال\n"
                          . "مشاهده در پورتال: " . url('/portal');

                    return $cleanPhone
                        ? "https://wa.me/{$cleanPhone}?text=" . urlencode($text)
                        : "https://wa.me/?text=" . urlencode($text);
                }),
            Action::make('sendTelegram')
                ->label('ارسال به تلگرام')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->openUrlInNewTab()
                ->url(function () {
                    $record = $this->record;
                    $text = "پیش‌فاکتور شماره {$record->reference_code} بازرگانی نفیس تجارت\n"
                          . "مبلغ کل: " . number_format((float) $record->final_total_irr) . " ریال";
                    $url = url('/portal');

                    return "https://t.me/share/url?url=" . urlencode($url) . "&text=" . urlencode($text);
                }),
        ];
    }
}
