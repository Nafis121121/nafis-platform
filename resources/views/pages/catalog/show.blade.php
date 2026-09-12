@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
    <a href="{{ route('catalog.index') }}" class="text-sm text-slate-400 hover:text-gold">← بازگشت به کاتالوگ</a>
    <div class="mt-6 grid lg:grid-cols-2 gap-8">
        <div class="glass-card rounded-3xl border border-white/10 min-h-80 flex items-center justify-center"><span class="text-7xl opacity-60">📦</span></div>
        <div>
            <div class="text-xs text-gold font-bold">{{ $product->category?->name_fa ?? 'کالا' }}</div>
            <h1 class="mt-3 text-3xl sm:text-4xl font-black text-white">{{ $product->name_fa }}</h1>
            @if($product->name_en)<p class="mt-2 text-slate-500" dir="ltr">{{ $product->name_en }}</p>@endif
            @if($product->description_fa)<p class="mt-6 text-slate-300 leading-8">{{ $product->description_fa }}</p>@endif
            <div class="mt-7 grid grid-cols-2 gap-3">
                <div class="glass-card border border-white/10 rounded-xl p-4"><span class="block text-xs text-slate-500">حداقل سفارش</span><b class="block mt-1 text-white">{{ number_format($product->moq) }} عدد</b></div>
                <div class="glass-card border border-white/10 rounded-xl p-4"><span class="block text-xs text-slate-500">برند</span><b class="block mt-1 text-white">{{ $product->brand?->name_fa ?? '—' }}</b></div>
            </div>
            @if($product->variants->count())
                <div class="mt-6"><h2 class="font-bold text-white mb-3">تنوع‌های محصول</h2><div class="flex flex-wrap gap-2">@foreach($product->variants as $variant)<span class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-xs text-slate-300">{{ $variant->name ?? $variant->sku ?? 'تنوع محصول' }}</span>@endforeach</div></div>
            @endif
            <a href="{{ route('sourcing.create', ['product' => $product->slug]) }}" class="inline-flex mt-8 gradient-crimson text-white rounded-xl px-7 py-3.5 text-sm font-bold">درخواست قیمت و تأمین این کالا</a>
        </div>
    </div>
</div>
@endsection
