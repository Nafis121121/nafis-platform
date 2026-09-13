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
                    ->label('دانلود PDF پیش‌فاکتور')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (\App\Models\Quotation $record) => app(\App\Services\QuotationPdfService::class)->download($record)),
            ])
            ->toolbarActions([]);
    }
}
