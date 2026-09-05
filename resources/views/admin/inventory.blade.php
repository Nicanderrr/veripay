@extends('layouts.admin')

@section('page_title', 'Inventory')

@section('content')
<div class="card overflow-x-auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
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
                    $statusText = $stock === 0 ? 'Out of Stock' : ($stock <= 10 ? 'Low Stock' : 'Healthy');
                @endphp
                <tr>
                    <td class="font-semibold text-slate-900">{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
                    <td>{{ $stock }}</td>
                    <td><span class="status-pill {{ $statusClass }}">{{ $statusText }}</span></td>
                    <td><a class="btn-outline" href="{{ route('admin.products.edit', $product) }}">Update Stock</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-slate-500">No inventory records.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $products->links() }}</div>

<div class="card mt-5 p-5">
    <h2 class="mb-3 text-base font-extrabold tracking-tight">Recent Stock Actions</h2>
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Product</th>
                    <th>User</th>
                    <th>Change</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr>
                        <td>{{ $log->created_at?->format('M d, Y H:i') }}</td>
                        <td>{{ $log->product?->name ?? 'N/A' }}</td>
                        <td>{{ $log->user?->name ?? 'System' }}</td>
                        <td>{{ $log->change_amount ?? $log->quantity_change ?? '-' }}</td>
                        <td>{{ $log->reason ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-slate-500">No recent inventory changes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
