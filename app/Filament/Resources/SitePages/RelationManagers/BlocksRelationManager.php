<?php

namespace App\Filament\Resources\SitePages\RelationManagers;

use App\Enums\ContentStatus;
use App\Filament\Support\ContentBlockForm;
use App\Models\ContentBlock;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlocksRelationManager extends RelationManager
{
    protected static string $relationship = 'blocks';

    protected static ?string $title = 'بلوک‌های محتوا';

    public function form(Schema $schema): Schema
    {
        return $schema->components(ContentBlockForm::schema());
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('block_key')
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('position')->label('ترتیب')->sortable(),
                TextColumn::make('block_key')->label('کلید')->searchable(),
                TextColumn::make('type')->label('نوع')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
                TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
                TextColumn::make('starts_at')->label('شروع')->dateTime('Y/m/d H:i')->toggleable(),
                TextColumn::make('updated_at')->label('آخرین تغییر')->since()->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()->label('افزودن بلوک'),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('انتشار')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ContentBlock $record): bool => $record->status !== ContentStatus::PUBLISHED || $record->draft_data !== $record->published_data)
                    ->action(function (ContentBlock $record): void {
                        $record->publish();
                        Notification::make()->title('بلوک منتشر شد.')->success()->send();
                    }),
                Action::make('revert')
                    ->label('بازگردانی پیش‌نویس')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn (ContentBlock $record): bool => filled($record->published_data))
                    ->action(function (ContentBlock $record): void {
                        $record->revertToPublished();
                        Notification::make()->title('پیش‌نویس از نسخه منتشرشده بازیابی شد.')->success()->send();
                    }),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()->label('حذف'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
