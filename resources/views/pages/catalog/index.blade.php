@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
    <div class="mb-10">
        <span class="text-gold text-xs font-bold tracking-widest">WHOLESALE CATALOG</span>
        <h1 class="mt-3 text-3xl sm:text-4xl font-black text-white">کاتالوگ عمده کالا</h1>
        <p class="mt-3 text-slate-400 max-w-2xl">محصولات قابل تأمین را جست‌وجو و فیلتر کنید. برای قیمت نهایی، موجودی و شرایط واردات درخواست تأمین ثبت کنید.</p>
    </div>

    <form method="GET" action="{{ route('catalog.index') }}" class="glass-card rounded-2xl border border-white/10 p-4 mb-8 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input name="q" value="{{ request('q') }}" placeholder="جست‌وجوی نام یا SKU..." class="md:col-span-2 rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:border-gold/50 focus:ring-0">
        <select name="category" class="rounded-xl bg-obsidian border border-white/10 px-4 py-3 text-sm text-slate-300">
            <option value="">همه دسته‌بندی‌ها</option>
            @foreach($categories as $category)
                <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name_fa }}</option>
            @endforeach
        </select>
        <select name="brand" class="rounded-xl bg-obsidian border border-white/10 px-4 py-3 text-sm text-slate-300">
            <option value="">همه برندها</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->slug }}" @selected(request('brand') === $brand->slug)>{{ $brand->name_fa }}</option>
            @endforeach
        </select>
        <div class="md:col-span-4 flex gap-3">
            <button class="gradient-crimson text-white rounded-xl px-6 py-3 text-sm font-bold">اعمال فیلتر</button>
            @if(request()->hasAny(['q','category','brand']))
                <a href="{{ route('catalog.index') }}" class="rounded-xl px-5 py-3 text-sm text-slate-300 bg-white/5 hover:bg-white/10">پاک کردن</a>
            @endif
        </div>
    </form>

    @if($products->count())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            @foreach($products as $product)
                @php
                    $image = $product->images->first();
                @endphp
                <article class="glass-card rounded-2xl border border-white/10 p-3 hover:border-gold/50 hover:shadow-xl hover:shadow-black/40 hover:-translate-y-1 transition-all duration-200 group flex flex-col">
                    <a href="{{ route('catalog.show', $product) }}" class="block">
                        <div class="w-full aspect-square rounded-xl bg-gradient-to-b from-white/[0.08] to-white/[0.02] border border-white/10 p-2.5 flex items-center justify-center overflow-hidden group-hover:bg-white/[0.1] transition">
                            @if($image)
                                <img src="{{ $image->resolved_url }}" alt="{{ $image->alt_text ?: $product->name_fa }}" class="w-full h-full object-contain rounded-lg group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            @else
                                <span class="text-3xl opacity-50">📦</span>
                            @endif
                        </div>
                    </a>
                    <div class="mt-2.5 flex items-center justify-between gap-2 text-[10px] text-gold font-bold">
                        <span class="truncate">{{ $product->category?->name_fa ?? 'کالا' }}</span>
                        @if($product->brand)
                            <span class="text-slate-400 font-normal truncate">{{ $product->brand->name_fa }}</span>
                        @endif
                    </div>
                    <a href="{{ route('catalog.show', $product) }}" class="block">
                        <h2 class="mt-1 text-xs sm:text-sm font-bold text-white group-hover:text-gold transition-colors line-clamp-2 leading-snug">{{ $product->name_fa }}</h2>
                    </a>
                    @if($product->name_en)
                        <p class="mt-1 text-[10px] text-slate-500 line-clamp-1" dir="ltr">{{ $product->name_en }}</p>
                    @endif
                    <div class="mt-auto pt-3">
                        <div class="pt-2.5 border-t border-white/10 flex items-center justify-between gap-2 text-[10px]">
                            <span class="text-slate-400">حداقل: <b class="text-slate-200">{{ number_format($product->moq) }}</b></span>
                            <span class="text-gold font-bold text-[11px] text-left">
                                {{ $product->base_price !== null ? number_format((float) $product->base_price) . ' ' . $product->base_currency : 'استعلام' }}
                            </span>
                        </div>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <a href="{{ route('catalog.show', $product) }}" class="text-center rounded-lg bg-white/5 hover:bg-white/10 px-2 py-2 text-[10px] font-bold text-white">مشاهده</a>
                            <a href="{{ route('sourcing.create', ['product' => $product->slug]) }}" class="text-center rounded-lg gradient-crimson px-2 py-2 text-[10px] font-bold text-white">استعلام</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-10">{{ $products->links() }}</div>
    @else
        <div class="glass-card rounded-2xl border border-white/10 p-12 text-center">
            <h2 class="text-xl font-bold text-white">محصولی پیدا نشد</h2>
            <p class="text-sm text-slate-400 mt-2">فیلترها را تغییر دهید یا درخواست تأمین مستقیم ثبت کنید.</p>
            <a href="{{ route('sourcing.create') }}" class="inline-block mt-6 gradient-crimson text-white rounded-xl px-6 py-3 text-sm font-bold">ثبت درخواست تأمین</a>
        </div>
    @endif
</div>
@endsection
