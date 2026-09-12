@php
    $slides = $data['slides'] ?? [];
@endphp

@if(count($slides) > 0)
<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($slides as $slide)
            @if(!empty($slide['active']))
                <div class="glass-card rounded-2xl overflow-hidden border border-white/10 group hover:border-gold/30 transition-all">
                    <div class="h-44 bg-surface relative overflow-hidden flex items-center justify-center">
                        @if(!empty($slide['imageUrl']))
                            <div class="w-full h-full bg-cover bg-center group-hover:scale-105 transition-transform duration-500" style="background-image: url('{{ $slide['imageUrl'] }}')"></div>
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-white/5 flex items-center justify-center text-2xl">📦</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-obsidian via-transparent to-transparent"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-base font-extrabold text-white group-hover:text-gold transition-colors">
                            {{ $slide['title'] }}
                        </h3>
                        @if(!empty($slide['subtitle']))
                            <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                {{ $slide['subtitle'] }}
                            </p>
                        @endif
                        @if(!empty($slide['link']))
                            <a href="{{ $slide['link'] }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gold mt-4 hover:underline">
                                <span>مشاهده اطلاعات بیشتر</span>
                                <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endif