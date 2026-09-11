<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\SitePage;
use Illuminate\Database\Seeder;

class MediaAssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            [
                'title' => 'لوگوی بازرگانی نفیس تجارت',
                'alt_fa' => 'لوگوی رسمی نفیس تجارت',
                'alt_en' => 'Nafis Tejarat Logo',
                'bucket' => 'site-media',
                'folder' => 'branding',
                'url' => '/assets/nafis-logo.jpg',
                'path' => 'branding/nafis-logo.jpg',
                'mime_type' => 'image/jpeg',
                'width' => 600,
                'height' => 600,
            ],
            [
                'title' => 'تصویر شاخص شبکه های اجتماعی (OG Image)',
                'alt_fa' => 'کارت پیش‌نمایش شبکه‌های اجتماعی نفیس تجارت',
                'alt_en' => 'Nafis Tejarat Social Card',
                'bucket' => 'site-media',
                'folder' => 'seo',
                'url' => '/assets/og-image.jpg',
                'path' => 'seo/og-image.jpg',
                'mime_type' => 'image/jpeg',
                'width' => 1200,
                'height' => 630,
            ],
            [
                'title' => 'بنر کالای دیجیتال',
                'alt_fa' => 'تأمین و واردات مستقیم کالای دیجیتال',
                'alt_en' => 'Digital Goods Sourcing',
                'bucket' => 'site-media',
                'folder' => 'banners',
                'url' => '/assets/slide-digital.png',
                'path' => 'banners/slide-digital.png',
                'mime_type' => 'image/png',
                'width' => 1920,
                'height' => 800,
            ],
            [
                'title' => 'بنر تجهیزات عکاسی',
                'alt_fa' => 'تأمین و واردات تجهیزات تخصصی عکاسی',
                'alt_en' => 'Photography Equipment Sourcing',
                'bucket' => 'site-media',
                'folder' => 'banners',
                'url' => '/assets/slide-photo.png',
                'path' => 'banners/slide-photo.png',
                'mime_type' => 'image/png',
                'width' => 1920,
                'height' => 800,
            ],
            [
                'title' => 'بنر اسباب‌بازی و سرگرمی',
                'alt_fa' => 'پخش و واردات انبوه اسباب‌بازی و سرگرمی',
                'alt_en' => 'Toys and Kids Wholesale',
                'bucket' => 'site-media',
                'folder' => 'banners',
                'url' => '/assets/slide-toys.png',
                'path' => 'banners/slide-toys.png',
                'mime_type' => 'image/png',
                'width' => 1920,
                'height' => 800,
            ],
        ];

        $ogAsset = null;
        foreach ($assets as $data) {
            $asset = MediaAsset::updateOrCreate(
                ['url' => $data['url']],
                $data
            );
            if ($data['folder'] === 'seo') {
                $ogAsset = $asset;
            }
        }

        if ($ogAsset) {
            SitePage::query()->update(['og_image_id' => $ogAsset->id]);
        }
    }
}