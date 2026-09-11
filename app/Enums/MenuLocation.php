<?php

namespace App\Enums;

enum MenuLocation: string
{
    case HEADER = 'header';
    case FOOTER_SERVICES = 'footer_services';
    case FOOTER_INDUSTRIES = 'footer_industries';
    case FOOTER_LEGAL = 'footer_legal';
    case MOBILE = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::HEADER => 'منوی اصلی (هدر)',
            self::FOOTER_SERVICES => 'فوتر — خدمات',
            self::FOOTER_INDUSTRIES => 'فوتر — صنایع',
            self::FOOTER_LEGAL => 'فوتر — قوانین',
            self::MOBILE => 'منوی موبایل',
        };
    }
}
