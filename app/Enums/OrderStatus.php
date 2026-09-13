<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OrderStatus: string implements HasLabel, HasColor
{
    case PENDING_PAYMENT = 'pending_payment';
    case DEPOSIT_PAID = 'deposit_paid';
    case IN_PROCUREMENT = 'in_procurement';
    case IN_PRODUCTION = 'in_production';
    case SHIPPED = 'shipped';
    case IN_CUSTOMS = 'in_customs';
    case READY_FOR_DELIVERY = 'ready_for_delivery';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'در انتظار پرداخت',
            self::DEPOSIT_PAID => 'پیش‌پرداخت واریز شد',
            self::IN_PROCUREMENT => 'در حال خرید و تأمین',
            self::IN_PRODUCTION => 'در حال تولید',
            self::SHIPPED => 'ارسال شد',
            self::IN_CUSTOMS => 'در گمرک',
            self::READY_FOR_DELIVERY => 'آماده تحویل',
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
            self::PENDING_PAYMENT => 'warning',
            self::DEPOSIT_PAID => 'info',
            self::IN_PROCUREMENT, self::IN_PRODUCTION => 'primary',
            self::SHIPPED, self::IN_CUSTOMS => 'info',
            self::READY_FOR_DELIVERY, self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
