@php
    $items = $data['stats'] ?? $data['items'] ?? [];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $s)
            <div class="glass-card rounded-2xl p-6 border border-white/10 text-center hover:border-gold/30 transition-colors">
                <div class="text-xl sm:text-2xl font-black text-gold-gradient">
                    {{ $s['title'] ?? $s['label'] ?? '' }}
                </div>
                <div class="text-xs sm:text-sm text-slate-400 mt-2 font-medium">
                    {{ $s['subtitle'] ?? $s['value'] ?? $s['description'] ?? '' }}
                </div>
            </div>
        @endforeach
    </div>
</div>