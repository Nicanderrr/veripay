@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <article class="card kpi-card">
        <div class="label">Total Revenue</div>
        <div class="value">GHS {{ number_format($totalSales, 2) }}</div>
        <div class="trend {{ $salesTrend >= 0 ? 'up' : 'down' }}">{{ $salesTrend >= 0 ? '+' : '' }}{{ $salesTrend }}% vs previous period</div>
    </article>
    <article class="card kpi-card">
        <div class="label">Total Orders</div>
        <div class="value">{{ number_format($ordersCount) }}</div>
        <div class="trend {{ $ordersTrend >= 0 ? 'up' : 'down' }}">{{ $ordersTrend >= 0 ? '+' : '' }}{{ $ordersTrend }}% vs yesterday</div>
    </article>
    <article class="card kpi-card">
        <div class="label">Active Users</div>
        <div class="value">{{ number_format($activeUsers) }}</div>
        <div class="trend up">Live customer accounts</div>
    </article>
    <article class="card kpi-card">
        <div class="label">Conversion Rate</div>
        <div class="value">{{ number_format($conversionRate, 1) }}%</div>
        <div class="trend {{ $inventoryHealth >= 85 ? 'up' : 'down' }}">Inventory health: {{ $inventoryHealth }}%</div>
    </article>
</div>

<div class="mt-5 grid gap-4 xl:grid-cols-3">
    <section class="card chart-card p-5 xl:col-span-2">
        <div class="chart-card-header mb-4 flex items-center justify-between">
            <h2 class="text-base font-extrabold tracking-tight">Sales Over Last 14 Days</h2>
            <span class="text-xs font-bold uppercase tracking-[0.14em] text-slate-500">Revenue + Orders</span>
        </div>
        <div class="chart-frame"><canvas id="sales-chart"></canvas></div>
    </section>

    <section class="card p-5">
        <h2 class="mb-4 text-base font-extrabold tracking-tight">Top Products</h2>
        <div class="space-y-3">
            @forelse($topProducts as $product)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <div class="font-semibold text-slate-900">{{ $product->name }}</div>
                    <div class="mt-1 text-xs font-semibold uppercase tracking-[0.12em] text-slate-500">Stock {{ $product->stock_quantity }}</div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No sales data yet.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="mt-5 grid gap-4 xl:grid-cols-3">
    <section class="card p-5 xl:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-base font-extrabold tracking-tight">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-sky-700">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user?->name ?? 'Guest' }}</td>
                            <td>GHS {{ number_format($order->total, 2) }}</td>
                            <td>
                                @php $st = strtolower((string) $order->status); @endphp
                                <span class="status-pill {{ str_contains($st, 'complete') ? 'status-ok' : (str_contains($st, 'pending') ? 'status-warn' : 'status-neutral') }}">{{ $order->status ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $order->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-slate-500">No recent orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card p-5">
        <h2 class="mb-4 text-base font-extrabold tracking-tight">Quick Insights</h2>
        <div class="space-y-3">
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                <div class="text-xs font-bold uppercase tracking-[0.12em] text-amber-700">Low Stock Alerts</div>
                <div class="mt-1 text-sm font-semibold text-amber-900">{{ $lowStockProducts->count() }} products need replenishment</div>
            </div>
            <div class="space-y-2">
                @forelse($lowStockProducts->take(5) as $product)
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 p-3 text-sm">
                        <span class="font-semibold text-slate-900">{{ $product->name }}</span>
                        <span class="status-pill {{ $product->stock_quantity === 0 ? 'status-danger' : 'status-warn' }}">{{ $product->stock_quantity }} left</span>
                    </div>
                @empty
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-semibold text-emerald-800">Stock levels are healthy.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = @json($chartLabels);
    const sales = @json($salesSeries);
    const orders = @json($ordersSeries);

    const chart = document.getElementById('sales-chart');
    if (chart) {
        new Chart(chart, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Sales',
                        data: sales,
                        yAxisID: 'y',
                        borderColor: '#ff6863',
                        backgroundColor: 'rgba(255,104,99,0.14)',
                        tension: 0.35,
                        fill: true,
                        pointRadius: 2
                    },
                    {
                        label: 'Orders',
                        data: orders,
                        yAxisID: 'y1',
                        borderColor: '#16835b',
                        backgroundColor: 'rgba(22,131,91,0.12)',
                        tension: 0.35,
                        fill: false,
                        pointRadius: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 120,
                plugins: { legend: { position: 'bottom' } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e2e8f0' } },
                    y1: { beginAtZero: true, position: 'right', grid: { display: false } }
                }
            }
        });
    }
</script>
@endsection
