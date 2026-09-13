<?php

namespace App\Filament\Portal\Resources\PortalQuotations\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;

class PortalQuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_code')->label('شماره پیش‌فاکتور')->searchable()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('final_total_irr')->label('مبلغ نهایی (ریال)')->numeric()->sortable(),
                TextColumn::make('valid_until')->label('مهلت اعتبار')->date('Y/m/d')->sortable(),
            ])
            ->emptyStateHeading('هیچ پیش‌فاکتوری صادر نشده است')
            ->emptyStateDescription('پیش‌فاکتورهای صادر شده توسط کارشناسان پس از بررسی در این بخش نمایش داده می‌شوند.')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label('دانلود PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn (\App\Models\Quotation $record) => app(\App\Services\QuotationPdfService::class)->download($record)),
                Action::make('sendWhatsapp')
                    ->label('واتساپ')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->openUrlInNewTab()
                    ->url(function (\App\Models\Quotation $record) {
                        $text = "پیش‌فاکتور شماره {$record->reference_code} بازرگانی نفیس تجارت\n"
                              . "مبلغ کل: " . number_format((float) $record->final_total_irr) . " ریال\n"
                              . "مشاهده در پورتال: " . url('/portal');
                        return "https://wa.me/?text=" . urlencode($text);
                    }),
                Action::make('sendTelegram')
                    ->label('تلگرام')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->openUrlInNewTab()
                    ->url(function (\App\Models\Quotation $record) {
                        $text = "پیش‌فاکتور شماره {$record->reference_code} بازرگانی نفیس تجارت\n"
                              . "مبلغ کل: " . number_format((float) $record->final_total_irr) . " ریال";
                        $url = url('/portal');
                        return "https://t.me/share/url?url=" . urlencode($url) . "&text=" . urlencode($text);
                    }),
            ])
            ->toolbarActions([]);
    }
}
