@extends('layouts.admin')

@section('page_title', 'Product QR Labels')

@section('content')
@php
    $productQrCode = app(\App\Services\ProductQrCode::class);
@endphp

<div class="card p-4 print:hidden">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-black text-slate-950">Printable product QR labels</h2>
            <p class="text-sm font-semibold text-slate-500">Each QR code contains the product QR value used by the scan-to-cart page.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn-outline">Back to Products</a>
            <button class="btn-primary" type="button" onclick="window.print()">Print Labels</button>
        </div>
    </div>
</div>

<div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @forelse($products as $product)
        <article class="break-inside-avoid rounded-lg border border-slate-200 bg-white p-4 text-center shadow-sm">
            <img src="{{ $productQrCode->dataUri($product) }}" alt="QR code for {{ $product->name }}" class="mx-auto h-36 w-36">
            <h2 class="mt-3 text-base font-black text-slate-950">{{ $product->name }}</h2>
            <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">{{ $product->barcode }}</p>
            <p class="mt-1 text-sm font-extrabold text-slate-700">GHS {{ number_format($product->price, 2) }}</p>
            <p class="mt-1 text-xs font-semibold text-slate-400">{{ $product->category?->name ?? 'Uncategorized' }}</p>
        </article>
    @empty
        <div class="card p-5 text-sm font-semibold text-slate-500">No active products found.</div>
    @endforelse
</div>

<style>
    @media print {
        body {
            background: #fff !important;
        }

        aside,
        header,
        nav,
        .admin-sidebar,
        .admin-header {
            display: none !important;
        }

        main,
        .admin-main,
        .content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: none !important;
        }

        article {
            box-shadow: none !important;
            page-break-inside: avoid;
        }
    }
</style>
@endsection
