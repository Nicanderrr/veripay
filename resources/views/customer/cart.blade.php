@extends('layouts.customer')

@section('title', 'Shopping Cart')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1fr_360px]">
    <section class="space-y-4">
        <div class="shop-card p-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <div class="section-kicker">Cart</div>
                    <h1 class="section-title mt-1">Your shopping cart</h1>
                </div>
                <a href="{{ route('shop') }}" class="shop-btn shop-btn-outline !py-2 text-sm">Continue shopping</a>
            </div>
        </div>

        <div id="budget-warning" class="hidden rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-900"></div>
        <div id="cart-items" class="space-y-4"></div>
    </section>

    <aside class="shop-card h-fit p-5 lg:sticky lg:top-28">
        <h2 class="text-xl font-black text-slate-950">Order summary</h2>
        <div class="mt-5 space-y-3 text-sm font-bold text-slate-600">
            <div class="flex justify-between"><span>Subtotal</span><span id="cart-subtotal">GHS 0.00</span></div>
            <div class="flex justify-between"><span>Estimated tax</span><span id="cart-tax">GHS 0.00</span></div>
            <div class="border-t border-slate-200 pt-3 text-lg font-black text-slate-950">
                <div class="flex justify-between"><span>Total</span><span id="cart-total">GHS 0.00</span></div>
            </div>
        </div>
        <a href="{{ route('checkout') }}" class="shop-btn shop-btn-primary mt-5 w-full">Checkout</a>
        <p class="mt-3 text-center text-xs font-semibold text-slate-500">Secure checkout - Mobile money or card</p>
    </aside>
</div>

<div class="mobile-action-bar fixed inset-x-0 z-[85] border-t border-slate-200 bg-white/95 p-3 shadow-[0_-18px_44px_rgba(15,23,42,0.12)] backdrop-blur lg:hidden">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Total</div>
            <div id="cart-mobile-total" class="text-xl font-black text-slate-950">GHS 0.00</div>
        </div>
        <a href="{{ route('checkout') }}" class="shop-btn shop-btn-primary min-w-[160px]">Checkout</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const itemsEl = document.getElementById('cart-items');
    const subtotalEl = document.getElementById('cart-subtotal');
    const taxEl = document.getElementById('cart-tax');
    const totalEl = document.getElementById('cart-total');
    const mobileTotalEl = document.getElementById('cart-mobile-total');
    const warningEl = document.getElementById('budget-warning');

    function initials(name) {
        return String(name || 'Item').split(/\s+/).filter(Boolean).slice(0, 2).map(part => part[0]).join('').toUpperCase();
    }

    function mediaClass(category) {
        const name = String(category || '').toLowerCase();
        if (name.includes('produce')) return 'media-produce';
        if (name.includes('beverage')) return 'media-beverages';
        if (name.includes('snack')) return 'media-snacks';
        if (name.includes('house')) return 'media-household';
        if (name.includes('frozen')) return 'media-frozen';
        return 'media-default';
    }

    function productImageUrl(item) {
        return item && item.image_path ? `/storage/${item.image_path}` : '';
    }

    function money(value) {
        return 'GHS ' + Number(value || 0).toFixed(2);
    }

    async function changeQuantity(productId, delta, button) {
        button.disabled = true;
        try {
            await window.apiFetch(delta > 0 ? '/api/cart/add' : '/api/cart/remove', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            });
            await loadCart();
            await window.updateCartBadge();
        } catch (err) {
            window.showAlert(err.message || 'Unable to update quantity.', 'error');
        } finally {
            button.disabled = false;
        }
    }

    function renderCart(data) {
        itemsEl.replaceChildren();
        subtotalEl.textContent = money(data.subtotal);
        taxEl.textContent = money(data.tax);
        totalEl.textContent = money(data.total);
        mobileTotalEl.textContent = money(data.total);

        if (!data.items || !data.items.length) {
            const empty = document.createElement('div');
            empty.className = 'shop-card p-8 text-center';
            empty.innerHTML = `
                <div class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-sky-50 text-sky-800">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M6 7h14l-1.7 8.6a2 2 0 0 1-2 1.6H9.2a2 2 0 0 1-2-1.7L5.2 3.8H3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div class="mt-4 text-xl font-black text-slate-950">Your cart is empty</div>
                <p class="mt-2 text-sm font-semibold text-slate-500">Start shopping or scan products to checkout faster.</p>
                <a href="{{ route('shop') }}" class="shop-btn shop-btn-primary mt-5">Browse Products</a>
            `;
            itemsEl.appendChild(empty);
            return;
        }

        data.items.forEach(item => {
            const row = document.createElement('article');
            row.className = 'shop-card grid grid-cols-[94px_minmax(0,1fr)] gap-4 p-4 sm:grid-cols-[120px_minmax(0,1fr)_auto] sm:items-center';

            const media = document.createElement('a');
            media.href = `/products/${item.product_id}`;
            media.className = `product-media ${mediaClass(item.category?.name)} h-[94px] w-[94px] rounded-md border border-slate-200 sm:h-[120px] sm:w-[120px]`;

            const imageUrl = productImageUrl(item);
            if (imageUrl) {
                const image = document.createElement('img');
                image.src = imageUrl;
                image.alt = item.name || 'Product image';
                media.appendChild(image);
            } else {
                const glyph = document.createElement('div');
                glyph.className = 'product-glyph !h-14 !w-12 !rounded-xl !text-sm';
                glyph.textContent = initials(item.name);
                media.appendChild(glyph);
            }

            const info = document.createElement('div');
            const name = document.createElement('a');
            name.href = `/products/${item.product_id}`;
            name.className = 'line-clamp-2 text-base font-black text-slate-950';
            name.textContent = item.name || 'Unknown item';
            const meta = document.createElement('div');
            meta.className = 'mt-1 text-xs font-semibold text-slate-500';
            meta.textContent = item.barcode ? `QR ${item.barcode}` : 'Store item';
            const price = document.createElement('div');
            price.className = 'mt-3 text-lg font-black text-slate-950';
            price.textContent = money(item.total);
            info.append(name, meta, price);

            const controls = document.createElement('div');
            controls.className = 'col-span-2 flex items-center justify-between gap-3 sm:col-span-1 sm:flex-col sm:items-end';

            const quantity = document.createElement('div');
            quantity.className = 'inline-flex items-center overflow-hidden rounded-md border border-slate-200 bg-white';

            const minus = document.createElement('button');
            minus.type = 'button';
            minus.className = 'px-4 py-2 text-lg font-black text-slate-700';
            minus.textContent = '-';
            minus.addEventListener('click', () => changeQuantity(item.product_id, -1, minus));

            const count = document.createElement('span');
            count.className = 'min-w-10 px-3 text-center text-sm font-black text-slate-950';
            count.textContent = item.quantity;

            const plus = document.createElement('button');
            plus.type = 'button';
            plus.className = 'px-4 py-2 text-lg font-black text-slate-700';
            plus.textContent = '+';
            plus.addEventListener('click', () => changeQuantity(item.product_id, 1, plus));

            quantity.append(minus, count, plus);

            const unit = document.createElement('div');
            unit.className = 'text-sm font-bold text-slate-500';
            unit.textContent = money(item.unit_price) + ' each';
            controls.append(quantity, unit);

            row.append(media, info, controls);
            itemsEl.appendChild(row);
        });

        if (data.budget_warning) {
            warningEl.classList.remove('hidden');
            warningEl.textContent = 'Budget exceeded. Limit: ' + money(data.budget_limit);
        } else {
            warningEl.classList.add('hidden');
        }
    }

    async function loadCart() {
        try {
            const data = await window.apiFetch('/api/cart');
            renderCart(data);
        } catch (err) {
            const error = document.createElement('div');
            error.className = 'shop-card p-6 text-sm font-bold text-rose-700';
            error.textContent = err.message || err.toString();
            itemsEl.replaceChildren(error);
        }
    }

    loadCart();
</script>
@endsection
