@php
    $title = $data['title'] ?? null;
    $eyebrow = $data['eyebrow'] ?? null;
    $description = $data['description'] ?? null;
    $items = $data['items'] ?? $data['services'] ?? $data['steps'] ?? $data['cards'] ?? $data['industries'] ?? [];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6">
    @if($title || $eyebrow || $description)
        <div class="max-w-3xl mb-12">
            @if($eyebrow)
                <span class="text-xs font-extrabold uppercase tracking-widest text-gold">{{ $eyebrow }}</span>
            @endif
            @if($title)
                <h2 class="text-2xl sm:text-3xl font-black text-white mt-2">{{ $title }}</h2>
            @endif
            @if($description)
                <p class="text-sm sm:text-base text-slate-400 mt-3 leading-relaxed">{{ $description }}</p>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($items as $idx => $item)
            <div class="glass-card rounded-2xl p-6 border border-white/10 hover:border-primary/40 transition-all hover:-translate-y-1">
                @if(isset($item['step']))
                    <div class="w-10 h-10 rounded-xl gradient-crimson text-white font-black flex items-center justify-center text-sm mb-4">
                        {{ $item['step'] }}
                    </div>
                @else
                    <div class="w-10 h-10 rounded-xl bg-white/5 text-gold flex items-center justify-center text-lg mb-4">
                        ✦
                    </div>
                @endif

                <h3 class="text-base font-bold text-white">
                    {{ $item['title'] ?? '' }}
                </h3>

                @if(!empty($item['en']))
                    <span class="text-[11px] text-slate-500 font-mono block mt-0.5">{{ $item['en'] }}</span>
                @endif

                <p class="text-xs sm:text-sm text-slate-400 mt-3 leading-relaxed">
                    {{ $item['text'] ?? $item['description'] ?? '' }}
                </p>

                @if(!empty($item['points']))
                    <ul class="mt-4 pt-4 border-t border-white/5 space-y-1.5 text-xs text-slate-300">
                        @foreach($item['points'] as $p)
                            <li class="flex items-center gap-2">
                                <span class="text-gold font-bold">✓</span>
                                <span>{{ $p }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
</div>