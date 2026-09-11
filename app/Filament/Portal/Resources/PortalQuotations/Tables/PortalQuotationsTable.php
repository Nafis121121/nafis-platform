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
                TextColumn::make('reference_code')->label('کد استعلام')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('final_total_irr')->label('مبلغ نهایی')->numeric(),
                TextColumn::make('valid_until')->date(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label('دانلود PDF')
                    ->action(fn (\App\Models\Quotation $record) => app(\App\Services\QuotationPdfService::class)->download($record)),
            ])
            ->toolbarActions([]);
    }
}
