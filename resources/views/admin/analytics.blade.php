@extends('layouts.admin')

@section('page_title', 'Analytics')

@section('content')
<div class="grid gap-4 xl:grid-cols-3">
    <section class="card chart-card p-5 xl:col-span-2">
        <div class="chart-card-header mb-4 flex items-center justify-between">
            <h2 class="text-base font-extrabold tracking-tight">Sales Trend (30 Days)</h2>
            <span class="text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Revenue</span>
        </div>
        <div class="chart-frame compact"><canvas id="analytics-sales"></canvas></div>
    </section>

    <section class="card p-5">
        <h2 class="mb-4 text-base font-extrabold tracking-tight">Event Types</h2>
        <div class="space-y-2">
            @forelse($eventTypes as $event)
                <div class="flex items-center justify-between rounded-xl border border-slate-200 p-3">
                    <span class="text-sm font-semibold text-slate-900">{{ $event->event_type }}</span>
                    <span class="status-pill status-neutral">{{ $event->count }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-500">No analytics events yet.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="card mt-5 p-5">
    <h2 class="mb-4 text-base font-extrabold tracking-tight">Top Viewed Products</h2>
    <div class="overflow-x-auto">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Views</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topViewedProducts as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? 'Unknown Product' }}</td>
                        <td>{{ $item->views }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-slate-500">No product view data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('analytics-sales');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($salesLabels),
                datasets: [{
                    label: 'Revenue',
                    data: @json($salesValues),
                    backgroundColor: 'rgba(255,104,99,0.86)',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                resizeDelay: 120,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: '#e2e8f0' } } }
            }
        });
    }
</script>
@endsection
