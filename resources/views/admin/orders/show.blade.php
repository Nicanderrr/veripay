@extends('layouts.admin')

@section('page_title', 'Order #' . $order->id)

@section('content')
<div class="grid gap-4 xl:grid-cols-[1fr_380px]">
    <section class="card p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Customer Order</p>
                <h1 class="mt-1 text-2xl font-extrabold">Order #{{ $order->id }}</h1>
                <p class="mt-1 text-sm font-semibold text-slate-500">
                    {{ $order->created_at?->format('M d, Y H:i') }} · {{ $order->user?->name ?? 'Guest' }}
                </p>
            </div>
            @php $status = strtolower((string) $order->status); @endphp
            <span class="status-pill {{ in_array($status, ['complete', 'completed', 'fulfilled'], true) ? 'status-ok' : (str_contains($status, 'pending') ? 'status-warn' : 'status-neutral') }}">
                {{ $order->status ?? 'N/A' }}
            </span>
        </div>

        <div class="mt-5 overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>QR Code</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr>
                            <td class="font-semibold text-slate-900">{{ $item->product?->name ?? 'Product removed' }}</td>
                            <td>{{ $item->product?->category?->name ?? 'Uncategorized' }}</td>
                            <td>{{ $item->product?->barcode ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>GHS {{ number_format($item->unit_price, 2) }}</td>
                            <td>GHS {{ number_format($item->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-slate-500">No order items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <aside class="space-y-4">
        <section class="card p-5">
            <h2 class="text-xl font-extrabold">Customer</h2>
            <div class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                <div class="flex justify-between gap-4"><span>Name</span><strong class="text-right">{{ $order->user?->name ?? 'Guest' }}</strong></div>
                <div class="flex justify-between gap-4"><span>Email</span><strong class="text-right">{{ $order->user?->email ?? 'N/A' }}</strong></div>
            </div>
        </section>

        <section class="card p-5">
            <h2 class="text-xl font-extrabold">Receipt & Payment</h2>
            <div class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                <div class="flex justify-between gap-4"><span>Total</span><strong>GHS {{ number_format($order->total, 2) }}</strong></div>
                <div class="flex justify-between gap-4"><span>Payment</span><strong>{{ strtoupper($order->payment_status ?? 'N/A') }}</strong></div>
                <div class="flex justify-between gap-4"><span>Provider</span><strong>{{ strtoupper($order->payment?->provider ?? 'N/A') }}</strong></div>
                <div class="flex justify-between gap-4"><span>Reference</span><strong class="text-right">{{ $order->payment?->reference ?? 'N/A' }}</strong></div>
                <div class="flex justify-between gap-4"><span>Receipt Token</span><strong class="text-right">{{ $order->receipt_token ?? 'N/A' }}</strong></div>
                <div class="flex justify-between gap-4"><span>Verified</span><strong>{{ $order->receipt_verified_at?->format('M d, Y H:i') ?? 'Not verified' }}</strong></div>
            </div>

            <div class="mt-5 grid gap-2">
                @if($order->receipt_token)
                    <a class="btn-primary w-full" href="{{ route('security.receipts.show', $order->receipt_token) }}">Open Receipt Verification</a>
                @endif
                <a class="btn-outline w-full" href="{{ route('admin.orders.index') }}">Back to Orders</a>
            </div>
        </section>
    </aside>
</div>
@endsection
