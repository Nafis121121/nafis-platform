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
                TextColumn::make('tracking_code')->label('کد رهگیری')->searchable(),
                TextColumn::make('bill_of_lading')->label('بارنامه')->searchable(),
                TextColumn::make('method')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('eta')->date()->label('ETA'),
                TextColumn::make('ata')->date()->label('ATA'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
            ])
            ->toolbarActions([]);
    }
}
