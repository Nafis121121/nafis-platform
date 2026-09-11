<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case DRAFT = 'draft';
    case BOOKED = 'booked';
    case IN_TRANSIT_ORIGIN = 'in_transit_origin';
    case ON_WATER_AIR = 'on_water_air';
    case CUSTOMS_CLEARANCE = 'customs_clearance';
    case DELIVERED_TO_WAREHOUSE = 'delivered_to_warehouse';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
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
}
