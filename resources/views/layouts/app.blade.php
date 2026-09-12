<!DOCTYPE html>
<html lang="fa" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @php
        $settings = $chrome['settings'] ?? [];
        $brand = $settings['brand'] ?? [];
        $theme = $settings['theme'] ?? [];
        $contact = $settings['contact'] ?? [];
        $seo = $settings['seo'] ?? [];

        $pageTitle = $page->seo_title ?: ($page->title_fa . ' | ' . ($brand['name'] ?? 'نفیس تجارت'));
        $pageDesc = $page->seo_description ?: ($seo['description'] ?? '');
        $pageKeywords = $page->seo_keywords ?: ($seo['keywords'] ?? '');
        $pageOgImage = $page->ogImage?->resolved_url ?: ($seo['ogImage'] ?? '');
        $canonicalUrl = $page->canonical_url ?: url()->current();
        $ogTitle = $page->og_title ?: $pageTitle;
        $ogDescription = $page->og_description ?: $pageDesc;
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $brand['name'] ?? 'نفیس تجارت',
            'url' => url('/'),
            'email' => $contact['email'] ?? null,
            'telephone' => $contact['phoneIntl'] ?? null,
        ];
    @endphp

    <title>{{ $pageTitle }}</title>
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta name="description" content="{{ $pageDesc }}">
    @if(!empty($pageKeywords))
        <meta name="keywords" content="{{ $pageKeywords }}">
    @endif
    @if($page->noindex)
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow">
    @endif

    <!-- Open Graph / Twitter -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if(!empty($pageOgImage))
        <meta property="og:image" content="{{ url($pageOgImage) }}">
        <meta name="twitter:image" content="{{ url($pageOgImage) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">

    <script type="application/ld+json">{!! json_encode(array_filter($organizationSchema), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    <!-- Font: Vazirmatn -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php
        $hexToRgb = static function (?string $hex, string $fallback): string {
            $hex = ltrim($hex ?: $fallback, '#');
            if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
                $hex = ltrim($fallback, '#');
            }
            return hexdec(substr($hex, 0, 2)) . ' ' . hexdec(substr($hex, 2, 2)) . ' ' . hexdec(substr($hex, 4, 2));
        };
    @endphp
    <style>
        :root {
            --color-primary: {{ $hexToRgb($theme['primary'] ?? null, '#9e1b32') }};
            --color-primary-deep: {{ $hexToRgb($theme['primaryDeep'] ?? null, '#6e1120') }};
            --color-gold: {{ $hexToRgb($theme['gold'] ?? null, '#c9a84c') }};
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-primary selection:text-white">

    <!-- Topbar -->
    @if(!empty($brand['topbarActive']))
        <aside class="bg-obsidian border-b border-white/5 text-xs text-slate-400 py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                    <span>{{ $brand['topbar'] }}</span>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    <span class="flex items-center gap-1.5 hover:text-white transition-colors">
                        ✈️ حمل هوایی مستقیم چین و دبی
                    </span>
                    <span class="flex items-center gap-1.5 hover:text-white transition-colors">
                        🛡️ ترخیص تخصصی و تضمین اصالت
                    </span>
                    <a href="tel:{{ $contact['phoneIntl'] ?? '' }}" class="text-gold font-bold dir-ltr hover:underline">
                        {{ $contact['phoneDisplay'] ?? '' }}
                    </a>
                </div>
            </div>
        </aside>
    @endif

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-50 header-3d">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group flex-shrink-0">
                <div class="w-10 h-10 rounded-xl gradient-crimson flex items-center justify-center font-black text-lg text-white shadow-lg shadow-primary/30 group-hover:scale-105 transition-transform">
                    ن
                </div>
                <div>
                    <span class="block font-black text-base sm:text-lg tracking-tight text-white group-hover:text-gold transition-colors leading-tight">
                        {{ $brand['name'] ?? 'نفیس تجارت' }}
                    </span>
                    <span class="block text-[10px] text-slate-400 font-medium whitespace-nowrap">
                        {{ $brand['tagline'] ?? 'واردات · ترخیص · پخش عمده' }}
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Items -->
            <nav class="hidden lg:flex items-center gap-1 sm:gap-1.5 flex-nowrap overflow-visible">
                @foreach($chrome['menus']['header'] ?? [] as $item)
                    @php
                        $isActive = request()->is(ltrim($item->computed_url, '/')) || (request()->is('/') && $item->computed_url === '/');
                    @endphp
                    @if($item->children && $item->children->count() > 0)
                        <div class="relative group">
                            <a href="{{ $item->computed_url }}" class="nav-3d-pill {{ $isActive ? 'active' : '' }}">
                                <span>{{ $item->label_fa }}</span>
                                <svg class="w-3 h-3 transition-transform group-hover:rotate-180 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </a>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-1.5 w-52 glass-card rounded-xl shadow-2xl p-1.5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-white/15">
                                @foreach($item->children as $child)
                                    <a href="{{ $child->computed_url }}" class="block px-3 py-1.5 text-xs font-medium text-slate-300 hover:text-gold hover:bg-white/5 rounded-lg transition-colors whitespace-nowrap">
                                        {{ $child->label_fa }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $item->computed_url }}" class="nav-3d-pill {{ $isActive ? 'active' : '' }}">
                            {{ $item->label_fa }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <!-- Actions -->
            <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
                @auth
                    @if(auth()->user()->isRole('customer'))
                        <a href="{{ url('/portal') }}" class="btn-3d-secondary px-3 py-1.5 text-xs font-bold text-gold hover:text-white rounded-lg">
                            پورتال مشتری
                        </a>
                    @else
                        <a href="{{ url('/admin') }}" class="btn-3d-secondary px-3 py-1.5 text-xs font-bold text-slate-300 hover:text-white rounded-lg">
                            پنل مدیریت
                        </a>
                    @endif
                @else
                    <a href="{{ url('/portal/login') }}" class="btn-3d-secondary px-3 py-1.5 text-xs font-bold text-slate-300 hover:text-white rounded-lg">
                        ورود مشتریان
                    </a>
                @endauth
                <a href="/requests/new" class="btn-3d-primary text-white px-3.5 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 whitespace-nowrap">
                    <span>ثبت درخواست تأمین</span>
                    <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Mobile Menu Toggle Button -->
            <button id="mobileMenuBtn" aria-label="منو" class="lg:hidden p-2 rounded-xl bg-white/5 text-slate-300 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobileDrawer" class="hidden lg:hidden border-t border-white/10 bg-obsidian/95 p-4 space-y-2">
            @foreach($chrome['menus']['mobile'] ?? [] as $mItem)
                <a href="{{ $mItem->computed_url }}" class="block px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-300 hover:text-gold hover:bg-white/5">
                    {{ $mItem->label_fa }}
                </a>
            @endforeach
            <div class="pt-3 border-t border-white/10 flex flex-col gap-2">
                @auth
                    <a href="{{ auth()->user()->isRole('customer') ? url('/portal') : url('/admin') }}" class="w-full text-center bg-white/5 text-slate-300 py-3 rounded-xl text-xs font-bold">
                        {{ auth()->user()->isRole('customer') ? 'پورتال مشتری' : 'پنل مدیریت' }}
                    </a>
                @else
                    <a href="{{ url('/portal/login') }}" class="w-full text-center bg-white/5 text-slate-300 py-3 rounded-xl text-xs font-bold">
                        ورود مشتریان
                    </a>
                @endauth
                <a href="/requests/new" class="w-full text-center gradient-crimson text-white py-3 rounded-xl text-xs font-bold">
                    ثبت درخواست تأمین کالا
                </a>
                <a href="/contact" class="w-full text-center bg-white/5 text-slate-300 py-3 rounded-xl text-xs font-bold">
                    تماس با کارشناس بازرگانی
                </a>
            </div>
        </div>
    </header>

    <!-- Page Body -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Global Footer -->
    <footer class="mt-24 border-t border-white/10 bg-obsidian relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Top CTA Banner in Footer -->
            <div class="relative -top-12 glass-card rounded-3xl p-6 sm:p-10 border border-white/15 shadow-2xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl sm:text-2xl font-black text-white">
                        زنجیره تأمین کالای خود را به <span class="text-gold-gradient">نفیس تجارت</span> بسپارید
                    </h3>
                    <p class="text-slate-400 text-sm mt-2 max-w-2xl">
                        از شناسایی مستقیم کارخانه، بازرسی فنی و کنترل کیفیت تا حمل هوایی/دریایی و ترخیص نهایی در انبار شما.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                    <a href="/requests/new" class="gradient-crimson text-white px-6 py-3.5 rounded-xl text-sm font-bold shadow-lg shadow-primary/40 hover:scale-105 transition-transform flex items-center justify-center gap-2">
                        <span>ثبت درخواست تأمین</span>
                        <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="/contact" class="glass-card px-6 py-3.5 rounded-xl text-sm font-bold text-slate-300 hover:text-white hover:bg-white/10 transition-colors flex items-center justify-center">
                        مشاوره با کارشناس
                    </a>
                </div>
            </div>

            <!-- 4 Column Footer Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 pt-4">
                <!-- Col 1: About & Info -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl gradient-crimson flex items-center justify-center font-black text-lg text-white">ن</div>
                        <span class="font-extrabold text-lg text-white">{{ $brand['name'] ?? 'نفیس تجارت' }}</span>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400 text-justify">
                        شرکت بازرگانی نفیس تجارت؛ شریک عملیاتی کسب‌وکارها در سورسینگ، کنترل کیفیت، حمل بین‌المللی و ترخیص تخصصی کالا از چین، امارات و بازارهای جهانی.
                    </p>
                    <div class="text-xs text-slate-500 pt-2 flex items-center gap-2">
                        <span class="text-gold">✓</span> دارای کارت بازرگانی معتبر و نماد تجارت الکترونیکی
                    </div>
                </div>

                <!-- Col 2: Services Menu -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white border-r-2 border-primary pr-2.5">خدمات بازرگانی</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        @foreach($chrome['menus']['footer_services'] ?? [] as $fService)
                            <li>
                                <a href="{{ $fService->computed_url }}" class="hover:text-gold transition-colors block py-0.5">
                                    {{ $fService->label_fa }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Col 3: Industries Menu -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white border-r-2 border-gold pr-2.5">صنایع تخصصی</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        @foreach($chrome['menus']['footer_industries'] ?? [] as $fInd)
                            <li>
                                <a href="{{ $fInd->computed_url }}" class="hover:text-gold transition-colors block py-0.5">
                                    {{ $fInd->label_fa }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Col 4: Contact & Social -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white border-r-2 border-primary pr-2.5">ارتباط با ما</h4>
                    <div class="text-xs text-slate-400 space-y-2">
                        <p class="flex items-start gap-2">
                            <span class="text-primary font-bold">📍</span>
                            <span>{{ $contact['address'] ?? '' }}</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="text-primary font-bold">📞</span>
                            <a href="tel:{{ $contact['phoneIntl'] ?? '' }}" class="hover:text-white dir-ltr font-bold text-slate-300">
                                {{ $contact['phoneDisplay'] ?? '' }}
                            </a>
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="text-primary font-bold">✉️</span>
                            <span>{{ $contact['email'] ?? '' }}</span>
                        </p>
                    </div>

                    <!-- Social Icons -->
                    <div class="flex items-center gap-2 pt-2">
                        @if(!empty($contact['whatsapp']))
                            <a href="{{ $contact['whatsapp'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl glass-card flex items-center justify-center text-emerald-400 hover:scale-110 transition-transform" title="واتس‌اپ">
                                🟢
                            </a>
                        @endif
                        @if(!empty($contact['telegram']))
                            <a href="{{ $contact['telegram'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl glass-card flex items-center justify-center text-sky-400 hover:scale-110 transition-transform" title="تلگرام">
                                ✈️
                            </a>
                        @endif
                        @if(!empty($contact['instagram']))
                            <a href="{{ $contact['instagram'] }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl glass-card flex items-center justify-center text-pink-400 hover:scale-110 transition-transform" title="اینستاگرام">
                                📸
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Legal Bar -->
            <div class="border-t border-white/5 py-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>© {{ date('Y') }} {{ $brand['name'] ?? 'بازرگانی نفیس تجارت' }} — کلیه حقوق مادی و معنوی محفوظ است.</p>
                <div class="flex flex-wrap gap-4">
                    @foreach($chrome['menus']['footer_legal'] ?? [] as $fLegal)
                        <a href="{{ $fLegal->computed_url }}" class="hover:text-slate-300 transition-colors">
                            {{ $fLegal->label_fa }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Drawer Toggle Script -->
    <script>
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            const drawer = document.getElementById('mobileDrawer');
            drawer.classList.toggle('hidden');
        });
    </script>
</body>
</html>