@php
    $title = $data['title'] ?? 'آماده شروع همکاری هستید؟';
    $description = $data['description'] ?? '';
    $primaryText = $data['primaryBtnText'] ?? $data['buttonText'] ?? 'شروع همکاری';
    $primaryLink = $data['primaryBtnLink'] ?? $data['buttonLink'] ?? '/requests/new';
    $secondaryText = $data['secondaryBtnText'] ?? $data['secondaryText'] ?? null;
    $secondaryLink = $data['secondaryBtnLink'] ?? $data['secondaryLink'] ?? null;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="glass-card rounded-3xl p-8 sm:p-12 border border-white/15 relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-primary/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-2xl">
            <h2 class="text-2xl sm:text-3xl font-black text-white leading-snug">
                {{ $title }}
            </h2>
            @if($description)
                <p class="text-sm sm:text-base text-slate-300 mt-3 leading-relaxed">
                    {{ $description }}
                </p>
            @endif
        </div>

        <div class="relative z-10 flex flex-wrap gap-4 w-full md:w-auto">
            @if($primaryText)
                <a href="{{ $primaryLink }}" class="gradient-crimson text-white px-7 py-3.5 rounded-xl text-sm font-bold shadow-xl shadow-primary/40 hover:scale-105 transition-transform flex items-center gap-2">
                    <span>{{ $primaryText }}</span>
                    <svg class="w-4 h-4 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            @endif
            @if($secondaryText)
                <a href="{{ $secondaryLink }}" class="glass-card px-6 py-3.5 rounded-xl text-sm font-bold text-slate-300 hover:text-white hover:bg-white/10 transition-colors">
                    {{ $secondaryText }}
                </a>
            @endif
        </div>
    </div>
</div>