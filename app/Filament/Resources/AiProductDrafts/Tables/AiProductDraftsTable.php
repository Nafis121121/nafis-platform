<?php

namespace App\Filament\Resources\AiProductDrafts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;

class AiProductDraftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('محصول')->searchable(),
                TextColumn::make('brand_name')->label('برند'),
                TextColumn::make('status')->badge(),
                TextColumn::make('category.name_fa')->label('دسته‌بندی'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(collect(\App\Enums\AiProductDraftStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->value])->all()),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('approve')
                    ->label('تأیید و ایجاد محصول')
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\AiProductDraft $record) => $record->status !== \App\Enums\AiProductDraftStatus::IMPORTED)
                    ->action(fn (\App\Models\AiProductDraft $record) => app(\App\Services\AiProductImporterService::class)->approveDraftToProduct($record, auth()->id())),
                Action::make('reject')
                    ->label('رد کردن')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (\App\Models\AiProductDraft $record) => $record->update(['status' => \App\Enums\AiProductDraftStatus::REJECTED, 'reviewed_by' => auth()->id()])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
