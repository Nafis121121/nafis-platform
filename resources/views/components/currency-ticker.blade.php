@php
    $modeLabel = ($rates['mode'] ?? 'manual') === 'auto' ? 'به‌روزرسانی خودکار' : 'نرخ دستی';
    $modeColor = ($rates['mode'] ?? 'manual') === 'auto' ? 'bg-emerald-500' : 'bg-amber-500';
    $updatedAtLabel = null;
    if (!empty($rates['updated_at'])) {
        try {
            $updatedAtLabel = \Illuminate\Support\Carbon::parse($rates['updated_at'])->diffForHumans();
        } catch (\Throwable $e) {
            $updatedAtLabel = null;
        }
    }
@endphp
<div class="w-full bg-gray-900 text-gray-100 text-xs" dir="rtl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-1.5 flex flex-wrap items-center justify-center sm:justify-between gap-x-6 gap-y-1">
        <div class="flex items-center gap-x-5 flex-wrap justify-center">
            <span class="flex items-center gap-1.5">
                <span class="font-bold text-amber-400">CNY</span>
                <span class="font-mono">{{ number_format((float) ($rates['CNY'] ?? 0)) }}</span>
                <span class="text-gray-400">ریال</span>
            </span>
            <span class="flex items-center gap-1.5">
                <span class="font-bold text-amber-400">AED</span>
                <span class="font-mono">{{ number_format((float) ($rates['AED'] ?? 0)) }}</span>
                <span class="text-gray-400">ریال</span>
            </span>
            <span class="flex items-center gap-1.5">
                <span class="font-bold text-amber-400">USD</span>
                <span class="font-mono">{{ number_format((float) ($rates['USD'] ?? 0)) }}</span>
                <span class="text-gray-400">ریال</span>
            </span>
        </div>
        <div class="flex items-center gap-2 text-[11px] text-gray-400">
            <span class="inline-flex items-center gap-1">
                <span class="inline-block w-1.5 h-1.5 rounded-full {{ $modeColor }} animate-pulse"></span>
                {{ $modeLabel }}
            </span>
            @if($updatedAtLabel)
                <span>· آخرین به‌روزرسانی: {{ $updatedAtLabel }}</span>
            @endif
        </div>
    </div>
</div>
