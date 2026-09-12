<?php

namespace Database\Seeders;

use App\Enums\BlockType;
use App\Enums\ContentStatus;
use App\Models\ContentBlock;
use App\Models\SitePage;
use Illuminate\Database\Seeder;

class SitePageAndBlockSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        /* -------------------------------------------------------------------------- */
        /* 1. Home Page & Blocks                                                      */
        /* -------------------------------------------------------------------------- */
        $home = SitePage::updateOrCreate(
            ['slug' => 'home'],
            [
                'route_path' => '/',
                'title_fa' => 'صفحه اصلی',
                'title_en' => 'Home',
                'summary_fa' => 'تأمین هوشمند کالا از بازارهای جهانی، واردات مستقیم از چین و دبی، ترخیص تخصصی و پخش عمده',
                'status' => ContentStatus::PUBLISHED,
                'is_system' => true,
                'position' => 1,
                'seo_title' => 'نفیس تجارت | واردات هوشمند کالا و پخش عمده',
                'seo_description' => 'سورسینگ، کنترل کیفیت، حمل هوایی و دریایی، ترخیص تخصصی کالای دیجیتال، اسباب‌بازی و لوازم عکاسی از چین و دبی.',
                'seo_keywords' => 'واردات کالا, ترخیص کالا, بازرگانی, دبی, چین, کالای دیجیتال, اسباب‌بازی عمده, لوازم عکاسی',
                'noindex' => false,
                'published_at' => $now,
            ]
        );

        $homeBlocks = [
            [
                'block_key' => 'home_hero',
                'type' => BlockType::HERO,
                'position' => 1,
                'data' => [
                    'badge' => 'چین 🇨🇳 ← دبی 🇦🇪 ← ایران 🇮🇷',
                    'title' => 'تأمین هوشمند کالا از بازارهای جهانی با',
                    'highlight' => 'نفیس تجارت',
                    'subtitle' => 'تأمین مستقیم از کارخانه، حمل هوایی سریع، ترخیص تخصصی و پخش عمده کالای دیجیتال، اسباب‌بازی و لوازم عکاسی — با شفافیت کامل هزینه‌ها و نرخ ارز لحظه‌ای.',
                    'ctaPrimary' => 'ثبت درخواست تأمین',
                    'ctaPrimaryUrl' => '/requests/new',
                    'ctaSecondary' => 'شروع همکاری تجاری',
                    'ctaSecondaryUrl' => '/b2b',
                    'imageUrl' => '/assets/hero-bg.jpg',
                    'overlay' => 45,
                    'show3d' => true,
                ],
            ],
            [
                'block_key' => 'home_stats',
                'type' => BlockType::STATS,
                'position' => 2,
                'data' => [
                    'stats' => [
                        ['title' => '۳ صنعت تخصصی', 'subtitle' => 'دیجیتال، عکاسی، اسباب‌بازی'],
                        ['title' => 'چین • دبی • ایران', 'subtitle' => 'مسیرهای فعال تأمین'],
                        ['title' => '۵ گام شفاف', 'subtitle' => 'از درخواست تا تحویل'],
                    ],
                ],
            ],
            [
                'block_key' => 'home_banners',
                'type' => BlockType::BANNER_SLIDER,
                'position' => 3,
                'data' => [
                    'slides' => [
                        [
                            'id' => 1,
                            'title' => 'واردات و تأمین کالاهای دیجیتال',
                            'subtitle' => 'راهکار مطمئن برای واردات، تأمین و پخش انواع محصولات دیجیتال با کیفیت و قیمت رقابتی',
                            'imageUrl' => '/assets/slide-digital.png',
                            'link' => '/catalog',
                            'active' => true,
                        ],
                        [
                            'id' => 2,
                            'title' => 'واردات و تأمین لوازم عکاسی',
                            'subtitle' => 'تأمین و واردات انواع تجهیزات عکاسی حرفه‌ای با کیفیت بالا، اصالت کالا و قیمت رقابتی',
                            'imageUrl' => '/assets/slide-photo.png',
                            'link' => '/catalog',
                            'active' => true,
                        ],
                        [
                            'id' => 3,
                            'title' => 'واردات و تأمین اسباب‌بازی',
                            'subtitle' => 'واردات و پخش انواع اسباب‌بازی، بازی‌های آموزشی و محصولات کودک با تنوع بالا و قیمت مناسب',
                            'imageUrl' => '/assets/slide-toys.png',
                            'link' => '/catalog',
                            'active' => true,
                        ],
                    ],
                ],
            ],
            [
                'block_key' => 'home_industries',
                'type' => BlockType::FEATURE_GRID,
                'position' => 4,
                'data' => [
                    'eyebrow' => 'Industries We Serve',
                    'title' => 'راهکارهای تجاری در سه صنعت تخصصی',
                    'description' => 'نفیس در این صنایع، زنجیره کامل تأمین را برای کسب‌وکارها اجرا می‌کند: شناسایی تأمین‌کننده، مذاکره، کنترل کیفیت، حمل و ترخیص.',
                    'items' => [
                        [
                            'title' => 'کالای دیجیتال',
                            'en' => 'Digital Goods',
                            'text' => 'تأمین لوازم جانبی و گجت‌های دیجیتال از تولیدکنندگان معتبر، با بازرسی فنی، تست عملکرد و مدیریت گارانتی واردات.',
                            'points' => ['مذاکره مستقیم با کارخانه', 'کنترل کیفیت پیش از حمل', 'استاندارد و مجوز واردات'],
                        ],
                        [
                            'title' => 'تجهیزات عکاسی و تصویربرداری',
                            'en' => 'Photography Equipment',
                            'text' => 'تأمین دوربین، لنز، نورپردازی و تجهیزات استودیویی برای فروشگاه‌های تخصصی، استودیوها و پروژه‌های سازمانی.',
                            'points' => ['تأمین برندهای تخصصی', 'بسته‌بندی ایمن و بیمه بار', 'حمل هوایی سریع'],
                        ],
                        [
                            'title' => 'اسباب‌بازی و سرگرمی',
                            'en' => 'Toys & Kids',
                            'text' => 'تأمین انبوه اسباب‌بازی با رعایت الزامات ایمنی کودک، تطبیق با ضوابط واردات و پشتیبانی از سفارش‌های اختصاصی.',
                            'points' => ['گواهی ایمنی کودک', 'سفارش‌سازی و برندینگ', 'تأمین در حجم انبوه'],
                        ],
                    ],
                ],
            ],
            [
                'block_key' => 'home_how_it_works',
                'type' => BlockType::FEATURE_GRID,
                'position' => 5,
                'data' => [
                    'eyebrow' => 'How Nafis Works',
                    'title' => 'فرآیند تأمین در ۵ گام',
                    'description' => 'یک مسیر شفاف و قابل پیگیری، از ثبت درخواست تا تحویل کالا در انبار شما.',
                    'steps' => [
                        ['step' => '۰۱', 'title' => 'ثبت درخواست مشتری', 'text' => 'مشخصات کالا، حجم سفارش و استانداردهای موردنیاز را ثبت می‌کنید.'],
                        ['step' => '۰۲', 'title' => 'تطبیق تأمین‌کننده', 'text' => 'تیم نفیس تأمین‌کنندگان معتبر را ارزیابی و بهترین گزینه‌ها را انتخاب می‌کند.'],
                        ['step' => '۰۳', 'title' => 'کنترل کیفیت', 'text' => 'پیش‌فاکتور شفاف ارائه می‌شود و پیش از حمل، بازرسی و کنترل کیفیت مستند انجام می‌گیرد.'],
                        ['step' => '۰۴', 'title' => 'مدیریت حمل و ترخیص', 'text' => 'حمل هوایی یا دریایی، تشریفات گمرکی و ترخیص تخصصی با پیگیری لحظه‌ای.'],
                        ['step' => '۰۵', 'title' => 'تحویل نهایی', 'text' => 'انبارداری، توزیع داخلی و پشتیبانی مستمر برای سفارش‌های بعدی.'],
                    ],
                ],
            ],
            [
                'block_key' => 'home_why_nafis',
                'type' => BlockType::FEATURE_GRID,
                'position' => 6,
                'data' => [
                    'eyebrow' => 'Why Choose Nafis',
                    'title' => 'چرا کسب‌وکارها نفیس را انتخاب می‌کنند',
                    'description' => 'ما تنها یک واسطه خرید نیستیم؛ شریک عملیاتی شما در تأمین، کنترل کیفیت، حمل و واردات هستیم.',
                    'cards' => [
                        ['title' => 'شبکه جهانی تأمین‌کنندگان', 'text' => 'دسترسی مستقیم به کارخانه‌ها و تأمین‌کنندگان ارزیابی‌شده در چین، امارات و بازارهای منطقه.'],
                        ['title' => 'تضمین کیفیت', 'text' => 'بازرسی کالا پیش از حمل، تطبیق با مشخصات فنی سفارش و گزارش مستند کنترل کیفیت.'],
                        ['title' => 'تخصص در واردات', 'text' => 'ثبت سفارش، تشریفات گمرکی، ترخیص و مدیریت اسناد تجاری توسط کارشناسان بازرگانی.'],
                        ['title' => 'پشتیبانی کسب‌وکار', 'text' => 'کارشناس اختصاصی، گزارش‌های دوره‌ای و مشاوره برای توسعه پایدار زنجیره تأمین شما.'],
                    ],
                ],
            ],
            [
                'block_key' => 'home_cta',
                'type' => BlockType::CTA,
                'position' => 7,
                'data' => [
                    'title' => 'آماده شروع همکاری هستید؟',
                    'description' => 'درخواست تأمین خود را ثبت کنید تا کارشناسان نفیس گزینه‌های تأمین و پیش‌فاکتور شفاف را برای شما آماده کنند.',
                    'primaryBtnText' => 'درخواست تأمین کالا',
                    'primaryBtnLink' => '/requests/new',
                    'secondaryBtnText' => 'گفتگو با کارشناس',
                    'secondaryBtnLink' => '/contact',
                ],
            ],
        ];

        foreach ($homeBlocks as $b) {
            ContentBlock::updateOrCreate(
                ['page_id' => $home->id, 'block_key' => $b['block_key']],
                [
                    'type' => $b['type'],
                    'position' => $b['position'],
                    'draft_data' => $b['data'],
                    'published_data' => $b['data'],
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => $now,
                ]
            );
        }

        /* -------------------------------------------------------------------------- */
        /* 2. About Page & Blocks                                                     */
        /* -------------------------------------------------------------------------- */
        $about = SitePage::updateOrCreate(
            ['slug' => 'about'],
            [
                'route_path' => '/about',
                'title_fa' => 'درباره نفیس تجارت',
                'title_en' => 'About Us',
                'summary_fa' => 'پیشینه فعالیت، مجوزهای رسمی، عضویت اتاق بازرگانی و اهداف تجاری نفیس تجارت',
                'status' => ContentStatus::PUBLISHED,
                'is_system' => true,
                'position' => 2,
                'seo_title' => 'درباره بازرگانی نفیس تجارت | واردات و پخش عمده',
                'seo_description' => 'بازرگانی نفیس تجارت با سال‌ها تجربه در واردات و پخش عمده کالای دیجیتال، اسباب‌بازی و لوازم عکاسی از چین و دبی.',
                'seo_keywords' => 'درباره نفیس تجارت, شرکت بازرگانی, کارت بازرگانی, اتاق بازرگانی',
                'noindex' => false,
                'published_at' => $now,
            ]
        );

        $aboutBlocks = [
            [
                'block_key' => 'about_intro',
                'type' => BlockType::RICH_TEXT,
                'position' => 1,
                'data' => [
                    'title' => 'درباره شرکت بازرگانی نفیس تجارت',
                    'content' => 'شرکت بازرگانی نفیس تجارت با سال‌ها تجربه در زمینه واردات و پخش عمده، فعالیت خود را در سه حوزه کالای دیجیتال، اسباب‌بازی و لوازم عکاسی متمرکز کرده است. خرید عمده مستقیم از تامین‌کنندگان معتبر در چین، دبی و سایر کشورها و ترخیص تخصصی کالا، امکان ارائه قیمت رقابتی به مشتریان عمده و خرده‌فروشان را فراهم کرده است.',
                ],
            ],
            [
                'block_key' => 'about_badges',
                'type' => BlockType::STATS,
                'position' => 2,
                'data' => [
                    'items' => [
                        ['title' => 'عضویت اتاق بازرگانی', 'description' => 'عضو رسمی اتاق بازرگانی، صنایع، معادن و کشاورزی'],
                        ['title' => 'نماد اعتماد وزارت صمت', 'description' => 'دارای نماد اعتماد الکترونیکی معتبر'],
                        ['title' => 'کارت بازرگانی معتبر', 'description' => 'مجوزهای قانونی واردات و تشریفات گمرکی'],
                    ],
                ],
            ],
            [
                'block_key' => 'about_values',
                'type' => BlockType::FEATURE_GRID,
                'position' => 3,
                'data' => [
                    'title' => 'ارزش‌ها و تعهدات کاری ما',
                    'items' => [
                        ['title' => 'شفافیت ارزی و مالی', 'text' => 'ارائه پروفرما و فاکتور شفاف بدون هزینه‌های پنهان'],
                        ['title' => 'تعهد به زمان‌بندی', 'text' => 'بهینه‌سازی حداکثری فرآیندهای حمل هوایی و ترخیص سریع'],
                        ['title' => 'کنترل کیفیت در مبدأ', 'text' => 'بازرسی حضوری در گوانگجو و دبی پیش از تسویه با تأمین‌کننده'],
                    ],
                ],
            ],
        ];

        foreach ($aboutBlocks as $b) {
            ContentBlock::updateOrCreate(
                ['page_id' => $about->id, 'block_key' => $b['block_key']],
                [
                    'type' => $b['type'],
                    'position' => $b['position'],
                    'draft_data' => $b['data'],
                    'published_data' => $b['data'],
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => $now,
                ]
            );
        }

        /* -------------------------------------------------------------------------- */
        /* 3. Services Page & Blocks                                                  */
        /* -------------------------------------------------------------------------- */
        $services = SitePage::updateOrCreate(
            ['slug' => 'services'],
            [
                'route_path' => '/services',
                'title_fa' => 'خدمات بازرگانی و واردات',
                'title_en' => 'Services',
                'summary_fa' => 'زنجیره کامل خدمات تأمین و واردات B2B از بازارهای بین‌المللی',
                'status' => ContentStatus::PUBLISHED,
                'is_system' => true,
                'position' => 3,
                'seo_title' => 'خدمات بازرگانی و واردات | نفیس تجارت',
                'seo_description' => 'خدمات سورسینگ و شناسایی تأمین‌کننده، بازرسی و کنترل کیفیت، حمل بین‌المللی، ترخیص گمرکی، مدیریت اسناد و توزیع داخلی.',
                'seo_keywords' => 'خدمات بازرگانی, سورسینگ کالا, ترخیص گمرکی, حمل هوایی چین',
                'noindex' => false,
                'published_at' => $now,
            ]
        );

        $servicesBlocks = [
            [
                'block_key' => 'services_hero',
                'type' => BlockType::HERO,
                'position' => 1,
                'data' => [
                    'badge' => 'Global B2B Solutions',
                    'title' => 'زنجیره کامل خدمات بازرگانی و واردات',
                    'subtitle' => 'از شناسایی کارخانه و ارزیابی کیفیت تا ترخیص تخصصی و تحویل نهایی در انبار شما.',
                ],
            ],
            [
                'block_key' => 'services_list',
                'type' => BlockType::FEATURE_GRID,
                'position' => 2,
                'data' => [
                    'services' => [
                        [
                            'title' => 'سورسینگ و شناسایی تأمین‌کننده',
                            'en' => 'Sourcing & Supplier Discovery',
                            'text' => 'ارزیابی کارخانه‌ها و تأمین‌کنندگان در چین و امارات، مذاکره قیمت و انتخاب بهترین گزینه برای مشخصات فنی شما.',
                        ],
                        [
                            'title' => 'بازرسی و کنترل کیفیت',
                            'en' => 'Quality Control & Inspection',
                            'text' => 'بازرسی پیش از حمل، تست عملکرد، بررسی بسته‌بندی و ارائه گزارش مستند کنترل کیفیت.',
                        ],
                        [
                            'title' => 'حمل بین‌المللی',
                            'en' => 'International Freight',
                            'text' => 'حمل هوایی و دریایی با بیمه بار، کنسولیدیشن محموله‌ها و بهینه‌سازی هزینه حمل.',
                        ],
                        [
                            'title' => 'ترخیص و امور گمرکی',
                            'en' => 'Customs Clearance',
                            'text' => 'ثبت سفارش، تأمین ارز، تشریفات گمرکی و ترخیص تخصصی توسط کارشناسان بازرگانی.',
                        ],
                        [
                            'title' => 'مدیریت اسناد تجاری',
                            'en' => 'Trade Documentation',
                            'text' => 'پروفرما، قرارداد، اسناد حمل و گواهی‌های استاندارد؛ همه در پرونده اختصاصی شما.',
                        ],
                        [
                            'title' => 'انبارداری و توزیع داخلی',
                            'en' => 'Warehousing & Distribution',
                            'text' => 'تحویل در انبار شما، انبارداری موقت و توزیع سراسری با پیگیری وضعیت ارسال.',
                        ],
                    ],
                ],
            ],
            [
                'block_key' => 'services_cta',
                'type' => BlockType::CTA,
                'position' => 3,
                'data' => [
                    'title' => 'پروژه واردات اختصاصی خود را کلید بزنید',
                    'description' => 'مشخصات کالای مورد نظر را ثبت نمایید تا برآورد هزینه و زمان‌بندی دقیق برای شما تهیه شود.',
                    'primaryBtnText' => 'ثبت درخواست استعلام',
                    'primaryBtnLink' => '/requests/new',
                ],
            ],
        ];

        foreach ($servicesBlocks as $b) {
            ContentBlock::updateOrCreate(
                ['page_id' => $services->id, 'block_key' => $b['block_key']],
                [
                    'type' => $b['type'],
                    'position' => $b['position'],
                    'draft_data' => $b['data'],
                    'published_data' => $b['data'],
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => $now,
                ]
            );
        }

        /* -------------------------------------------------------------------------- */
        /* 4. Industries Page & Blocks                                                */
        /* -------------------------------------------------------------------------- */
        $industries = SitePage::updateOrCreate(
            ['slug' => 'industries'],
            [
                'route_path' => '/industries',
                'title_fa' => 'صنایع تخصصی',
                'title_en' => 'Industries',
                'summary_fa' => 'راهکارهای تجاری در صنایع کالای دیجیتال، تجهیزات عکاسی و اسباب‌بازی',
                'status' => ContentStatus::PUBLISHED,
                'is_system' => true,
                'position' => 4,
                'seo_title' => 'صنایع و حوزه‌های فعالیت | نفیس تجارت',
                'seo_description' => 'تأمین و واردات تخصصی کالای دیجیتال، لوازم عکاسی و تصویربرداری، اسباب‌بازی و سرگرمی کودک.',
                'seo_keywords' => 'کالای دیجیتال, تجهیزات عکاسی, اسباب‌بازی عمده, تأمین صنعتی',
                'noindex' => false,
                'published_at' => $now,
            ]
        );

        $industriesBlocks = [
            [
                'block_key' => 'industries_hero',
                'type' => BlockType::HERO,
                'position' => 1,
                'data' => [
                    'badge' => 'Focused Verticals',
                    'title' => 'تخصص عمیق در سه صنعت راهبردی',
                    'subtitle' => 'تسلط بر استانداردها، الزامات قانونی، زنجیره تأمین و برترین کارخانه‌های تولیدی در شرق آسیا و خاورمیانه.',
                ],
            ],
            [
                'block_key' => 'industries_details',
                'type' => BlockType::FEATURE_GRID,
                'position' => 2,
                'data' => [
                    'industries' => [
                        [
                            'title' => 'کالای دیجیتال و گجت‌های هوشمند',
                            'description' => 'واردات انواع اکسسوری، هاب‌ها، کابل‌های استاندارد، تجهیزات ذخیره‌سازی، هندزفری و ساعت هوشمند با استانداردهای CE و FCC.',
                        ],
                        [
                            'title' => 'تجهیزات عکاسی، فیلم‌برداری و نورپردازی',
                            'description' => 'تأمین نورهای استودیویی، رینگ‌لایت، سه‌پایه‌های حرفه‌ای، گیمبال، لنزها و تجهیزات تولید محتوا برای استودیوها و فروشگاه‌های تخصصی.',
                        ],
                        [
                            'title' => 'اسباب‌بازی و محصولات کودک',
                            'description' => 'واردات انبوه اسباب‌بازی‌های هوشمند، کنترلی، آموزشی، پازل و لگو با گواهی سلامت و ایمنی اسباب‌بازی (EN71).',
                        ],
                    ],
                ],
            ],
            [
                'block_key' => 'industries_cta',
                'type' => BlockType::CTA,
                'position' => 3,
                'data' => [
                    'title' => 'دسترسی به محصولات موجود در انبار',
                    'description' => 'کاتالوگ عمده کالاهای آماده تحویل را بررسی کنید و یا سفارش تأمین اختصاصی ثبت نمایید.',
                    'primaryBtnText' => 'مشاهده کاتالوگ عمده',
                    'primaryBtnLink' => '/catalog',
                ],
            ],
        ];

        foreach ($industriesBlocks as $b) {
            ContentBlock::updateOrCreate(
                ['page_id' => $industries->id, 'block_key' => $b['block_key']],
                [
                    'type' => $b['type'],
                    'position' => $b['position'],
                    'draft_data' => $b['data'],
                    'published_data' => $b['data'],
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => $now,
                ]
            );
        }

        /* -------------------------------------------------------------------------- */
        /* 5. Process Page & Blocks                                                   */
        /* -------------------------------------------------------------------------- */
        $process = SitePage::updateOrCreate(
            ['slug' => 'process'],
            [
                'route_path' => '/process',
                'title_fa' => 'فرآیند تأمین',
                'title_en' => 'Sourcing Process',
                'summary_fa' => 'مسیر شفاف و قابل پیگیری تأمین کالا از ثبت درخواست تا تحویل نهایی',
                'status' => ContentStatus::PUBLISHED,
                'is_system' => true,
                'position' => 5,
                'seo_title' => 'فرآیند تأمین و واردات در ۵ گام | نفیس تجارت',
                'seo_description' => 'فرآیند شفاف و پنج‌گامی نفیس تجارت: ثبت درخواست، استعلام و تطبیق کارخانه، کنترل کیفی، مدیریت حمل و ترخیص، و تحویل نهایی.',
                'seo_keywords' => 'فرآیند تأمین, مراحل ترخیص, کنترل کیفیت واردات',
                'noindex' => false,
                'published_at' => $now,
            ]
        );

        $processBlocks = [
            [
                'block_key' => 'process_hero',
                'type' => BlockType::HERO,
                'position' => 1,
                'data' => [
                    'badge' => 'Step by Step Transparency',
                    'title' => 'مسیر شفاف و مستند واردات کالا',
                    'subtitle' => 'هر گام با گزارش، پیش‌فاکتور رسمی و پیگیری برخط در پورتال مشتریان همراه است.',
                ],
            ],
            [
                'block_key' => 'process_faq',
                'type' => BlockType::FAQ,
                'position' => 2,
                'data' => [
                    'title' => 'پرسش‌های متداول فرآیند واردات و ترخیص',
                    'items' => [
                        [
                            'question' => 'مدت زمان حمل هوایی از چین تا ایران چقدر است؟',
                            'answer' => 'به طور معمول پرواز مستقیم یا ترانزیت دبی بین ۷ تا ۱۰ روز کاری زمان می‌برد.',
                        ],
                        [
                            'question' => 'آیا امکان بازرسی کالا در کارخانه تولیدکننده وجود دارد؟',
                            'answer' => 'بله، کارشناسان کنترل کیفیت نفیس در مبدأ (چین یا دبی) بازرسی را انجام داده و گزارش تصویری ارسال می‌کنند.',
                        ],
                        [
                            'question' => 'تشریفات ترخیص گمرکی چگونه پیگیری می‌شود؟',
                            'answer' => 'تمام اسناد، ثبت سفارش و شماره کوتاژ گمرکی در پنل اختصاصی مشتری ثبت و مرحله‌به‌مرحله به‌روزرسانی می‌گردد.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($processBlocks as $b) {
            ContentBlock::updateOrCreate(
                ['page_id' => $process->id, 'block_key' => $b['block_key']],
                [
                    'type' => $b['type'],
                    'position' => $b['position'],
                    'draft_data' => $b['data'],
                    'published_data' => $b['data'],
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => $now,
                ]
            );
        }

        /* -------------------------------------------------------------------------- */
        /* 6. B2B Page & Blocks                                                       */
        /* -------------------------------------------------------------------------- */
        $b2b = SitePage::updateOrCreate(
            ['slug' => 'b2b'],
            [
                'route_path' => '/b2b',
                'title_fa' => 'همکاری تجاری (B2B)',
                'title_en' => 'B2B Partnership',
                'summary_fa' => 'پنل مشتریان عمده و همکاری تجاری برای فروشگاه‌ها و بازرگانان',
                'status' => ContentStatus::PUBLISHED,
                'is_system' => true,
                'position' => 6,
                'seo_title' => 'پنل مشتریان عمده و همکاری تجاری (B2B) | نفیس تجارت',
                'seo_description' => 'ثبت‌نام عمده‌فروشان و فروشگاه‌ها در پنل B2B نفیس تجارت؛ دسترسی به قیمت‌های عمده پس از احراز هویت.',
                'seo_keywords' => 'همکاری تجاری, قیمت عمده, پنل B2B, خرید عمده وارداتی',
                'noindex' => false,
                'published_at' => $now,
            ]
        );

        $b2bBlocks = [
            [
                'block_key' => 'b2b_hero',
                'type' => BlockType::HERO,
                'position' => 1,
                'data' => [
                    'badge' => 'B2B Wholesale Portal',
                    'title' => 'پنل همکاران تجاری و عمده‌فروشان',
                    'subtitle' => 'پس از احراز هویت کسب‌وکار خود، به نرخ‌های دسته اول کارخانه‌ها، تخفیف‌های حجمی و شرایط اعتباری دسترسی خواهید داشت.',
                ],
            ],
            [
                'block_key' => 'b2b_benefits',
                'type' => BlockType::FEATURE_GRID,
                'position' => 2,
                'data' => [
                    'title' => 'مزایای عضویت در شبکه B2B نفیس',
                    'items' => [
                        ['title' => 'نرخ‌های دست‌اول و رقابتی', 'text' => 'حذف تمامی واسطه‌ها و دسترسی به کمترین قیمت بازار ایران'],
                        ['title' => 'تخصیص سهمیه اولویت‌دار', 'text' => 'رزرو و خرید پیش از توزیع عمومی در بازار'],
                        ['title' => 'مشاوره اختصاصی بازرگانی', 'text' => 'کارشناس معین برای پشتیبانی صفر تا صد سفارش‌های شما'],
                    ],
                ],
            ],
            [
                'block_key' => 'b2b_info',
                'type' => BlockType::RICH_TEXT,
                'position' => 3,
                'data' => [
                    'title' => 'شرایط تأیید حساب سازمانی',
                    'content' => 'ارائه جواز کسب معتبر، کارت بازرگانی یا شناسه ملی شرکت جهت فعال‌سازی نرخ‌های عمده‌فروشی الزامی است. بررسی مدارک توسط کارشناسان حداکثر ظرف ۴ ساعت کاری انجام می‌شود.',
                ],
            ],
        ];

        foreach ($b2bBlocks as $b) {
            ContentBlock::updateOrCreate(
                ['page_id' => $b2b->id, 'block_key' => $b['block_key']],
                [
                    'type' => $b['type'],
                    'position' => $b['position'],
                    'draft_data' => $b['data'],
                    'published_data' => $b['data'],
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => $now,
                ]
            );
        }

        /* -------------------------------------------------------------------------- */
        /* 7. Contact Page & Blocks                                                   */
        /* -------------------------------------------------------------------------- */
        $contact = SitePage::updateOrCreate(
            ['slug' => 'contact'],
            [
                'route_path' => '/contact',
                'title_fa' => 'تماس با ما',
                'title_en' => 'Contact Us',
                'summary_fa' => 'پل‌های ارتباطی، نشانی دفتر مرکزی و مشاوره اختصاصی واردات',
                'status' => ContentStatus::PUBLISHED,
                'is_system' => true,
                'position' => 7,
                'seo_title' => 'تماس با نفیس تجارت | مشاوره واردات و خرید عمده',
                'seo_description' => 'راه‌های ارتباطی با بازرگانی نفیس تجارت برای مشاوره واردات، ترخیص کالا و خرید عمده در تهران.',
                'seo_keywords' => 'تماس با نفیس تجارت, آدرس دفتر بازرگانی, تلفن نفیس تجارت',
                'noindex' => false,
                'published_at' => $now,
            ]
        );

        $contactBlocks = [
            [
                'block_key' => 'contact_details',
                'type' => BlockType::CONTACT,
                'position' => 1,
                'data' => [
                    'title' => 'اطلاعات تماس دفتر مرکزی',
                    'address' => 'تهران، میدان دوم صادقیه، برج گلدیس، پلاک ۱۵۱۸، طبقه ۵، واحد ۵۰۸',
                    'phoneDisplay' => '۰۹۹۹۱۲۲۲۲۶۱',
                    'phoneIntl' => '+989991222261',
                    'email' => 'info@nafiskala.co',
                    'whatsapp' => 'https://wa.me/989991222261',
                    'telegram' => 'https://t.me/Nafiskalaonline',
                    'telegramHandle' => '@Nafiskalaonline',
                    'instagram' => 'https://instagram.com/nafiskala.co',
                    'instagramHandle' => 'NAFISKALA.CO',
                ],
            ],
            [
                'block_key' => 'contact_hours',
                'type' => BlockType::RICH_TEXT,
                'position' => 2,
                'data' => [
                    'title' => 'ساعات کاری و جلسات بازرگانی',
                    'content' => 'شنبه تا چهارشنبه: ۹:۰۰ الی ۱۷:۳۰ | پنج‌شنبه‌ها: ۹:۰۰ الی ۱۳:۳۰. لطفا برای جلسات مشاوره حضوری از قبل وقت تعیین فرمایید.',
                ],
            ],
        ];

        foreach ($contactBlocks as $b) {
            ContentBlock::updateOrCreate(
                ['page_id' => $contact->id, 'block_key' => $b['block_key']],
                [
                    'type' => $b['type'],
                    'position' => $b['position'],
                    'draft_data' => $b['data'],
                    'published_data' => $b['data'],
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => $now,
                ]
            );
        }
    }
}