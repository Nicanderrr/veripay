@extends('layouts.customer')

@section('title', 'Order History')

@section('content')
<div class="space-y-6">
    <div class="shop-card p-5">
        <div class="section-kicker">History</div>
        <h1 class="section-title mt-1">Your orders</h1>
        <p class="mt-2 text-sm font-semibold text-slate-500">Review what you bought, when you bought it, and the payment status for each order.</p>
    </div>

    @forelse($orders as $order)
        <article class="shop-card overflow-hidden">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 p-5">
                <div>
                    <div class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Order #{{ $order->id }}</div>
                    <div class="mt-1 text-lg font-black text-slate-950">{{ $order->created_at?->format('M d, Y H:i') }}</div>
                    <div class="mt-1 text-sm font-semibold text-slate-500">
                        {{ $order->items->count() }} item{{ $order->items->count() === 1 ? '' : 's' }}
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $status = strtolower((string) $order->status);
                        $payment = strtolower((string) $order->payment_status);
                    @endphp
                    <span class="status-pill {{ str_contains($status, 'complete') || $status === 'placed' ? 'status-ok' : (str_contains($status, 'pending') ? 'status-warn' : 'status-neutral') }}">{{ $order->status ?? 'N/A' }}</span>
                    <span class="status-pill {{ $payment === 'paid' ? 'status-ok' : ($payment === 'failed' ? 'status-danger' : 'status-warn') }}">{{ $order->payment_status ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="grid gap-4 p-5 lg:grid-cols-[1fr_260px]">
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between gap-4 rounded-md border border-slate-200 bg-slate-50 p-3">
                            <div>
                                <div class="font-extrabold text-slate-950">{{ $item->product?->name ?? 'Product' }}</div>
                                <div class="text-xs font-semibold text-slate-500">Qty {{ $item->quantity }} | QR {{ $item->product?->barcode ?? 'N/A' }}</div>
                            </div>
                            <div class="text-sm font-black text-slate-950">GHS {{ number_format($item->total, 2) }}</div>
                        </div>
                    @endforeach
                </div>

                <aside class="rounded-md border border-slate-200 bg-[#fff8f8] p-4">
                    <div class="space-y-2 text-sm font-bold text-slate-600">
                        <div class="flex justify-between"><span>Subtotal</span><strong>GHS {{ number_format($order->items->sum('total'), 2) }}</strong></div>
                        <div class="flex justify-between"><span>Payment</span><strong>{{ strtoupper($order->payment?->provider ?? 'N/A') }}</strong></div>
                        <div class="flex justify-between"><span>Status</span><strong>{{ strtoupper($order->payment_status) }}</strong></div>
                    </div>

                    @if($order->receipt_token)
                        <div class="mt-4 text-xs font-semibold text-slate-500">
                            Receipt ready for security verification.
                        </div>
                    @endif
                </aside>
            </div>
        </article>
    @empty
        <div class="shop-card p-8 text-center">
            <div class="text-xl font-black text-slate-950">No orders yet</div>
            <p class="mt-2 text-sm font-semibold text-slate-500">Once you check out, your completed orders will appear here.</p>
            <a href="{{ route('shop') }}" class="shop-btn shop-btn-primary mt-5">Start Shopping</a>
        </div>
    @endforelse

    <div>{{ $orders->links() }}</div>
</div>
@endsection
