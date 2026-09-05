@extends('layouts.admin')

@section('page_title', 'Customers')

@section('content')
<div class="card overflow-x-auto">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Orders</th>
                <th>Total Purchases</th>
                <th>Reset Password</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td class="font-semibold text-slate-900">{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->orders_count }}</td>
                    <td>GHS {{ number_format((float) ($customer->orders_sum_total ?? 0), 2) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.customers.password', $customer) }}" class="grid gap-2 md:grid-cols-[1fr_1fr_auto]" data-confirm="Reset this customer's password?">
                            @csrf
                            <input type="password" name="password" class="field" placeholder="New password" required minlength="8">
                            <input type="password" name="password_confirmation" class="field" placeholder="Confirm password" required minlength="8">
                            <button type="submit" class="btn-primary whitespace-nowrap">Update</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-slate-500">No customers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
