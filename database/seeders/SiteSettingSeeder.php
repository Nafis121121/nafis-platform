<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::updateOrCreate(
            ['singleton' => true],
            [
                'cms_enabled' => true,
                'brand' => [
                    'name' => 'نفیس تجارت',
                    'tagline' => 'واردات · ترخیص · پخش عمده',
                    'logoUrl' => '/assets/nafis-logo.jpg',
                    'topbar' => 'عضو رسمی اتاق بازرگانی · کارت بازرگانی معتبر · نماد اعتماد وزارت صمت',
                    'topbarActive' => true,
                ],
                'theme' => [
                    'primary' => '#9e1b32',
                    'primaryDeep' => '#6e1120',
                    'gold' => '#c9a84c',
                    'background' => '#ffffff',
                    'foreground' => '#2b2320',
                    'radius' => 0.9,
                    'fontKey' => 'vazirmatn',
                    'fontScale' => 100,
                ],
                'contact' => [
                    'phoneIntl' => '+989991222261',
                    'phoneDisplay' => '۰۹۹۹۱۲۲۲۲۶۱',
                    'whatsapp' => 'https://wa.me/989991222261',
                    'telegram' => 'https://t.me/Nafiskalaonline',
                    'telegramHandle' => '@Nafiskalaonline',
                    'instagram' => 'https://instagram.com/nafiskala.co',
                    'instagramHandle' => 'NAFISKALA.CO',
                    'email' => 'info@nafiskala.co',
                    'address' => 'تهران، میدان دوم صادقیه، برج گلدیس، پلاک ۱۵۱۸، طبقه ۵، واحد ۵۰۸',
                ],
                'seo' => [
                    'siteTitle' => 'نفیس تجارت | واردات و پخش عمده کالا',
                    'description' => 'بازرگانی نفیس تجارت: واردات مستقیم کالای دیجیتال، اسباب‌بازی و لوازم عکاسی از چین و دبی، ترخیص تخصصی و پخش عمده.',
                    'ogImage' => '/assets/og-image.jpg',
                    'keywords' => 'واردات کالا، ترخیص، پخش عمده، چین، دبی، کالای دیجیتال',
                ],
                'social' => [
                    'telegram' => 'https://t.me/Nafiskalaonline',
                    'instagram' => 'https://instagram.com/nafiskala.co',
                    'whatsapp' => 'https://wa.me/989991222261',
                ],
                'pricing' => [
                    'exchange_rate_cny' => 125000,
                    'exchange_rate_aed' => 245000,
                    'exchange_rate_usd' => 900000,
                    'default_margin_percentage' => 15,
                    'shipping_rate_per_kg' => 5.5,
                    'shipping_rate_per_cbm' => 180,
                    'rate_mode' => 'manual',
                    'rate_source' => null,
                    'rate_last_synced_at' => null,
                ],
            ]
        );
    }
}