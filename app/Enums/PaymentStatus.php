<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasColor
{
    case UNPAID = 'unpaid';
    case PARTIALLY_PAID = 'partially_paid';
    case FULLY_PAID = 'fully_paid';
    case REFUNDED = 'refunded';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::UNPAID => 'پرداخت نشده',
            self::PARTIALLY_PAID => 'پرداخت ناقص (مرحله‌ای)',
            self::FULLY_PAID => 'تسویه کامل',
            self::REFUNDED => 'مسترد شده',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::UNPAID => 'danger',
            self::PARTIALLY_PAID => 'warning',
            self::FULLY_PAID => 'success',
            self::REFUNDED => 'gray',
        };
    }
}
