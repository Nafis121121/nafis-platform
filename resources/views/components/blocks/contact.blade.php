@php
    $title = $data['title'] ?? 'اطلاعات تماس';
    $address = $data['address'] ?? '';
    $phoneDisplay = $data['phoneDisplay'] ?? '';
    $phoneIntl = $data['phoneIntl'] ?? '';
    $email = $data['email'] ?? '';
    $whatsapp = $data['whatsapp'] ?? null;
    $telegram = $data['telegram'] ?? null;
    $instagram = $data['instagram'] ?? null;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="glass-card rounded-3xl p-8 sm:p-12 border border-white/10 grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-white">{{ $title }}</h2>
            <p class="text-sm text-slate-400 mt-3">
                جهت دریافت مشاوره بازرگانی، استعلام قیمت واردات کالا یا هماهنگی جلسات حضوری با ما تماس بگیرید.
            </p>

            <div class="mt-8 space-y-4 text-sm text-slate-300">
                <div class="flex items-start gap-3">
                    <span class="text-primary text-lg">📍</span>
                    <div>
                        <strong class="text-white block">دفتر مرکزی:</strong>
                        <span>{{ $address }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-primary text-lg">📞</span>
                    <div>
                        <strong class="text-white">شماره تماس:</strong>
                        <a href="tel:{{ $phoneIntl }}" class="hover:text-gold font-bold dir-ltr mr-2">{{ $phoneDisplay }}</a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-primary text-lg">✉️</span>
                    <div>
                        <strong class="text-white">پست الکترونیکی:</strong>
                        <span class="dir-ltr mr-2">{{ $email }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @if($whatsapp)
                    <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl glass-card text-emerald-400 text-xs font-bold hover:bg-emerald-500/10 transition-colors flex items-center gap-2">
                        <span>واتس‌اپ بیزینس</span>
                    </a>
                @endif
                @if($telegram)
                    <a href="{{ $telegram }}" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl glass-card text-sky-400 text-xs font-bold hover:bg-sky-500/10 transition-colors flex items-center gap-2">
                        <span>کانال تلگرام</span>
                    </a>
                @endif
                @if($instagram)
                    <a href="{{ $instagram }}" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl glass-card text-pink-400 text-xs font-bold hover:bg-pink-500/10 transition-colors flex items-center gap-2">
                        <span>اینستاگرام</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-surface/50 rounded-2xl p-6 sm:p-8 border border-white/5 flex flex-col justify-center">
            <h3 class="text-lg font-bold text-white mb-2">ثبت سریع پیام یا استعلام</h3>
            <p class="text-xs text-slate-400 mb-6">کارشناسان بازرگانی در کمتر از ۲ ساعت کاری با شما تماس می‌گیرند.</p>

            <form id="contact-form" action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                @csrf
                @if(session('contact_success'))
                    <div class="rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-xs text-emerald-300">
                        {{ session('contact_success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="rounded-xl border border-red-400/20 bg-red-400/10 px-4 py-3 text-xs text-red-300">
                        {{ $errors->first() }}
                    </div>
                @endif

                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">نام و نام خانوادگی / نام شرکت</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-obsidian border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-gold outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">شماره همراه</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required class="w-full bg-obsidian border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-gold outline-none dir-ltr text-right">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-300 mb-1">شرح درخواست یا استعلام کالا</label>
                    <textarea name="message" rows="3" required class="w-full bg-obsidian border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-gold outline-none">{{ old('message') }}</textarea>
                </div>
                <button type="submit" class="w-full gradient-crimson text-white py-3 rounded-xl text-xs font-bold hover:scale-[1.02] transition-transform">
                    ارسال پیام به کارشناس بازرگانی
                </button>
            </form>
        </div>
    </div>
</div>