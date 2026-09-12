<?php

namespace App\Filament\Resources\SiteMenus\Schemas;

use App\Enums\ContentStatus;
use App\Enums\MenuLocation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SiteMenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('location')
                    ->options(MenuLocation::class)
                    ->required(),
                Select::make('status')
                    ->options(ContentStatus::class)
                    ->default('published')
                    ->required(),
            ]);
    }
}
