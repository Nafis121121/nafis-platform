<?php

namespace App\Enums;

enum SourcingRequestStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case QUOTED = 'quoted';
    case SUPPLIER_FOUND = 'supplier_found';
    case REJECTED = 'rejected';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'در انتظار بررسی',
            self::IN_PROGRESS => 'در حال بررسی',
            self::QUOTED => 'استعلام صادر شد',
            self::SUPPLIER_FOUND => 'تأمین‌کننده پیدا شد',
            self::REJECTED => 'رد شده',
            self::CLOSED => 'بسته شده',
        };
    }
}
