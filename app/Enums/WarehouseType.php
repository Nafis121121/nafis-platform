<?php

namespace App\Enums;

enum WarehouseType: string
{
    case ORIGIN = 'origin';
    case TRANSIT = 'transit';
    case CUSTOMS = 'customs';
    case DESTINATION = 'destination';

    public function label(): string
    {
        return match ($this) {
            self::ORIGIN => 'مبدأ',
            self::TRANSIT => 'ترانزیت',
            self::CUSTOMS => 'گمرک',
            self::DESTINATION => 'مقصد',
        };
    }
}
