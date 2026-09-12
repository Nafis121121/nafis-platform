<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\MenuLocation;
use App\Models\MenuItem;
use App\Models\SiteMenu;
use App\Models\SitePage;
use Illuminate\Database\Seeder;

class SiteMenuSeeder extends Seeder
{
    public function run(): void
    {
        $pages = SitePage::all()->keyBy('slug');

        /* -------------------------------------------------------------------------- */
        /* 1. Header Menu                                                             */
        /* -------------------------------------------------------------------------- */
        $header = SiteMenu::updateOrCreate(
            ['location' => MenuLocation::HEADER->value],
            [
                'name' => 'منوی اصلی (هدر)',
                'status' => ContentStatus::PUBLISHED,
            ]
        );
        $header->items()->delete();

        // 1. Home
        $header->items()->create([
            'label_fa' => 'خانه',
            'label_en' => 'Home',
            'page_id' => $pages->get('home')?->id,
            'position' => 1,
            'visible' => true,
        ]);

        // 2. About
        $header->items()->create([
            'label_fa' => 'درباره نفیس',
            'label_en' => 'About',
            'page_id' => $pages->get('about')?->id,
            'position' => 2,
            'visible' => true,
        ]);

        // 3. Services (with children)
        $servicesItem = $header->items()->create([
            'label_fa' => 'خدمات',
            'label_en' => 'Services',
            'page_id' => $pages->get('services')?->id,
            'position' => 3,
            'visible' => true,
        ]);
        $header->items()->create([
            'parent_id' => $servicesItem->id,
            'label_fa' => 'سورسینگ و تأمین کالا',
            'label_en' => 'Sourcing',
            'href' => '/services',
            'position' => 1,
            'visible' => true,
        ]);
        $header->items()->create([
            'parent_id' => $servicesItem->id,
            'label_fa' => 'کنترل کیفیت و بازرسی',
            'label_en' => 'Quality Control',
            'href' => '/process',
            'position' => 2,
            'visible' => true,
        ]);
        $header->items()->create([
            'parent_id' => $servicesItem->id,
            'label_fa' => 'حمل هوایی و دریایی',
            'label_en' => 'Freight',
            'href' => '/services',
            'position' => 3,
            'visible' => true,
        ]);
        $header->items()->create([
            'parent_id' => $servicesItem->id,
            'label_fa' => 'ترخیص تخصصی گمرک',
            'label_en' => 'Customs Clearance',
            'href' => '/services',
            'position' => 4,
            'visible' => true,
        ]);

        // 4. Industries (with children)
        $industriesItem = $header->items()->create([
            'label_fa' => 'صنایع',
            'label_en' => 'Industries',
            'page_id' => $pages->get('industries')?->id,
            'position' => 4,
            'visible' => true,
        ]);
        $header->items()->create([
            'parent_id' => $industriesItem->id,
            'label_fa' => 'کالای دیجیتال',
            'label_en' => 'Digital Goods',
            'href' => '/industries',
            'position' => 1,
            'visible' => true,
        ]);
        $header->items()->create([
            'parent_id' => $industriesItem->id,
            'label_fa' => 'تجهیزات عکاسی و تصویربرداری',
            'label_en' => 'Photo Equipment',
            'href' => '/industries',
            'position' => 2,
            'visible' => true,
        ]);
        $header->items()->create([
            'parent_id' => $industriesItem->id,
            'label_fa' => 'اسباب‌بازی و سرگرمی',
            'label_en' => 'Toys & Kids',
            'href' => '/industries',
            'position' => 3,
            'visible' => true,
        ]);

        // 5. Catalog
        $header->items()->create([
            'label_fa' => 'کاتالوگ عمده',
            'label_en' => 'Catalog',
            'href' => '/catalog',
            'position' => 5,
            'visible' => true,
        ]);

        // 6. Process
        $header->items()->create([
            'label_fa' => 'فرآیند تأمین',
            'label_en' => 'Process',
            'page_id' => $pages->get('process')?->id,
            'position' => 6,
            'visible' => true,
        ]);

        // 7. B2B
        $header->items()->create([
            'label_fa' => 'همکاری تجاری',
            'label_en' => 'B2B',
            'page_id' => $pages->get('b2b')?->id,
            'position' => 7,
            'visible' => true,
        ]);

        // 8. Contact
        $header->items()->create([
            'label_fa' => 'تماس با ما',
            'label_en' => 'Contact',
            'page_id' => $pages->get('contact')?->id,
            'position' => 8,
            'visible' => true,
        ]);

        /* -------------------------------------------------------------------------- */
        /* 2. Footer Services Menu                                                    */
        /* -------------------------------------------------------------------------- */
        $footerServices = SiteMenu::updateOrCreate(
            ['location' => MenuLocation::FOOTER_SERVICES->value],
            [
                'name' => 'فوتر — خدمات',
                'status' => ContentStatus::PUBLISHED,
            ]
        );
        $footerServices->items()->delete();

        $footerServices->items()->createMany([
            ['label_fa' => 'سورسینگ و تأمین کالا', 'page_id' => $pages->get('services')?->id, 'position' => 1, 'visible' => true],
            ['label_fa' => 'کنترل کیفیت و بازرسی', 'page_id' => $pages->get('process')?->id, 'position' => 2, 'visible' => true],
            ['label_fa' => 'محاسبه‌گر هزینه واردات', 'href' => '/calculator', 'position' => 3, 'visible' => true],
            ['label_fa' => 'استعلام هوشمند تصویری', 'href' => '/sourcing', 'position' => 4, 'visible' => true],
        ]);

        /* -------------------------------------------------------------------------- */
        /* 3. Footer Industries Menu                                                  */
        /* -------------------------------------------------------------------------- */
        $footerIndustries = SiteMenu::updateOrCreate(
            ['location' => MenuLocation::FOOTER_INDUSTRIES->value],
            [
                'name' => 'فوتر — صنایع',
                'status' => ContentStatus::PUBLISHED,
            ]
        );
        $footerIndustries->items()->delete();

        $footerIndustries->items()->createMany([
            ['label_fa' => 'کالای دیجیتال', 'page_id' => $pages->get('industries')?->id, 'position' => 1, 'visible' => true],
            ['label_fa' => 'تجهیزات عکاسی', 'page_id' => $pages->get('industries')?->id, 'position' => 2, 'visible' => true],
            ['label_fa' => 'اسباب‌بازی و سرگرمی', 'page_id' => $pages->get('industries')?->id, 'position' => 3, 'visible' => true],
            ['label_fa' => 'کاتالوگ عمده کالا', 'href' => '/catalog', 'position' => 4, 'visible' => true],
            ['label_fa' => 'ورود به پنل مشتریان', 'href' => '/dashboard', 'position' => 5, 'visible' => true],
        ]);

        /* -------------------------------------------------------------------------- */
        /* 4. Footer Legal Menu                                                       */
        /* -------------------------------------------------------------------------- */
        $footerLegal = SiteMenu::updateOrCreate(
            ['location' => MenuLocation::FOOTER_LEGAL->value],
            [
                'name' => 'فوتر — قوانین و مقررات',
                'status' => ContentStatus::PUBLISHED,
            ]
        );
        $footerLegal->items()->delete();

        $footerLegal->items()->createMany([
            ['label_fa' => 'قوانین و ضوابط بازرگانی', 'href' => '/terms', 'position' => 1, 'visible' => true],
            ['label_fa' => 'حریم خصوصی و امنیت داده‌ها', 'href' => '/privacy', 'position' => 2, 'visible' => true],
            ['label_fa' => 'شرایط بازرسی و گارانتی کالا', 'href' => '/warranty', 'position' => 3, 'visible' => true],
            ['label_fa' => 'دستورالعمل پرداخت و تسویه ارزی', 'href' => '/payment-terms', 'position' => 4, 'visible' => true],
        ]);

        /* -------------------------------------------------------------------------- */
        /* 5. Mobile Menu                                                             */
        /* -------------------------------------------------------------------------- */
        $mobileMenu = SiteMenu::updateOrCreate(
            ['location' => MenuLocation::MOBILE->value],
            [
                'name' => 'منوی موبایل',
                'status' => ContentStatus::PUBLISHED,
            ]
        );
        $mobileMenu->items()->delete();

        $mobileMenu->items()->createMany([
            ['label_fa' => 'خانه', 'page_id' => $pages->get('home')?->id, 'position' => 1, 'visible' => true],
            ['label_fa' => 'درباره ما', 'page_id' => $pages->get('about')?->id, 'position' => 2, 'visible' => true],
            ['label_fa' => 'خدمات بازرگانی', 'page_id' => $pages->get('services')?->id, 'position' => 3, 'visible' => true],
            ['label_fa' => 'صنایع تخصصی', 'page_id' => $pages->get('industries')?->id, 'position' => 4, 'visible' => true],
            ['label_fa' => 'کاتالوگ عمده', 'href' => '/catalog', 'position' => 5, 'visible' => true],
            ['label_fa' => 'فرآیند تأمین ۵ مرحله‌ای', 'page_id' => $pages->get('process')?->id, 'position' => 6, 'visible' => true],
            ['label_fa' => 'همکاری سازمانی (B2B)', 'page_id' => $pages->get('b2b')?->id, 'position' => 7, 'visible' => true],
            ['label_fa' => 'تماس با ما', 'page_id' => $pages->get('contact')?->id, 'position' => 8, 'visible' => true],
        ]);
    }
}