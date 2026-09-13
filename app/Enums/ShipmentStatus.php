<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ShipmentStatus: string implements HasLabel, HasColor
{
    case DRAFT = 'draft';
    case BOOKED = 'booked';
    case IN_TRANSIT_ORIGIN = 'in_transit_origin';
    case ON_WATER_AIR = 'on_water_air';
    case CUSTOMS_CLEARANCE = 'customs_clearance';
    case DELIVERED_TO_WAREHOUSE = 'delivered_to_warehouse';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'پیش‌نویس',
            self::BOOKED => 'رزرو حمل',
            self::IN_TRANSIT_ORIGIN => 'در حال انتقال از مبدأ',
            self::ON_WATER_AIR => 'در مسیر دریایی/هوایی',
            self::CUSTOMS_CLEARANCE => 'ترخیص گمرکی',
            self::DELIVERED_TO_WAREHOUSE => 'تحویل انبار',
            self::COMPLETED => 'تکمیل‌شده',
            self::CANCELLED => 'لغوشده',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::BOOKED => 'warning',
            self::IN_TRANSIT_ORIGIN, self::ON_WATER_AIR => 'info',
            self::CUSTOMS_CLEARANCE => 'primary',
            self::DELIVERED_TO_WAREHOUSE, self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
