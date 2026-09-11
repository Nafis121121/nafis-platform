<?php

namespace App\Filament\Resources\WholesaleRules\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\{Select,TextInput,DatePicker,Toggle};

class WholesaleRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')->relationship('product','name_fa')->searchable()->preload(),
                Select::make('category_id')->relationship('category','name_fa')->searchable()->preload(),
                TextInput::make('min_quantity')->numeric()->required(),
                TextInput::make('max_quantity')->numeric(),
                TextInput::make('discount_percentage')->numeric()->required(),
                DatePicker::make('starts_at'), DatePicker::make('ends_at'), Toggle::make('is_active')->default(true),
            ]);
    }
}
