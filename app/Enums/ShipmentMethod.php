<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ShipmentMethod: string implements HasLabel
{
    case SEA = 'sea';
    case AIR = 'air';
    case LAND = 'land';
    case EXPRESS = 'express';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SEA => 'دریایی',
            self::AIR => 'هوایی',
            self::LAND => 'زمینی',
            self::EXPRESS => 'اکسپرس (سریع)',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }
}
