<?php

namespace App\Enums;

enum SupplierStatus: string
{
    case ACTIVE = 'active';
    case PENDING = 'pending';
    case BLACKLISTED = 'blacklisted';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'فعال',
            self::PENDING => 'در انتظار بررسی',
            self::BLACKLISTED => 'مسدود',
        };
    }
}
