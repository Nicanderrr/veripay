@extends('layouts.customer')

@section('title', $product->name . ' - Veripay')

@php
    $mediaClass = function (?string $category): string {
        $name = strtolower($category ?? '');
        return match (true) {
            str_contains($name, 'produce') => 'media-produce',
            str_contains($name, 'beverage') => 'media-beverages',
            str_contains($name, 'snack') => 'media-snacks',
            str_contains($name, 'house') => 'media-household',
            str_contains($name, 'frozen') => 'media-frozen',
            default => 'media-beverages',
        };
    };

    $initials = fn (string $name): string => collect(explode(' ', $name))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->join('');
@endphp

@section('content')
<div class="grid gap-6 lg:grid-cols-[1fr_0.9fr]">
    <section class="shop-card overflow-hidden">
        <div class="product-media {{ $mediaClass($product->category?->name) }} min-h-[360px] sm:min-h-[460px]">
            @if($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
            @else
                <div class="product-glyph !h-40 !w-32 !rounded-[34px] !text-4xl">{{ $initials($product->name) }}</div>
            @endif
        </div>
        <div class="grid grid-cols-3 gap-3 p-4">
            @foreach(range(1, 3) as $index)
                <div class="product-media {{ $mediaClass($product->category?->name) }} min-h-[96px] rounded-2xl">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                    @else
                        <div class="product-glyph !h-14 !w-12 !rounded-xl !text-sm">{{ $initials($product->name) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <aside class="space-y-5">
        <div class="shop-card p-5 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="text-sm font-extrabold text-sky-800">{{ $product->category?->name ?? 'Store item' }}</div>
                    <h1 class="mt-2 text-3xl font-black leading-tight text-slate-950 sm:text-4xl">{{ $product->name }}</h1>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-2 text-xs font-extrabold text-emerald-700">
                    {{ $product->stock_quantity > 0 ? 'In stock' : 'Out of stock' }}
                </span>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <span class="rating-stars text-base">*****</span>
                <span class="text-sm font-bold text-slate-500">4.8 - 124 reviews</span>
            </div>

            <div class="mt-5 text-4xl font-black text-slate-950">GHS {{ number_format($product->price, 2) }}</div>
            <p class="mt-4 text-base leading-7 text-slate-600">
                {{ $product->description ?: 'A popular store essential selected for fast shopping, live cart tracking, and self checkout.' }}
            </p>

            <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="font-extrabold text-slate-950">Stock</div>
                    <div class="mt-1 font-semibold text-slate-500">{{ $product->stock_quantity }} available</div>
                </div>
            </div>

            <div class="mt-6 hidden gap-3 sm:grid sm:grid-cols-2">
                <button type="button" data-add-to-cart="{{ $product->id }}" class="shop-btn shop-btn-primary">Add to Cart</button>
                <button type="button" id="buy-now" class="shop-btn shop-btn-secondary">Buy Now</button>
            </div>
        </div>

        <div class="shop-card p-5">
            <div class="text-lg font-black text-slate-950">Why shoppers choose this</div>
            <div class="mt-4 grid gap-3 text-sm font-semibold text-slate-600">
                <div class="flex gap-3"><span class="text-emerald-600">✓</span> Ready for quick scan-and-pay shopping</div>
                <div class="flex gap-3"><span class="text-emerald-600">✓</span> Cart total updates instantly after adding</div>
                <div class="flex gap-3"><span class="text-emerald-600">✓</span> Checkout-ready for mobile money or card simulation</div>
            </div>
        </div>
    </aside>
</div>

@if($recommendations->isNotEmpty())
    <section class="mt-8 space-y-4">
        <div>
            <div class="section-kicker">Related</div>
            <h2 class="section-title mt-1">You may also like</h2>
        </div>
        <div class="horizontal-rail">
            @foreach($recommendations as $recommended)
                <article class="product-card">
                    <a href="{{ route('products.show', $recommended) }}" class="block">
                        <div class="product-media {{ $mediaClass($recommended->category?->name) }} min-h-[124px]">
                            @if($recommended->image_path)
                                <img src="{{ asset('storage/' . $recommended->image_path) }}" alt="{{ $recommended->name }}">
                            @else
                                <div class="product-glyph">{{ $initials($recommended->name) }}</div>
                            @endif
                        </div>
                    </a>
                    <div class="p-4">
                        <a href="{{ route('products.show', $recommended) }}" class="line-clamp-2 text-sm font-extrabold text-slate-950">{{ $recommended->name }}</a>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="font-black text-slate-950">GHS {{ number_format($recommended->price, 2) }}</span>
                            <button type="button" data-add-to-cart="{{ $recommended->id }}" class="rounded-full bg-orange-500 px-3 py-2 text-xs font-extrabold text-white shadow-lg">Add</button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif

<div class="mobile-action-bar fixed inset-x-0 z-[85] border-t border-slate-200 bg-white/95 p-3 shadow-[0_-18px_44px_rgba(15,23,42,0.12)] backdrop-blur sm:hidden">
    <div class="grid grid-cols-2 gap-2">
        <button type="button" data-add-to-cart="{{ $product->id }}" class="shop-btn shop-btn-primary !rounded-2xl !px-2 text-sm">Add</button>
        <button type="button" id="buy-now-mobile" class="shop-btn shop-btn-secondary !rounded-2xl !px-2 text-sm">Buy Now</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function buyNow(button) {
        const added = await window.addToCart({{ $product->id }}, button);
        if (added) window.location.href = '{{ route('checkout') }}';
    }

    document.getElementById('buy-now')?.addEventListener('click', event => buyNow(event.currentTarget));
    document.getElementById('buy-now-mobile')?.addEventListener('click', event => buyNow(event.currentTarget));
</script>
@endsection
