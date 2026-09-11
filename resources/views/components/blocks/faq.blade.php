@php
    $title = $data['title'] ?? 'پرسش‌های متداول';
    $items = $data['items'] ?? [];
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6">
    @if($title)
        <h2 class="text-2xl sm:text-3xl font-black text-white text-center mb-10">{{ $title }}</h2>
    @endif

    <div class="space-y-4">
        @foreach($items as $idx => $item)
            <details class="glass-card rounded-2xl border border-white/10 p-5 group transition-all" {{ $idx === 0 ? 'open' : '' }}>
                <summary class="flex items-center justify-between cursor-pointer font-bold text-sm sm:text-base text-white hover:text-gold list-none">
                    <span>{{ $item['question'] ?? '' }}</span>
                    <span class="text-slate-400 group-open:rotate-180 transition-transform">▼</span>
                </summary>
                <div class="mt-4 pt-4 border-t border-white/5 text-xs sm:text-sm text-slate-300 leading-relaxed text-justify">
                    {{ $item['answer'] ?? '' }}
                </div>
            </details>
        @endforeach
    </div>
</div>