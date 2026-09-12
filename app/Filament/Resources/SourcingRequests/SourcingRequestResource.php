<?php

namespace App\Filament\Resources\SourcingRequests;

use App\Enums\SourcingRequestStatus;
use App\Filament\Resources\SourcingRequests\Pages\CreateSourcingRequest;
use App\Filament\Resources\SourcingRequests\Pages\EditSourcingRequest;
use App\Filament\Resources\SourcingRequests\Pages\ListSourcingRequests;
use App\Models\SourcingRequest;
use App\Models\User;
use App\Services\SourcingService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use UnitEnum;

class SourcingRequestResource extends Resource
{
    protected static ?string $model = SourcingRequest::class;
    protected static ?string $modelLabel = 'درخواست سورسینگ';
    protected static ?string $pluralModelLabel = 'درخواست‌های سورسینگ';
    protected static ?string $navigationLabel = 'درخواست‌های سورسینگ';
    protected static string|UnitEnum|null $navigationGroup = 'سفارشات و بازرگانی';
    protected static ?int $navigationSort = 3;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;
    protected static ?string $recordTitleAttribute = 'reference_code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('درخواست و مشتری')->columns(2)->schema([
                TextInput::make('reference_code')->label('کد رهگیری')->disabled()->dehydrated(false),
                Select::make('user_id')->label('مشتری')->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload()->required(),
                Select::make('assigned_to')->label('کارشناس')->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload()->nullable(),
                Select::make('status')->label('وضعیت')->options(collect(SourcingRequestStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all())->default('pending')->required(),
                TextInput::make('title')->label('عنوان کالای درخواستی')->required()->columnSpanFull(),
                Textarea::make('technical_specifications')->label('مشخصات فنی')->columnSpanFull(),
                Textarea::make('required_standards')->label('استانداردها')->columnSpanFull(),
            ]),
            Section::make('نیاز تجاری و پیگیری')->columns(2)->schema([
                TextInput::make('estimated_quantity')->label('تیراژ تخمینی')->numeric(),
                TextInput::make('target_price')->label('قیمت هدف')->numeric(),
                Select::make('target_currency')->label('ارز هدف')->options(['USD' => 'USD', 'CNY' => 'CNY', 'AED' => 'AED', 'IRR' => 'IRR'])->default('USD')->required(),
                Textarea::make('follow_up_notes')->label('یادداشت پیگیری'),
                Textarea::make('result_notes')->label('نتیجه بررسی'),
                Textarea::make('attachments')->label('پیوست‌ها (JSON)')->helperText('لینک فایل یا تصویر را به‌صورت JSON وارد کنید.')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('reference_code')->label('کد')->searchable()->sortable(),
            TextColumn::make('title')->label('درخواست')->searchable(),
            TextColumn::make('customer.name')->label('مشتری')->searchable(),
            TextColumn::make('assignee.name')->label('کارشناس')->placeholder('—'),
            TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
            TextColumn::make('created_at')->label('ثبت')->dateTime('Y/m/d')->sortable(),
        ])->recordActions([
            EditAction::make(),
            Action::make('createQuotation')->label('ایجاد پیش‌فاکتور')->icon('heroicon-o-document-plus')
                ->visible(fn (SourcingRequest $record): bool => blank($record->quotation_id))
                ->requiresConfirmation()
                ->action(fn (SourcingRequest $record) => app(SourcingService::class)->createQuotation($record)),
            Action::make('changeStatus')->label('تغییر وضعیت')->icon('heroicon-o-arrow-path')
                ->form([Select::make('status')->label('وضعیت')->options(collect(SourcingRequestStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all())->required()])
                ->action(fn (SourcingRequest $record, array $data) => app(SourcingService::class)->changeStatus($record, SourcingRequestStatus::from($data['status']))),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSourcingRequests::route('/'),
            'create' => CreateSourcingRequest::route('/create'),
            'edit' => EditSourcingRequest::route('/{record}/edit'),
        ];
    }
}
