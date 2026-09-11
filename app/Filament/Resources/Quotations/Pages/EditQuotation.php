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
                ->label('بازتبه محاسبات')
                ->action(fn () => app(QuotationPricingService::class)->recalculate($this->record)),
            Action::make('downloadPdf')
                ->label('دانلود PDF')
                ->action(fn () => app(QuotationPdfService::class)->download($this->record)),
        ];
    }
}
