<?php

namespace App\Filament\Resources\AiProductDrafts\Pages;

use App\Enums\AiProductDraftStatus;
use App\Filament\Resources\AiProductDrafts\AiProductDraftResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAiProductDraft extends EditRecord
{
    protected static string $resource = AiProductDraftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('تأیید و ایجاد محصول')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status !== AiProductDraftStatus::IMPORTED)
                ->form([
                    Select::make('category_id')
                        ->label('دسته‌بندی نهایی کالا')
                        ->relationship('category', 'name_fa')
                        ->default($this->record->category_id)
                        ->searchable()
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name_fa')->label('نام دسته‌بندی فارسی')->required(),
                            TextInput::make('name_en')->label('نام انگلیسی'),
                            TextInput::make('slug')->label('Slug')->required(),
                        ])
                        ->createOptionUsing(fn (array $data): string => Category::create([
                            'name_fa' => $data['name_fa'],
                            'name_en' => $data['name_en'] ?? null,
                            'slug' => \Illuminate\Support\Str::slug($data['slug'] ?: $data['name_fa']),
                            'is_active' => true,
                        ])->id)
                        ->required(),
                    TextInput::make('base_price')
                        ->label('قیمت پایه (اختیاری)')
                        ->numeric()
                        ->minValue(0),
                    Select::make('base_currency')
                        ->label('ارز')
                        ->options(['USD' => 'دلار (USD)', 'CNY' => 'یوان (CNY)', 'AED' => 'درهم (AED)', 'IRR' => 'ریال (IRR)'])
                        ->default('USD'),
                    TextInput::make('moq')
                        ->label('حداقل تعداد سفارش (MOQ)')
                        ->numeric()
                        ->default(1),
                ])
                ->action(function (array $data) {
                    $this->save();
                    $this->record->update(['category_id' => $data['category_id'] ?? $this->record->category_id]);
                    $product = app(\App\Services\AiProductImporterService::class)->approveDraftToProduct($this->record, auth()->id());
                    if (isset($data['base_price']) || isset($data['base_currency']) || isset($data['moq'])) {
                        $product->update([
                            'base_price' => $data['base_price'] ?? null,
                            'base_currency' => $data['base_currency'] ?? 'USD',
                            'moq' => $data['moq'] ?? 1,
                        ]);
                    }
                    Notification::make()
                        ->title('محصول با موفقیت ساخته شد')
                        ->body("محصول '{$product->name_fa}' به عنوان پیش‌نویس غیرعمومی در انبار ثبت شد.")
                        ->success()
                        ->send();

                    $this->redirect(AiProductDraftResource::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }
}

