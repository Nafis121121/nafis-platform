<?php

namespace App\Filament\Portal\Resources\PortalSourcingRequests\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PortalSourcingRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_code')->label('کد پیگیری'),
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
            ])
            ->toolbarActions([]);
    }
}
