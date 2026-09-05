@extends('layouts.customer')

@section('title', 'Shop Products')

@section('content')
<div class="shop-grid">
    <aside class="shop-filter-panel">
        <div class="section-kicker">Catalog</div>
        <h1 class="filter-title">Find products</h1>

        <div class="relative mt-4">
            <input id="search" class="shop-input pr-10" value="{{ request('search') }}" placeholder="Search apples, coffee, detergent...">
            <div id="suggestions" class="absolute left-0 right-0 top-[calc(100%+6px)] z-30 hidden overflow-hidden rounded-md border border-slate-200 bg-white shadow-xl"></div>
        </div>

        <div class="mt-4 grid gap-3">
            <label class="field-label">Category</label>
            <select id="category-filter" class="shop-select">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="field-label">Min</label>
                    <input id="min-price" class="shop-input mt-1" type="number" min="0" step="0.01" placeholder="GHS 0">
                </div>
                <div>
                    <label class="field-label">Max</label>
                    <input id="max-price" class="shop-input mt-1" type="number" min="0" step="0.01" placeholder="GHS 100">
                </div>
            </div>

            <label class="stock-toggle">
                <input id="in-stock" type="checkbox">
                In stock only
            </label>

            <button id="search-btn" class="shop-btn shop-btn-primary w-full" type="button">Apply Filters</button>
        </div>

        <div class="self-checkout-panel">
            <i class="fa fa-qrcode" aria-hidden="true"></i>
            <div>
                <div class="font-bold text-slate-950">Self checkout</div>
                <p>Scan items in-store, pay from your cart, and leave with a digital order record.</p>
            </div>
        </div>
    </aside>

    <section class="min-w-0 space-y-6">
        <div class="catalog-heading">
            <div>
                <div class="section-kicker">Products</div>
                <h2 class="section-title mt-1">Shop the store</h2>
            </div>
            <div class="catalog-tags">
                <span>Fast add</span>
                <span>Live stock</span>
                <span>Self pay</span>
            </div>
        </div>

        <div id="shop-status" class="hidden rounded-md border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-bold text-sky-900"></div>
        <div id="results" class="product-grid"></div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    const resultsEl = document.getElementById('results');
    const suggestionsEl = document.getElementById('suggestions');
    const statusEl = document.getElementById('shop-status');
    const searchInput = document.getElementById('search');
    const categoryFilter = document.getElementById('category-filter');
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');
    const inStockInput = document.getElementById('in-stock');
    let debounceTimer = null;

    function mediaClass(category) {
        const name = String(category || '').toLowerCase();
        if (name.includes('produce')) return 'media-produce';
        if (name.includes('beverage')) return 'media-beverages';
        if (name.includes('snack')) return 'media-snacks';
        if (name.includes('house')) return 'media-household';
        if (name.includes('frozen')) return 'media-frozen';
        return 'media-default';
    }

    function initials(name) {
        return String(name || 'Item').split(/\s+/).filter(Boolean).slice(0, 2).map(part => part[0]).join('').toUpperCase();
    }

    function productImageUrl(product) {
        return product && product.image_path ? `/storage/${product.image_path}` : '';
    }

    function setStatus(message, tone = 'info') {
        statusEl.textContent = message || '';
        statusEl.classList.toggle('hidden', !message);
        statusEl.classList.toggle('border-rose-200', tone === 'error');
        statusEl.classList.toggle('bg-rose-50', tone === 'error');
        statusEl.classList.toggle('text-rose-800', tone === 'error');
    }

    function queryString() {
        const params = new URLSearchParams();
        if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
        if (categoryFilter.value) params.set('category_id', categoryFilter.value);
        if (minPriceInput.value) params.set('min_price', minPriceInput.value);
        if (maxPriceInput.value) params.set('max_price', maxPriceInput.value);
        if (inStockInput.checked) params.set('in_stock', '1');
        return params.toString();
    }

    async function loadProducts(showSuggestions = false) {
        setStatus(showSuggestions ? '' : 'Loading products...');
        const data = await window.apiFetch('/api/products?' + queryString());
        renderProducts(data.data || []);
        if (showSuggestions) renderSuggestions(data.data || []);
        else suggestionsEl.classList.add('hidden');
        setStatus('');
    }

    function renderSuggestions(products) {
        suggestionsEl.replaceChildren();
        products.slice(0, 5).forEach(product => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'flex w-full items-center justify-between px-4 py-3 text-left text-sm font-bold text-slate-700 hover:bg-slate-50';
            button.textContent = product.name;
            button.addEventListener('click', () => {
                searchInput.value = product.name;
                suggestionsEl.classList.add('hidden');
                loadProducts();
            });
            suggestionsEl.appendChild(button);
        });
        suggestionsEl.classList.toggle('hidden', products.length === 0 || !searchInput.value.trim());
    }

    function renderProducts(products) {
        resultsEl.replaceChildren();
        if (!products.length) {
            const empty = document.createElement('div');
            empty.className = 'shop-card col-span-full p-8 text-center text-sm font-bold text-slate-500';
            empty.textContent = 'No products match these filters.';
            resultsEl.appendChild(empty);
            return;
        }

        products.forEach(product => {
            const card = document.createElement('article');
            card.className = 'product-card';

            const media = document.createElement('a');
            media.href = `/products/${product.id}`;
            media.className = `product-media ${mediaClass(product.category?.name)}`;

            const imageUrl = productImageUrl(product);
            if (imageUrl) {
                const image = document.createElement('img');
                image.src = imageUrl;
                image.alt = product.name || 'Product image';
                media.appendChild(image);
            } else {
                const glyph = document.createElement('div');
                glyph.className = 'product-glyph';
                glyph.textContent = initials(product.name);
                media.appendChild(glyph);
            }

            const body = document.createElement('div');
            body.className = 'space-y-2 p-3';

            const name = document.createElement('a');
            name.href = `/products/${product.id}`;
            name.className = 'block line-clamp-2 text-sm font-extrabold leading-5 text-slate-950';
            name.textContent = product.name || 'Unknown product';

            const meta = document.createElement('div');
            meta.className = 'text-xs font-semibold text-slate-500';
            meta.textContent = `${product.category?.name || 'Store item'} - ${Number(product.stock_quantity || 0)} in stock`;

            const priceRow = document.createElement('div');
            priceRow.className = 'flex items-center justify-between gap-2';

            const price = document.createElement('div');
            price.className = 'text-base font-black text-slate-950';
            price.textContent = 'GHS ' + Number(product.price || 0).toFixed(2);

            const add = document.createElement('button');
            add.type = 'button';
            add.className = 'shop-btn shop-btn-primary !px-3 !py-2 text-xs';
            add.textContent = 'Add';
            add.addEventListener('click', () => window.addToCart(product.id, add));

            priceRow.append(price, add);
            body.append(name, meta, priceRow);
            card.append(media, body);
            resultsEl.appendChild(card);
        });
    }

    document.getElementById('search-btn').addEventListener('click', () => loadProducts().catch(err => setStatus(err.message, 'error')));

    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadProducts(true).catch(() => {});
        }, 240);
    });

    [categoryFilter, minPriceInput, maxPriceInput, inStockInput].forEach(input => {
        input.addEventListener('change', () => loadProducts().catch(err => setStatus(err.message, 'error')));
    });

    loadProducts().catch(err => setStatus(err.message, 'error'));
</script>
@endsection
