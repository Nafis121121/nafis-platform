<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AiProductDraftStatus: string implements HasLabel, HasColor
{
    case PENDING = 'pending';
    case REVIEWING = 'reviewing';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case IMPORTED = 'imported';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PENDING => 'در انتظار بررسی',
            self::REVIEWING => 'در حال بررسی',
            self::APPROVED => 'تأییدشده',
            self::REJECTED => 'ردشده',
            self::IMPORTED => 'تبدیل‌شده به محصول',
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
            self::REVIEWING => 'info',
            self::APPROVED => 'primary',
            self::REJECTED => 'danger',
            self::IMPORTED => 'success',
        };
    }
}
