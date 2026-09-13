<?php

namespace App\Filament\Portal\Resources\PortalShipments\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PortalShipmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_code')->label('کد رهگیری')->searchable()->sortable(),
                TextColumn::make('bill_of_lading')->label('شماره بارنامه')->searchable(),
                TextColumn::make('method')->label('روش حمل')->badge(),
                TextColumn::make('status')->label('وضعیت ارسال')->badge(),
                TextColumn::make('eta')->label('زمان تقریبی رسیدن (ETA)')->date('Y/m/d')->sortable(),
                TextColumn::make('ata')->label('زمان تحویل قطعی (ATA)')->date('Y/m/d')->sortable(),
            ])
            ->emptyStateHeading('هیچ محموله‌ای ثبت نشده است')
            ->emptyStateDescription('اطلاعات بارگیری، بارنامه و زمان‌بندی ترخیص و تحویل مرسوله‌ها در این بخش قرار می‌گیرد.')
            ->filters([
                //
            ])
            ->recordActions([
            ])
            ->toolbarActions([]);
    }
}
