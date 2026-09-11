<?php

namespace App\Filament\Resources\CustomerInteractions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select,Textarea,DateTimePicker};

class CustomerInteractionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')->relationship('customer','name')->searchable()->preload()->required(),
                Select::make('staff_id')->relationship('staff','name')->searchable()->preload()->default(fn () => auth()->id())->required(),
                Select::make('type')->options(collect(\App\Enums\CustomerInteractionType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->value])->all())->required(),
                Textarea::make('summary')->required()->columnSpanFull(),
                Textarea::make('business_outcome')->columnSpanFull(),
                DateTimePicker::make('next_follow_up_at'),
                Select::make('quotation_id')->relationship('quotation','reference_code')->searchable(),
                Select::make('order_id')->relationship('order','reference_code')->searchable(),
                Select::make('sourcing_request_id')->relationship('sourcingRequest','reference_code')->searchable(),
            ]);
    }
}
