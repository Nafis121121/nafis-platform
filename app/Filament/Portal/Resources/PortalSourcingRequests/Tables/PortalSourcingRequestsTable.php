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
                TextColumn::make('reference_code')->label('کد پیگیری')->searchable()->sortable(),
                TextColumn::make('title')->label('عنوان درخواست')->searchable()->limit(50),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('created_at')->label('تاریخ ثبت')->dateTime('Y/m/d H:i')->sortable(),
            ])
            ->emptyStateHeading('هیچ درخواست استعلامی ثبت نشده است')
            ->emptyStateDescription('برای ثبت درخواست استعلام یا تأمین کالا، از دکمه «ثبت درخواست استعلام جدید» استفاده کنید.')
            ->filters([
                //
            ])
            ->recordActions([
            ])
            ->toolbarActions([]);
    }
}
