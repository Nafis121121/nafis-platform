@php
    $isAuto = ($rates['mode'] ?? 'manual') === 'auto';
    $modeLabel = $isAuto ? 'به‌روزرسانی خودکار' : 'تنظیم دستی نرخ‌ها';
    $modeColor = $isAuto ? 'bg-emerald-500' : 'bg-amber-500';
    $updatedAtLabel = null;
    if (!empty($rates['updated_at'])) {
        try {
            $updatedAtLabel = \Illuminate\Support\Carbon::parse($rates['updated_at'])->diffForHumans();
        } catch (\Throwable $e) {
            $updatedAtLabel = null;
        }
    }
@endphp
<div class="w-full bg-slate-900 border-b border-slate-800 text-slate-100 text-sm shadow-sm" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-2 flex flex-wrap items-center justify-center sm:justify-between gap-x-6 gap-y-2">
        <!-- نرخ ارزها همراه با پرچم و سایز خوانا -->
        <div class="flex items-center gap-x-3 sm:gap-x-4 flex-wrap justify-center font-medium">
            <!-- یوان چین -->
            <div class="inline-flex items-center gap-1.5 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/60 shadow-inner">
                <span class="text-base" title="چین">🇨🇳</span>
                <span class="font-bold text-amber-400">یوان (CNY):</span>
                <span class="font-mono text-white font-bold tracking-wide">{{ number_format((float) ($rates['CNY'] ?? 0)) }}</span>
                <span class="text-xs text-slate-400">ریال</span>
            </div>

            <!-- درهم امارات -->
            <div class="inline-flex items-center gap-1.5 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/60 shadow-inner">
                <span class="text-base" title="امارات متحده عربی">🇦🇪</span>
                <span class="font-bold text-amber-400">درهم (AED):</span>
                <span class="font-mono text-white font-bold tracking-wide">{{ number_format((float) ($rates['AED'] ?? 0)) }}</span>
                <span class="text-xs text-slate-400">ریال</span>
            </div>

            <!-- دلار آمریکا -->
            <div class="inline-flex items-center gap-1.5 bg-slate-800/80 px-2.5 py-1 rounded-md border border-slate-700/60 shadow-inner">
                <span class="text-base" title="ایالات متحده آمریکا">🇺🇸</span>
                <span class="font-bold text-amber-400">دلار (USD):</span>
                <span class="font-mono text-white font-bold tracking-wide">{{ number_format((float) ($rates['USD'] ?? 0)) }}</span>
                <span class="text-xs text-slate-400">ریال</span>
            </div>
        </div>

        <!-- وضعیت به‌روزرسانی -->
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <span class="inline-flex items-center gap-1.5 bg-slate-800/50 px-2 py-0.5 rounded-full border border-slate-700/40">
                <span class="inline-block w-2 h-2 rounded-full {{ $modeColor }} animate-pulse"></span>
                <span class="text-slate-300 font-medium">{{ $modeLabel }}</span>
            </span>
            @if($updatedAtLabel)
                <span class="hidden md:inline text-slate-500">· آخرین همگام‌سازی: {{ $updatedAtLabel }}</span>
            @endif
        </div>
    </div>
</div>