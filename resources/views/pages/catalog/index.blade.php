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
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($products as $product)
                <article class="glass-card rounded-2xl border border-white/10 p-5 hover:border-gold/30 transition-all group">
                    <div class="h-36 rounded-xl bg-gradient-to-br from-white/10 to-white/[.02] flex items-center justify-center mb-5 border border-white/5">
                        @if($product->images->first())
                            <img src="{{ $product->images->first()->resolved_url }}" alt="{{ $product->images->first()->alt_text ?: $product->name_fa }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            <span class="text-4xl opacity-60">📦</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-gold mb-2">
                        <span>{{ $product->category?->name_fa ?? 'کالا' }}</span>
                        @if($product->brand)<span class="text-slate-600">•</span><span class="text-slate-400">{{ $product->brand->name_fa }}</span>@endif
                    </div>
                    <h2 class="text-lg font-black text-white group-hover:text-gold transition-colors">{{ $product->name_fa }}</h2>
                    @if($product->name_en)<p class="text-xs text-slate-500 mt-1" dir="ltr">{{ $product->name_en }}</p>@endif
                    <div class="mt-5 flex items-center justify-between text-xs">
                        <span class="text-slate-400">MOQ: <b class="text-white">{{ number_format($product->moq) }}</b></span>
                        <span class="text-slate-400">قیمت:
                           <b class="text-gold">{{ $product->base_price !== null ? number_format((float) $product->base_price) . ' ' . $product->base_currency : 'استعلام' }}</b>
                        </span>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <a href="{{ route('catalog.show', $product) }}" class="text-center rounded-xl bg-white/5 hover:bg-white/10 px-3 py-3 text-xs font-bold text-white">مشاهده محصول</a>
                        <a href="{{ route('sourcing.create', ['product' => $product->slug]) }}" class="text-center rounded-xl gradient-crimson px-3 py-3 text-xs font-bold text-white">درخواست تأمین</a>
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
