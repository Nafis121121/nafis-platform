<?php

namespace App\Filament\Portal\Resources\PortalSourcingRequests;

use App\Filament\Portal\Resources\PortalSourcingRequests\Pages\CreatePortalSourcingRequest;
use App\Filament\Portal\Resources\PortalSourcingRequests\Pages\ListPortalSourcingRequests;
use App\Filament\Portal\Resources\PortalSourcingRequests\Schemas\PortalSourcingRequestForm;
use App\Filament\Portal\Resources\PortalSourcingRequests\Tables\PortalSourcingRequestsTable;
use App\Models\SourcingRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PortalSourcingRequestResource extends Resource
{
    protected static ?string $model = SourcingRequest::class;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isRole('customer') ?? false;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PortalSourcingRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PortalSourcingRequestsTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPortalSourcingRequests::route('/'),
            'create' => CreatePortalSourcingRequest::route('/create'),
        ];
    }
}
