<?php

namespace App\Filament\Portal\Resources\PortalSourcingRequests\Pages;

use App\Filament\Portal\Resources\PortalSourcingRequests\PortalSourcingRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePortalSourcingRequest extends CreateRecord
{
    protected static string $resource = PortalSourcingRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status'] = \App\Enums\SourcingRequestStatus::PENDING;

        return $data;
    }
}
