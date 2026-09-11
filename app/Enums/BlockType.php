<?php

namespace App\Enums;

enum BlockType: string
{
    case HERO = 'hero';
    case BANNER_SLIDER = 'banner_slider';
    case FEATURE_GRID = 'feature_grid';
    case CTA = 'cta';
    case RICH_TEXT = 'rich_text';
    case STATS = 'stats';
    case LOGOS = 'logos';
    case FAQ = 'faq';
    case CONTACT = 'contact';

    public function label(): string
    {
        return match ($this) {
            self::HERO => 'هیرو',
            self::BANNER_SLIDER => 'اسلایدر بنر',
            self::FEATURE_GRID => 'شبکه ویژگی‌ها',
            self::CTA => 'فراخوان اقدام',
            self::RICH_TEXT => 'متن غنی',
            self::STATS => 'آمار',
            self::LOGOS => 'لوگوها',
            self::FAQ => 'پرسش‌های متداول',
            self::CONTACT => 'تماس',
        };
    }
}
