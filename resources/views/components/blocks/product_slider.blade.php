<section class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-end justify-between gap-4 mb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-gold animate-pulse"></span>
                <span class="text-gold text-[11px] font-bold tracking-widest uppercase">نمونه کالاهای وارداتی و عمده</span>
            </div>
            <h2 class="mt-1.5 text-xl sm:text-2xl font-black text-white">نمونه محصولات وارداتی</h2>
        </div>
        <a href="{{ route('catalog.index') }}" class="text-xs font-bold text-slate-300 hover:text-gold flex items-center gap-1 transition">
            <span>مشاهده کاتالوگ کامل</span>
            <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>

    <div class="flex gap-4 overflow-x-auto pb-4 snap-x scrollbar-thin scrollbar-thumb-white/10">
        @foreach($products as $product)
            @php
                $img = $product->images->first();
                $imgUrl = $img?->resolved_url;
            @endphp
            <a href="{{ route('catalog.show', $product) }}" class="w-48 sm:w-56 flex-shrink-0 snap-start glass-card rounded-2xl border border-white/10 p-3 hover:border-gold/50 hover:shadow-xl hover:shadow-black/40 hover:-translate-y-1 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <!-- 1:1 Square Product Image (800x800 aspect ratio) -->
                    <div class="w-full aspect-square rounded-xl bg-gradient-to-b from-white/[0.08] to-white/[0.02] border border-white/10 p-2.5 flex items-center justify-center overflow-hidden group-hover:bg-white/[0.1] transition">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $img->alt_text ?: $product->name_fa }}" class="w-full h-full object-contain rounded-lg group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @else
                            <span class="text-3xl opacity-50">📦</span>
                        @endif
                    </div>

                    <!-- Category & Title -->
                    <div class="mt-2.5 flex items-center justify-between text-[10px] text-gold font-bold">
                        <span>{{ $product->category?->name_fa ?? 'کالا' }}</span>
                        @if($product->brand)
                            <span class="text-slate-400 font-normal truncate max-w-[90px]">{{ $product->brand->name_fa }}</span>
                        @endif
                    </div>

                    <h3 class="mt-1 text-xs sm:text-sm font-bold text-white group-hover:text-gold transition-colors line-clamp-1 leading-snug">
                        {{ $product->name_fa }}
                    </h3>

                    @if($product->description_fa)
                        <p class="mt-1 text-[11px] text-slate-400 line-clamp-2 leading-relaxed text-justify">
                            {{ $product->description_fa }}
                        </p>
                    @endif
                </div>

                <!-- Footer / Price / MOQ -->
                <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-[11px]">
                    <span class="text-slate-400 text-[10px]">حداقل: <b class="text-slate-200">{{ number_format($product->moq) }}</b></span>
                    <span class="text-gold font-bold text-[11px]">
                        {{ $product->base_price !== null ? number_format((float) $product->base_price) . ' ' . $product->base_currency : 'استعلام' }}
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</section>

