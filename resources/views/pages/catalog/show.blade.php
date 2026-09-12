@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
    <a href="{{ route('catalog.index') }}" class="text-sm text-slate-400 hover:text-gold flex items-center gap-1.5 transition">
        <span>←</span>
        <span>بازگشت به کاتالوگ محصولات</span>
    </a>

    <div class="mt-6 grid lg:grid-cols-2 gap-8 items-start">
        <!-- Product Gallery & Images -->
        <div class="space-y-4">
            <div class="glass-card rounded-3xl border border-white/10 min-h-80 p-4 flex flex-col items-center justify-center relative group">
                @php
                    $primaryImg = $product->images->first();
                @endphp
                @if($primaryImg)
                    <img id="main-product-image" src="{{ $primaryImg->resolved_url }}" alt="{{ $primaryImg->alt_text ?: $product->name_fa }}" class="w-full max-h-[28rem] object-contain rounded-2xl transition-all duration-300">
                    <div class="absolute bottom-4 left-4 right-4 flex justify-between items-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <a id="main-image-download-btn" href="{{ $primaryImg->resolved_url }}" target="_blank" download class="glass-card bg-obsidian/90 hover:bg-gold hover:text-obsidian text-white text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-2 border border-white/20 shadow-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>دانلود تصویر اصلی</span>
                        </a>
                    </div>
                @else
                    <span class="text-7xl opacity-60">📦</span>
                @endif
            </div>

            @if($product->images->count() > 1)
                <div class="glass-card border border-white/10 rounded-2xl p-3">
                    <div class="text-xs text-slate-400 mb-2 font-medium">گالری تصاویر (جهت تغییر تصویر یا دانلود کلیک کنید):</div>
                    <div class="flex gap-3 overflow-x-auto pb-1">
                        @foreach($product->images as $index => $image)
                            <div class="relative group/thumb flex-shrink-0">
                                <img src="{{ $image->resolved_url }}" alt="{{ $image->alt_text ?: $product->name_fa }}" onclick="document.getElementById('main-product-image').src = '{{ $image->resolved_url }}'; document.getElementById('main-image-download-btn').href = '{{ $image->resolved_url }}';" class="w-20 h-20 object-cover rounded-xl border border-white/15 cursor-pointer hover:border-gold transition hover:scale-105">
                                <a href="{{ $image->resolved_url }}" target="_blank" download title="دانلود تصویر" class="absolute -top-1.5 -left-1.5 bg-obsidian text-gold border border-gold/40 rounded-full p-1 opacity-0 group-hover/thumb:opacity-100 transition shadow">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Product Information -->
        <div>
            <div class="flex items-center gap-2 text-xs text-gold font-bold">
                <span>{{ $product->category?->name_fa ?? 'کالای بازرگانی' }}</span>
                @if($product->brand)
                    <span class="text-slate-600">•</span>
                    <span class="text-slate-400">برند {{ $product->brand->name_fa }}</span>
                @endif
            </div>

            <h1 class="mt-3 text-2xl sm:text-3xl font-black text-white leading-snug">{{ $product->name_fa }}</h1>
            @if($product->name_en)
                <p class="mt-2 text-sm text-slate-400 font-mono" dir="ltr">{{ $product->name_en }}</p>
            @endif

            @if($product->description_fa)
                <div class="mt-6 glass-card border border-white/10 rounded-2xl p-5">
                    <h3 class="text-xs text-gold font-bold mb-2">توضیحات و مشخصات کالا</h3>
                    <p class="text-slate-300 text-sm leading-8 text-justify">{{ $product->description_fa }}</p>
                </div>
            @endif

            <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="glass-card border border-white/10 rounded-xl p-3.5">
                    <span class="block text-xs text-slate-400">حداقل سفارش (MOQ)</span>
                    <b class="block mt-1 text-white text-sm">{{ number_format($product->moq) }} عدد</b>
                </div>
                <div class="glass-card border border-white/10 rounded-xl p-3.5">
                    <span class="block text-xs text-slate-400">قیمت عمده پایه</span>
                    <b class="block mt-1 text-gold text-sm">{{ $product->base_price !== null ? number_format((float) $product->base_price) . ' ' . $product->base_currency : 'قابل استعلام' }}</b>
                </div>
                <div class="glass-card border border-white/10 rounded-xl p-3.5">
                    <span class="block text-xs text-slate-400">SKU / شناسه کالا</span>
                    <b class="block mt-1 text-slate-300 text-xs font-mono">{{ $product->base_sku ?: '—' }}</b>
                </div>
            </div>

            @php
                $specs = $product->metadata['specifications'] ?? [];
            @endphp
            @if(!empty($specs) && is_array($specs))
                <div class="mt-6 glass-card border border-white/10 rounded-2xl p-5">
                    <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gold"></span>
                        <span>جدول مشخصات فنی و استانداردهای تولید</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        @foreach($specs as $key => $val)
                            <div class="flex justify-between items-center py-2 px-3 rounded-lg bg-white/5 border border-white/5">
                                <span class="text-slate-400">{{ is_string($key) ? $key : '' }}</span>
                                <span class="text-white font-medium text-left dir-ltr">{{ is_array($val) ? json_encode($val) : $val }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($product->variants->count())
                <div class="mt-6 glass-card border border-white/10 rounded-2xl p-5">
                    <h3 class="font-bold text-white text-sm mb-3">تنوع‌های موجود کالا (مدل، رنگ و متغیرها)</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->variants as $variant)
                            <span class="px-3.5 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-slate-300 hover:border-gold/40 transition">
                                {{ $variant->name_fa ?: $variant->name_en ?: $variant->sku }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('sourcing.create', ['product' => $product->slug]) }}" class="flex-1 text-center gradient-crimson text-white rounded-xl px-7 py-3.5 text-sm font-bold shadow-lg shadow-primary/20 hover:scale-[1.02] transition">
                    ثبت استعلام قیمت و سفارش عمده این کالا
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

