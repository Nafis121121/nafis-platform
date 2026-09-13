<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QuotationStatus: string implements HasLabel, HasColor
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CONVERTED_TO_ORDER = 'converted_to_order';
    case EXPIRED = 'expired';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => 'پیش‌نویس',
            self::SUBMITTED => 'ارسال‌شده',
            self::UNDER_REVIEW => 'در حال بررسی',
            self::APPROVED => 'تأییدشده',
            self::REJECTED => 'ردشده',
            self::CONVERTED_TO_ORDER => 'تبدیل‌شده به سفارش',
            self::EXPIRED => 'منقضی‌شده',
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
            self::SUBMITTED => 'info',
            self::UNDER_REVIEW => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::CONVERTED_TO_ORDER => 'primary',
            self::EXPIRED => 'danger',
        };
    }
}
