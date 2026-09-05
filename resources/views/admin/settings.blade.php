@extends('layouts.admin')

@section('page_title', 'Settings')

@section('content')
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <article class="card kpi-card">
        <div class="label">Products</div>
        <div class="value">{{ number_format($stats['products']) }}</div>
    </article>
    <article class="card kpi-card">
        <div class="label">Orders</div>
        <div class="value">{{ number_format($stats['orders']) }}</div>
    </article>
    <article class="card kpi-card">
        <div class="label">Categories</div>
        <div class="value">{{ number_format($stats['categories']) }}</div>
    </article>
    <article class="card kpi-card">
        <div class="label">Order Items</div>
        <div class="value">{{ number_format($stats['order_items']) }}</div>
    </article>
</div>

<div class="card mt-5 p-5">
    <h2 class="text-base font-extrabold tracking-tight">System Preferences</h2>
    <p class="mt-2 text-sm text-slate-500">Quick links to live administrative tools used before deployment.</p>
    <div class="mt-4 grid gap-3 md:grid-cols-2">
        <a class="btn-outline" href="{{ route('admin.inventory') }}">Manage Notifications</a>
        <a class="btn-outline" href="{{ route('admin.products.index') }}">Manage Integrations</a>
        <a class="btn-outline" href="{{ route('admin.orders.index') }}">Export Reports</a>
        <a class="btn-outline" href="{{ route('admin.analytics') }}">Audit Log</a>
    </div>
</div>
@endsection
