<?php

namespace App\Filament\Resources\Shipments;

use App\Enums\ShipmentMethod;
use App\Enums\ShipmentStatus;
use App\Filament\Resources\Shipments\Pages\CreateShipment;
use App\Filament\Resources\Shipments\Pages\EditShipment;
use App\Filament\Resources\Shipments\Pages\ListShipments;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\Order;
use App\Models\Warehouse;
use App\Services\LogisticsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
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

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;
    protected static ?string $modelLabel = 'مرسوله';
    protected static ?string $pluralModelLabel = 'مرسولات و باربری';
    protected static ?string $navigationLabel = 'مرسولات و باربری';
    protected static string|UnitEnum|null $navigationGroup = 'انبار و لجستیک';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;
    protected static ?string $recordTitleAttribute = 'tracking_code';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات حمل')->columns(2)->schema([
                TextInput::make('tracking_code')->label('کد رهگیری')->disabled()->dehydrated(false),
                Select::make('method')->label('روش حمل')->options(collect(ShipmentMethod::cases())->mapWithKeys(fn ($method) => [$method->value => $method->value])->all())->required(),
                TextInput::make('freight_forwarder')->label('فورواردر'),
                TextInput::make('bill_of_lading')->label('بارنامه'),
                TextInput::make('container_tracking_number')->label('کانتینر/ترکینگ'),
                Select::make('order_id')->label('سفارش مرتبط')->options(fn (): array => Order::query()->latest()->pluck('reference_code', 'id')->all())->searchable()->nullable(),
                Select::make('status')->label('وضعیت')->options(collect(ShipmentStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])->all())->default('draft')->required(),
                Select::make('origin_warehouse_id')->label('انبار مبدأ')->options(fn (): array => Warehouse::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload()->required(),
                Select::make('destination_warehouse_id')->label('انبار مقصد')->options(fn (): array => Warehouse::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload()->required(),
                DatePicker::make('etd')->label('ETD'),
                DatePicker::make('eta')->label('ETA'),
                DatePicker::make('ata')->label('ATA'),
                Select::make('cost_currency')->label('ارز هزینه')->options(['USD' => 'USD', 'CNY' => 'CNY', 'AED' => 'AED', 'IRR' => 'IRR'])->default('USD'),
                TextInput::make('freight_cost')->label('هزینه حمل')->numeric()->default(0),
                TextInput::make('insurance_cost')->label('بیمه')->numeric()->default(0),
                TextInput::make('customs_cost')->label('گمرک')->numeric()->default(0),
                Textarea::make('notes')->label('یادداشت')->columnSpanFull(),
            ]),
            Section::make('لیست بسته‌بندی')->schema([
                Repeater::make('items')->relationship()->schema([
                    Select::make('product_id')->label('محصول')->options(fn (): array => Product::query()->where('is_active', true)->orderBy('name_fa')->pluck('name_fa', 'id')->all())->searchable()->preload()->required(),
                    Select::make('product_variant_id')->label('تنوع')->options(fn (callable $get): array => ProductVariant::query()->where('product_id', $get('product_id'))->pluck('sku', 'id')->all())->searchable()->nullable(),
                    TextInput::make('quantity')->label('تعداد')->numeric()->minValue(0.001)->required(),
                    TextInput::make('gross_weight_kg')->label('وزن ناخالص (کیلوگرم)')->numeric()->default(0),
                    TextInput::make('volume_cbm')->label('حجم (CBM)')->numeric()->default(0),
                    TextInput::make('carton_count')->label('کارتن')->numeric()->default(0),
                    TextInput::make('pallet_count')->label('پالت')->numeric()->default(0),
                ])->columns(2)->defaultItems(1)->addActionLabel('افزودن قلم')->reorderable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('tracking_code')->label('کد')->searchable()->sortable(),
            TextColumn::make('method')->label('روش'),
            TextColumn::make('originWarehouse.name')->label('مبدأ'),
            TextColumn::make('destinationWarehouse.name')->label('مقصد'),
            TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn ($state) => $state?->label() ?? $state),
            TextColumn::make('eta')->label('ETA')->date('Y/m/d'),
            TextColumn::make('ata')->label('ATA')->date('Y/m/d')->placeholder('—'),
        ])->recordActions([
            EditAction::make(),
            Action::make('receive')->label('ثبت رسید انبار')->icon('heroicon-o-archive-box-arrow-down')
                ->visible(fn (Shipment $record): bool => ! in_array($record->status?->value, [ShipmentStatus::DELIVERED_TO_WAREHOUSE->value, ShipmentStatus::COMPLETED->value], true))
                ->requiresConfirmation()
                ->action(fn (Shipment $record) => app(LogisticsService::class)->receiveShipmentIntoWarehouse($record)),
            Action::make('complete')->label('تکمیل حمل')->icon('heroicon-o-check')
                ->visible(fn (Shipment $record): bool => $record->status === ShipmentStatus::DELIVERED_TO_WAREHOUSE)
                ->action(fn (Shipment $record) => app(LogisticsService::class)->updateStatus($record, ShipmentStatus::COMPLETED)),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => ListShipments::route('/'), 'create' => CreateShipment::route('/create'), 'edit' => EditShipment::route('/{record}/edit')];
    }
}
