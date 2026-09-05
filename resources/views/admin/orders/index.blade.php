@extends('layouts.admin')

@section('page_title', 'Orders')

@section('content')
<div class="card p-4">
    <form method="GET" class="flex flex-col gap-3 md:flex-row md:items-center">
        <input class="field max-w-xs" type="date" name="date" value="{{ request('date') }}">
        <button class="btn-outline" type="submit">Filter</button>
        <a href="{{ route('admin.orders.index') }}" class="btn-outline">Reset</a>
    </form>
</div>

<div class="card mt-4 overflow-x-auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th>Receipt</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                @php $st = strtolower((string) $order->status); @endphp
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user?->name ?? 'Guest' }}</td>
                    <td>GHS {{ number_format($order->total, 2) }}</td>
                    <td>
                        <span class="status-pill {{ in_array($st, ['complete', 'completed', 'fulfilled'], true) ? 'status-ok' : (str_contains($st, 'pending') ? 'status-warn' : 'status-neutral') }}">{{ $order->status ?? 'N/A' }}</span>
                    </td>
                    <td>
                        @if($order->receipt_token)
                            <span class="text-xs font-bold text-slate-600">{{ $order->receipt_token }}</span>
                        @else
                            <span class="text-xs font-semibold text-slate-400">No receipt</span>
                        @endif
                    </td>
                    <td>{{ $order->created_at?->format('M d, Y H:i') }}</td>
                    <td>
                        <div class="flex flex-wrap items-center gap-2">
                            <a class="btn-outline" href="{{ route('admin.orders.show', $order) }}">View Order</a>
                            @if($order->receipt_token)
                                <a class="btn-outline" href="{{ route('security.receipts.show', $order->receipt_token) }}">View Receipt</a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-slate-500">No orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $orders->withQueryString()->links() }}</div>
@endsection
