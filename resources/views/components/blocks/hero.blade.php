@php
    $badge = $data['badge'] ?? null;
    $title = $data['title'] ?? 'تأمین هوشمند کالا از بازارهای جهانی';
    $highlight = $data['highlight'] ?? 'نفیس تجارت';
    $subtitle = $data['subtitle'] ?? '';

    $ctaPrimary = $data['ctaPrimary'] ?? 'ثبت درخواست تأمین';
    $ctaPrimaryUrl = $data['ctaPrimaryUrl'] ?? '/requests/new';

    $ctaSecondary = $data['ctaSecondary'] ?? 'همکاری تجاری';
    $ctaSecondaryUrl = $data['ctaSecondaryUrl'] ?? '/portal';

    $imageUrl = $data['imageUrl'] ?? null;
    $overlay = $data['overlay'] ?? 45;
@endphp

<div class="relative overflow-hidden py-16 sm:py-24 border-b border-white/5">

    @if($imageUrl)
        <div
            class="absolute inset-0 bg-cover bg-center"
            style="background-image:url('{{ $imageUrl }}')"
        ></div>

        <div
            class="absolute inset-0 bg-obsidian"
            style="opacity: {{ max(0, min(100, (int) $overlay)) / 100 }}"
        ></div>
    @endif

    <!-- Background radial glow -->
    <div
        class="pointer-events-none absolute -top-40 right-1/4 w-96 h-96 bg-primary/20 rounded-full blur-3xl"
    ></div>

    <div
        class="pointer-events-none absolute -bottom-20 left-10 w-80 h-80 bg-gold/15 rounded-full blur-3xl"
    ></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="max-w-3xl">

            @if($badge)
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold text-gold bg-gold/10 border border-gold/20 mb-6"
                >
                    <span>✨</span>
                    <span>{{ $badge }}</span>
                </div>
            @endif

            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white leading-tight">
                {{ $title }}

                @if($highlight)
                    <span class="text-gold-gradient block mt-2 sm:inline sm:mt-0">
                        {{ $highlight }}
                    </span>
                @endif
            </h1>

            @if($subtitle)
                <p class="mt-6 text-base sm:text-lg text-slate-300 leading-relaxed text-justify">
                    {{ $subtitle }}
                </p>
            @endif

            <div class="mt-8 sm:mt-10 flex flex-wrap gap-4">

                @if($ctaPrimary)
                    <a
                        href="{{ $ctaPrimaryUrl }}"
                        class="gradient-crimson text-white px-7 py-4 rounded-xl text-sm font-bold shadow-xl shadow-primary/30 hover:scale-105 transition-transform flex items-center gap-2"
                    >
                        <span>{{ $ctaPrimary }}</span>

                        <svg
                            class="w-4 h-4 rotate-180"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"
                            ></path>
                        </svg>
                    </a>
                @endif

                @if($ctaSecondary)
                    <a
                        href="{{ $ctaSecondaryUrl }}"
                        class="glass-card px-7 py-4 rounded-xl text-sm font-bold text-slate-200 hover:text-white hover:bg-white/10 transition-colors"
                    >
                        {{ $ctaSecondary }}
                    </a>
                @endif

            </div>
        </div>
    </div>
</div>