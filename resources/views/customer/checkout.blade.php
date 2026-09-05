@extends('layouts.customer')

@section('title', 'Checkout')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1fr_380px]">
    <section class="space-y-5">
        <div class="shop-card p-5">
            <div class="section-kicker">Checkout</div>
            <h1 class="section-title mt-1">Review and pay</h1>
            <p class="mt-2 text-sm font-semibold text-slate-500">A short checkout flow for in-store mobile payments.</p>
        </div>

        <div class="shop-card p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-950">Payment method</h2>
                <span class="rounded-md bg-emerald-50 px-3 py-2 text-xs font-extrabold text-emerald-700">Secure</span>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <label class="payment-option rounded-md border-2 border-sky-700 bg-sky-50 p-4">
                    <input type="radio" name="provider" value="card" class="sr-only" checked>
                    <div class="text-lg font-black text-slate-950">Card</div>
                    <div class="mt-1 text-sm font-semibold text-slate-500">Visa, Mastercard simulation</div>
                </label>
                <label class="payment-option rounded-md border-2 border-slate-200 bg-white p-4">
                    <input type="radio" name="provider" value="paystack" class="sr-only">
                    <div class="text-lg font-black text-slate-950">Mobile money</div>
                    <div class="mt-1 text-sm font-semibold text-slate-500">Paystack mock provider</div>
                </label>
            </div>
        </div>

        <div class="shop-card p-5">
            <h2 class="text-xl font-black text-slate-950">Order items</h2>
            <div id="checkout-items" class="mt-4 space-y-3"></div>
        </div>
    </section>

    <aside class="shop-card h-fit p-5 lg:sticky lg:top-28">
        <h2 class="text-xl font-black text-slate-950">Order summary</h2>
        <div class="mt-5 space-y-3 text-sm font-bold text-slate-600">
            <div class="flex justify-between"><span>Subtotal</span><span id="checkout-subtotal">GHS 0.00</span></div>
            <div class="flex justify-between"><span>Tax</span><span id="checkout-tax">GHS 0.00</span></div>
            <div class="border-t border-slate-200 pt-3 text-lg font-black text-slate-950">
                <div class="flex justify-between"><span>Total</span><span id="checkout-total">GHS 0.00</span></div>
            </div>
        </div>
        <button id="checkout-btn" class="shop-btn shop-btn-primary mt-5 w-full" type="button">Pay Now</button>
        <div id="checkout-status" class="mt-4 text-sm font-bold text-slate-600"></div>
    </aside>
</div>

<div class="mobile-action-bar fixed inset-x-0 z-[85] border-t border-slate-200 bg-white/95 p-3 shadow-[0_-18px_44px_rgba(15,23,42,0.12)] backdrop-blur lg:hidden">
    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Total</div>
            <div id="checkout-mobile-total" class="text-xl font-black text-slate-950">GHS 0.00</div>
        </div>
        <button id="checkout-mobile-btn" class="shop-btn shop-btn-primary min-w-[160px]" type="button">Pay Now</button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const itemsEl = document.getElementById('checkout-items');
    const subtotalEl = document.getElementById('checkout-subtotal');
    const taxEl = document.getElementById('checkout-tax');
    const totalEl = document.getElementById('checkout-total');
    const mobileTotalEl = document.getElementById('checkout-mobile-total');
    const statusEl = document.getElementById('checkout-status');
    const checkoutBtn = document.getElementById('checkout-btn');
    const checkoutMobileBtn = document.getElementById('checkout-mobile-btn');

    function money(value) {
        return 'GHS ' + Number(value || 0).toFixed(2);
    }

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

    function renderCart(data) {
        itemsEl.replaceChildren();
        subtotalEl.textContent = money(data.subtotal);
        taxEl.textContent = money(data.tax);
        totalEl.textContent = money(data.total);
        mobileTotalEl.textContent = money(data.total);

        if (!data.items || !data.items.length) {
            const empty = document.createElement('div');
            empty.className = 'rounded-md bg-slate-50 p-4 text-sm font-bold text-slate-500';
            empty.textContent = 'Your cart is empty.';
            itemsEl.appendChild(empty);
            return;
        }

        data.items.forEach(item => {
            const row = document.createElement('div');
            row.className = 'grid grid-cols-[58px_minmax(0,1fr)_auto] items-center gap-3 rounded-md bg-slate-50 p-3';

            const media = document.createElement('a');
            media.href = `/products/${item.product_id}`;
            media.className = `product-media ${mediaClass(item.category?.name)} h-[58px] w-[58px] rounded-md border border-slate-200`;

            const imageUrl = productImageUrl(item);
            if (imageUrl) {
                const image = document.createElement('img');
                image.src = imageUrl;
                image.alt = item.name || 'Product image';
                media.appendChild(image);
            } else {
                const glyph = document.createElement('div');
                glyph.className = 'product-glyph !h-10 !w-9 !rounded-lg !text-xs';
                glyph.textContent = initials(item.name);
                media.appendChild(glyph);
            }

            const details = document.createElement('div');
            const name = document.createElement('div');
            name.className = 'line-clamp-2 font-black leading-5 text-slate-950';
            name.textContent = item.name || 'Unknown item';
            const qty = document.createElement('div');
            qty.className = 'text-xs font-bold text-slate-500';
            qty.textContent = 'Qty ' + item.quantity;
            details.append(name, qty);
            const total = document.createElement('div');
            total.className = 'font-black text-slate-950';
            total.textContent = money(item.total);
            row.append(media, details, total);
            itemsEl.appendChild(row);
        });
    }

    function selectedProvider() {
        return document.querySelector('input[name="provider"]:checked')?.value || 'card';
    }

    function setLoading(isLoading) {
        checkoutBtn.disabled = isLoading;
        checkoutMobileBtn.disabled = isLoading;
        checkoutBtn.textContent = isLoading ? 'Processing...' : 'Pay Now';
        checkoutMobileBtn.textContent = isLoading ? 'Processing...' : 'Pay Now';
    }

    async function checkout() {
        setLoading(true);
        statusEl.textContent = '';
        try {
            const data = await window.apiFetch('/api/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ provider: selectedProvider() })
            });
            if (data.authorization_url) {
                statusEl.className = 'mt-4 rounded-md bg-sky-50 px-4 py-3 text-sm font-bold text-sky-800';
                statusEl.textContent = 'Redirecting to Paystack...';
                window.location.href = data.authorization_url;
                return;
            }
            statusEl.className = 'mt-4 rounded-md bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800';
            statusEl.textContent = data.message || 'Checkout complete. Your receipt has been emailed.';
            await window.updateCartBadge();
            setTimeout(() => window.location.href = '{{ route('home') }}', 1200);
        } catch (err) {
            statusEl.className = 'mt-4 rounded-md bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800';
            statusEl.textContent = err.message || err.toString();
        } finally {
            setLoading(false);
        }
    }

    document.querySelectorAll('.payment-option').forEach(label => {
        label.addEventListener('click', () => {
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('border-sky-700', 'bg-sky-50');
                option.classList.add('border-slate-200', 'bg-white');
            });
            label.classList.add('border-sky-700', 'bg-sky-50');
            label.classList.remove('border-slate-200', 'bg-white');
        });
    });

    checkoutBtn.addEventListener('click', checkout);
    checkoutMobileBtn.addEventListener('click', checkout);

    window.apiFetch('/api/cart')
        .then(renderCart)
        .catch(err => {
            statusEl.className = 'mt-4 rounded-md bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800';
            statusEl.textContent = err.message || err.toString();
        });
</script>
@endsection
