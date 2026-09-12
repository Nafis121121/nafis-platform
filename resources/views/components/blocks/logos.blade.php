@php
    $title = $data['title'] ?? 'شرکای تجاری و کارخانه‌ها';
    $logos = $data['logos'] ?? [];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 text-center">
    @if($title)
        <span class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-8 block">{{ $title }}</span>
    @endif
    <div class="flex flex-wrap items-center justify-center gap-8 opacity-70">
        @forelse($logos as $logo)
            @php($content = !empty($logo['imageUrl']) ? '<img src="'.e($logo['imageUrl']).'" alt="'.e($logo['name'] ?? '').'" class="h-9 w-auto object-contain">' : e($logo['name'] ?? 'Partner'))
            @if(!empty($logo['link']))
                <a href="{{ $logo['link'] }}" class="glass-card px-6 py-3 rounded-xl border border-white/5 text-xs font-bold text-slate-300 hover:border-gold/30 transition-all" target="_blank" rel="noopener">{!! $content !!}</a>
            @else
                <div class="glass-card px-6 py-3 rounded-xl border border-white/5 text-xs font-bold text-slate-300">{!! $content !!}</div>
            @endif
        @empty
            <div class="glass-card px-6 py-3 rounded-xl text-xs font-bold text-slate-400">اتاق بازرگانی ایران و چین</div>
            <div class="glass-card px-6 py-3 rounded-xl text-xs font-bold text-slate-400">گمرک جمهوری اسلامی ایران</div>
        @endforelse
    </div>
</div>
