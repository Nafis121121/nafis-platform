@extends('layouts.app')

@section('content')
    <div class="sticky top-20 z-40 bg-amber-400 text-black px-4 py-2 text-center text-xs font-black shadow-lg">
        پیش‌نمایش CMS — داده‌های پیش‌نویس نمایش داده می‌شوند و برای بازدیدکنندگان سایت قابل مشاهده نیستند.
    </div>

    <div class="space-y-16 sm:space-y-24 pt-6">
        @forelse($page->blocks as $block)
            <section id="preview-block-{{ $block->block_key ?? $block->id }}" class="block-wrapper">
                @php
                    $type = $block->type->value;
                    $data = $block->draft_data ?? $block->published_data ?? [];
                @endphp

                @if(view()->exists("components.blocks.{$type}"))
                    @include("components.blocks.{$type}", ['block' => $block, 'data' => $data])
                @endif
            </section>
        @empty
            <div class="max-w-4xl mx-auto px-4 py-24 text-center text-slate-400">این صفحه هنوز بلوکی ندارد.</div>
        @endforelse
    </div>
@endsection
