<?php

namespace App\Filament\Portal\Resources\PortalSourcingRequests\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\{TextInput,Textarea,Select};

class PortalSourcingRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->label('عنوان کالای درخواستی')->required(),
                Textarea::make('technical_specifications')->label('مشخصات فنی')->required()->columnSpanFull(),
                Textarea::make('required_standards')->label('استانداردهای مورد نظر'),
                TextInput::make('estimated_quantity')->numeric()->label('تیراژ تخمینی')->required(),
                TextInput::make('target_price')->numeric()->label('قیمت هدف'),
                Select::make('target_currency')->options(['USD'=>'USD','CNY'=>'CNY','AED'=>'AED','IRR'=>'IRR'])->default('USD'),
            ]);
    }
}
