@extends('layouts.admin')

@section('page_title', 'Receipt Verification')

@section('content')
@if(!$order)
    <div class="card p-6">
        <span class="status-pill status-danger">Invalid</span>
        <h1 class="mt-4 text-2xl font-extrabold">Receipt not found</h1>
        <p class="mt-2 text-sm font-semibold text-slate-500">Token: {{ $token }}</p>
        <a href="{{ route('security.index') }}" class="btn-outline mt-5">Scan Again</a>
    </div>
@else
    <div class="grid gap-4 xl:grid-cols-[1fr_360px]">
        <section class="card p-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Receipt #{{ $order->id }}</p>
                    <h1 class="mt-1 text-2xl font-extrabold">{{ $order->user?->name ?? 'Customer' }}</h1>
                    <p class="mt-1 text-sm font-semibold text-slate-500">{{ $order->user?->email }}</p>
                </div>
                @if($order->receipt_verified_at)
                    <span class="status-pill status-ok">Already verified</span>
                @elseif($order->payment_status === 'paid')
                    <span class="status-pill status-ok">Paid</span>
                @else
                    <span class="status-pill status-danger">Not paid</span>
                @endif
            </div>

            <div class="mt-5 overflow-x-auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>QR Code</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product?->name ?? 'Product' }}</td>
                                <td>{{ $item->product?->barcode ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>GHS {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="card p-5">
            <h2 class="text-xl font-extrabold">Verification</h2>
            <div class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                <div class="flex justify-between"><span>Total</span><strong>GHS {{ number_format($order->total, 2) }}</strong></div>
                <div class="flex justify-between"><span>Payment</span><strong>{{ strtoupper($order->payment_status) }}</strong></div>
                <div class="flex justify-between"><span>Provider</span><strong>{{ strtoupper($order->payment?->provider ?? 'N/A') }}</strong></div>
                <div class="flex justify-between"><span>Reference</span><strong>{{ $order->payment?->reference ?? 'N/A' }}</strong></div>
            </div>

            @if($order->receipt_verified_at)
                <div class="mt-5 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                    Verified {{ $order->receipt_verified_at->format('M d, Y H:i') }} by {{ $order->receiptVerifier?->name ?? 'Security' }}.
                </div>
            @elseif($order->payment_status === 'paid')
                <form method="POST" action="{{ route('security.receipts.verify', $order->receipt_token) }}" class="mt-5">
                    @csrf
                    <button class="btn-primary w-full" type="submit">Mark Receipt Verified</button>
                </form>
            @else
                <div class="mt-5 rounded-lg bg-rose-50 px-4 py-3 text-sm font-bold text-rose-800">
                    Do not approve exit. This order is not paid.
                </div>
            @endif

            <a href="{{ route('security.index') }}" class="btn-outline mt-3 w-full">Scan Another Receipt</a>
            @if(auth()->user()?->role === \App\Models\User::ROLE_ADMIN)
                <a href="{{ route('admin.orders.show', $order) }}" class="btn-outline mt-3 w-full">View Customer Order</a>
            @endif
        </aside>
    </div>
@endif
@endsection
