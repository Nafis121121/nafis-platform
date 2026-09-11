<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select,TextInput,Textarea};

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference_code')->disabled(),
                Select::make('status')->options(collect(\App\Enums\OrderStatus::cases())->mapWithKeys(fn($x)=>[$x->value=>$x->value])->all())->required(),
                Select::make('payment_status')->options(collect(\App\Enums\PaymentStatus::cases())->mapWithKeys(fn($x)=>[$x->value=>$x->value])->all()),
                TextInput::make('total_irr')->numeric()->required(),
                TextInput::make('required_deposit_irr')->numeric(),
                Textarea::make('delivery_address')->columnSpanFull(),
                Textarea::make('internal_notes')->columnSpanFull(),
            ]);
    }
}
