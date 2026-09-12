<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CONVERTED_TO_ORDER = 'converted_to_order';
    case EXPIRED = 'expired';

    public function label(): string
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
}
