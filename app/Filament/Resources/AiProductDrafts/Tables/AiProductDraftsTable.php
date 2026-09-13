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
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof \BackedEnum ? $state->value : $state) {
                        'pending' => 'warning',
                        'reviewing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'imported' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('category.name_fa')->label('دسته‌بندی'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(collect(\App\Enums\AiProductDraftStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])->all()),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('approve')
                    ->label('تأیید و ایجاد محصول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (\App\Models\AiProductDraft $record) => $record->status !== \App\Enums\AiProductDraftStatus::IMPORTED)
                    ->form(fn (\App\Models\AiProductDraft $record) => [
                        \Filament\Forms\Components\Select::make('category_id')
                            ->label('دسته‌بندی نهایی کالا')
                            ->relationship('category', 'name_fa')
                            ->default($record->category_id)
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                \Filament\Forms\Components\TextInput::make('name_fa')->label('نام دسته‌بندی فارسی')->required(),
                                \Filament\Forms\Components\TextInput::make('name_en')->label('نام انگلیسی'),
                                \Filament\Forms\Components\TextInput::make('slug')->label('Slug')->required(),
                            ])
                            ->createOptionUsing(fn (array $data): string => \App\Models\Category::create([
                                'name_fa' => $data['name_fa'],
                                'name_en' => $data['name_en'] ?? null,
                                'slug' => \Illuminate\Support\Str::slug($data['slug'] ?: $data['name_fa']),
                                'is_active' => true,
                            ])->id)
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('base_price')
                            ->label('قیمت پایه (اختیاری)')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('می‌توانید قیمت را بعداً نیز در پنل محصولات ویرایش کنید.'),
                        \Filament\Forms\Components\Select::make('base_currency')
                            ->label('ارز')
                            ->options(['USD' => 'دلار (USD)', 'CNY' => 'یوان (CNY)', 'AED' => 'درهم (AED)', 'IRR' => 'ریال (IRR)'])
                            ->default('USD'),
                        \Filament\Forms\Components\TextInput::make('moq')
                            ->label('حداقل تعداد سفارش (MOQ)')
                            ->numeric()
                            ->default(1),
                    ])
                    ->action(function (\App\Models\AiProductDraft $record, array $data) {
                        $record->update(['category_id' => $data['category_id'] ?? $record->category_id]);
                        $product = app(\App\Services\AiProductImporterService::class)->approveDraftToProduct($record, auth()->id());
                        if (isset($data['base_price']) || isset($data['base_currency']) || isset($data['moq'])) {
                            $product->update([
                                'base_price' => $data['base_price'] ?? null,
                                'base_currency' => $data['base_currency'] ?? 'USD',
                                'moq' => $data['moq'] ?? 1,
                            ]);
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('محصول با موفقیت ساخته شد')
                            ->body("محصول '{$product->name_fa}' به عنوان پیش‌نویس غیرعمومی در انبار ثبت شد.")
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('رد کردن')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (\App\Models\AiProductDraft $record) => $record->update(['status' => \App\Enums\AiProductDraftStatus::REJECTED, 'reviewed_by' => auth()->id()])),
                Action::make('link')
                    ->label('اتصال به محصول موجود')
                    ->form([
                        \Filament\Forms\Components\Select::make('product_id')
                            ->label('محصول موجود')
                            ->relationship('product', 'name_fa')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\AiProductDraft $record) => $record->status !== \App\Enums\AiProductDraftStatus::IMPORTED)
                    ->action(fn (\App\Models\AiProductDraft $record, array $data) => app(\App\Services\AiProductImporterService::class)->linkDraftToProduct($record, \App\Models\Product::findOrFail($data['product_id']), auth()->id())),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
