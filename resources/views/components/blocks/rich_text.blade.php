@php
    $title = $data['title'] ?? null;
    $content = $data['content'] ?? $data['body'] ?? '';
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6">
    <div class="glass-card rounded-3xl p-8 sm:p-12 border border-white/10 space-y-4">
        @if($title)
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white border-r-4 border-primary pr-4">{{ $title }}</h2>
        @endif
        <div class="text-sm sm:text-base text-slate-300 leading-loose text-justify pt-2">
            {!! nl2br(e($content)) !!}
        </div>
    </div>
</div>