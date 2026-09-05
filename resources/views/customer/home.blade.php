@extends('layouts.customer')

@section('title', 'Veripay - Smart Shopping')

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
<div class="space-y-8">
    <section class="grid gap-4 lg:grid-cols-[1.25fr_0.75fr]">
        <div class="shop-card overflow-hidden">
            <div class="rage-hero">
                <div class="rage-hero-content">
                    <div class="section-kicker">Today only</div>
                    <h1>
                        Scan, shop, and pay yourself.
                    </h1>
                    <p>
                        Browse daily essentials, scan product QR codes, track your cart in real time, and finish checkout from your phone.
                    </p>
                    <div class="rage-hero-actions">
                        <a href="{{ route('shop') }}" class="shop-btn shop-btn-primary">Shop Now</a>
                        <a href="{{ route('scan') }}" class="shop-btn shop-btn-secondary">Start Scan</a>
                    </div>
                </div>
            </div>
        </div>

        <aside class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
            <a href="{{ route('scan') }}" class="shop-card block p-5 transition hover:-translate-y-0.5">
                <div class="text-sm font-bold text-slate-500">Scan & Go</div>
                <div class="mt-2 text-2xl font-black text-slate-950">Use QR code scan</div>
                <p class="mt-2 text-sm text-slate-600">Add products instantly while walking the aisles.</p>
            </a>
            <a href="{{ route('cart') }}" class="shop-card block p-5 transition hover:-translate-y-0.5">
                <div class="text-sm font-bold text-slate-500">Live Cart</div>
                <div class="mt-2 text-2xl font-black text-slate-950">Track your total</div>
                <p class="mt-2 text-sm text-slate-600">Review quantity, subtotal, and checkout faster.</p>
            </a>
        </aside>
    </section>

    <section id="categories" class="space-y-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="section-kicker">Departments</div>
                <h2 class="section-title mt-1">Shop by category</h2>
            </div>
            <a href="{{ route('shop') }}" class="hidden text-sm font-extrabold text-sky-800 sm:inline">View all</a>
        </div>
        <div class="horizontal-rail">
            @foreach($categories as $category)
                <a href="{{ route('shop', ['category_id' => $category->id]) }}" class="shop-card block overflow-hidden">
                    <div class="product-media {{ $mediaClass($category->name) }} min-h-[118px]">
                        <div class="product-glyph">{{ $initials($category->name) }}</div>
                    </div>
                    <div class="p-4">
                        <div class="font-extrabold text-slate-950">{{ $category->name }}</div>
                        <div class="mt-1 text-xs font-bold text-slate-500">{{ $category->products_count }} products</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-end justify-between gap-4">
            <div>
                <div class="section-kicker">Featured</div>
                <h2 class="section-title mt-1">Popular products</h2>
            </div>
            <a href="{{ route('shop') }}" class="text-sm font-extrabold text-sky-800">Search all</a>
        </div>
        <div class="product-grid">
            @foreach($products as $product)
                <article class="product-card">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <div class="product-media {{ $mediaClass($product->category?->name) }}">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                            @else
                                <div class="product-glyph">{{ $initials($product->name) }}</div>
                            @endif
                        </div>
                    </a>
                    <div class="space-y-2 p-3">
                        <div>
                            <a href="{{ route('products.show', $product) }}" class="line-clamp-2 text-sm font-extrabold leading-5 text-slate-950">
                                {{ $product->name }}
                            </a>
                            <div class="text-xs font-semibold text-slate-500">{{ $product->category?->name ?? 'Store item' }}</div>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-base font-black text-slate-950">GHS {{ number_format($product->price, 2) }}</div>
                            <button type="button" data-add-to-cart="{{ $product->id }}" class="shop-btn shop-btn-primary !rounded-xl !px-3 !py-2 text-xs">Add</button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div>
            <div class="section-kicker">Recommended</div>
            <h2 class="section-title mt-1">Trending near you</h2>
        </div>
        <div class="horizontal-rail">
            @foreach($trending as $product)
                <article class="product-card">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <div class="product-media {{ $mediaClass($product->category?->name) }} min-h-[124px]">
                            @if($product->image_path)
                                <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}">
                            @else
                                <div class="product-glyph">{{ $initials($product->name) }}</div>
                            @endif
                        </div>
                    </a>
                    <div class="p-4">
                        <a href="{{ route('products.show', $product) }}" class="line-clamp-2 text-sm font-extrabold text-slate-950">{{ $product->name }}</a>
                        <div class="mt-1 text-xs font-semibold text-slate-500">{{ $product->stock_quantity }} in stock</div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="font-black text-slate-950">GHS {{ number_format($product->price, 2) }}</span>
                            <button type="button" data-add-to-cart="{{ $product->id }}" class="rounded-full bg-orange-500 px-3 py-2 text-xs font-extrabold text-white shadow-lg">Add</button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
