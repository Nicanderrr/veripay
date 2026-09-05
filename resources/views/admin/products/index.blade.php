@extends('layouts.admin')

@section('page_title', 'Products')

@section('content')
@php
    $productQrCode = app(\App\Services\ProductQrCode::class);
@endphp

<div class="card p-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <form method="GET" class="flex flex-1 flex-col gap-3 md:flex-row md:items-center">
            <input class="field max-w-md" type="search" name="q" value="{{ request('q') }}" placeholder="Search product name or QR code">
            <button class="btn-outline" type="submit">Search</button>
        </form>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.products.qr-labels') }}" class="btn-outline">QR Labels</a>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">Add Product</a>
        </div>
    </div>
</div>

<div class="card mt-4 overflow-x-auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>QR Code</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                @php
                    $stock = (int) $product->stock_quantity;
                    $statusClass = $stock === 0 ? 'status-danger' : ($stock <= 10 ? 'status-warn' : 'status-ok');
                    $statusText = $stock === 0 ? 'Out' : ($stock <= 10 ? 'Low' : 'In Stock');
                @endphp
                <tr>
                    <td>
                        @if($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-11 w-11 rounded-lg border border-slate-200 bg-white object-contain p-1">
                        @else
                            <div class="h-11 w-11 rounded-lg border border-slate-200 bg-slate-100"></div>
                        @endif
                    </td>
                    <td>
                        <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                        <div class="text-xs text-slate-500">QR: {{ $product->barcode }}</div>
                    </td>
                    <td>
                        @if($product->is_active)
                            <button
                                type="button"
                                class="qr-open-button inline-flex flex-col items-center gap-1 rounded-lg border border-slate-200 bg-white p-2 text-[10px] font-bold uppercase tracking-wide text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                                data-qr-src="{{ $productQrCode->dataUri($product) }}"
                                data-product-name="{{ e($product->name) }}"
                                data-qr-value="{{ e($product->barcode) }}"
                                data-product-price="GHS {{ number_format($product->price, 2) }}"
                                data-product-category="{{ e($product->category?->name ?? 'Uncategorized') }}"
                            >
                                <img src="{{ $productQrCode->dataUri($product) }}" alt="QR code for {{ $product->name }}" class="h-14 w-14">
                                Open
                            </button>
                        @else
                            <span class="text-xs font-semibold text-slate-400">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
                    <td>GHS {{ number_format($product->price, 2) }}</td>
                    <td>{{ $stock }}</td>
                    <td><span class="status-pill {{ $statusClass }}">{{ $statusText }}</span></td>
                    <td>
                        <div class="flex items-center gap-2">
                            <a class="btn-outline" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="Delete this product?">
                                @csrf
                                @method('DELETE')
                                <button class="btn-outline" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-slate-500">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>

<div id="qr-modal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-950/70 px-4 py-6 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="qr-modal-title">
    <div class="w-full max-w-lg overflow-hidden rounded-lg bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 p-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-red-500">Product QR Code</p>
                <h2 id="qr-modal-title" class="mt-1 text-2xl font-extrabold text-slate-950">Product QR Code</h2>
                <p id="qr-modal-meta" class="mt-1 text-sm font-semibold text-slate-500"></p>
            </div>
            <button id="qr-modal-close" type="button" class="grid h-10 w-10 place-items-center rounded-md border border-slate-200 bg-white text-slate-700 hover:bg-red-50 hover:text-red-600" aria-label="Close QR code modal">
                <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
            </button>
        </div>

        <div class="p-6 text-center">
            <div class="mx-auto inline-flex rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <img id="qr-modal-image" src="" alt="Product QR code" class="h-72 w-72 max-w-full">
            </div>
            <div class="mt-5 rounded-lg bg-slate-50 px-4 py-3">
                <div class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">QR Value</div>
                <div id="qr-modal-value" class="mt-1 break-all text-lg font-black text-slate-950"></div>
            </div>
            <div class="mt-5 flex flex-wrap justify-center gap-2">
                <button id="qr-modal-print" type="button" class="btn-primary">Print QR Code</button>
                <button id="qr-modal-dismiss" type="button" class="btn-outline">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function () {
        const modal = document.getElementById('qr-modal');
        const title = document.getElementById('qr-modal-title');
        const meta = document.getElementById('qr-modal-meta');
        const image = document.getElementById('qr-modal-image');
        const value = document.getElementById('qr-modal-value');
        const closeButtons = [
            document.getElementById('qr-modal-close'),
            document.getElementById('qr-modal-dismiss'),
        ].filter(Boolean);
        const printButton = document.getElementById('qr-modal-print');

        if (!modal || !title || !meta || !image || !value) return;

        function openQrModal(button) {
            title.textContent = button.dataset.productName || 'Product QR Code';
            meta.textContent = [button.dataset.productCategory, button.dataset.productPrice].filter(Boolean).join(' - ');
            image.src = button.dataset.qrSrc || '';
            image.alt = `QR code for ${button.dataset.productName || 'product'}`;
            value.textContent = button.dataset.qrValue || 'N/A';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeQrModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            image.src = '';
        }

        document.querySelectorAll('.qr-open-button').forEach(button => {
            button.addEventListener('click', () => openQrModal(button));
        });

        closeButtons.forEach(button => button.addEventListener('click', closeQrModal));
        modal.addEventListener('click', event => {
            if (event.target === modal) closeQrModal();
        });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeQrModal();
        });

        if (printButton) {
            printButton.addEventListener('click', () => {
                const printWindow = window.open('', '_blank', 'width=520,height=680');
                if (!printWindow) {
                    window.showAlert('Popup blocked. Please allow popups to print this QR code.', 'error');
                    return;
                }

                printWindow.document.write(`
                    <!doctype html>
                    <html>
                    <head>
                        <title>${title.textContent}</title>
                        <style>
                            body { font-family: Arial, sans-serif; text-align: center; padding: 32px; color: #111827; }
                            img { width: 320px; height: 320px; }
                            h1 { font-size: 24px; margin: 20px 0 8px; }
                            p { margin: 6px 0; font-weight: 700; }
                        </style>
                    </head>
                    <body>
                        <img src="${image.src}" alt="${image.alt}">
                        <h1>${title.textContent}</h1>
                        <p>${value.textContent}</p>
                        <p>${meta.textContent}</p>
                    </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            });
        }
    })();
</script>
@endsection
