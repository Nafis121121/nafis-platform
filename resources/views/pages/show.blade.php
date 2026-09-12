@extends('layouts.app')

@section('content')
    <div class="space-y-16 sm:space-y-24">
        @forelse($page->publishedBlocks as $block)
            <section id="block-{{ $block->block_key ?? $block->id }}" class="block-wrapper">
                @php
                    $type = $block->type->value;
                    $data = $block->published_data ?? [];
                @endphp

                @if(view()->exists("components.blocks.{$type}"))
                    @include("components.blocks.{$type}", ['block' => $block, 'data' => $data])
                @else
                    <div class="max-w-7xl mx-auto px-4 py-8">
                        <div class="glass-card p-6 rounded-2xl border border-yellow-500/20 text-yellow-300 text-sm">
                            بلوک [{{ $block->type->label() }}] آماده رندر است.
                        </div>
                    </div>
                @endif
            </section>
        @empty
            <div class="max-w-4xl mx-auto px-4 py-24 text-center">
                <div class="glass-card p-12 rounded-3xl border border-white/10">
                    <h2 class="text-2xl font-bold text-white mb-2">{{ $page->title_fa }}</h2>
                    <p class="text-slate-400 text-sm">محتوای این صفحه به زودی منتشر خواهد شد.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection