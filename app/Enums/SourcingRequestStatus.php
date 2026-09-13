<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SourcingRequestStatus: string implements HasLabel, HasColor
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case QUOTED = 'quoted';
    case SUPPLIER_FOUND = 'supplier_found';
    case REJECTED = 'rejected';
    case CLOSED = 'closed';

    public function getLabel(): ?string
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

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'info',
            self::QUOTED => 'success',
            self::SUPPLIER_FOUND => 'primary',
            self::REJECTED => 'danger',
            self::CLOSED => 'gray',
        };
    }
}
