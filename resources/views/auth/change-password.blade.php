@php
    $usesAdminLayout = auth()->check() && in_array(auth()->user()->role, ['admin', 'staff'], true);
@endphp

@extends($usesAdminLayout ? 'layouts.admin' : 'layouts.customer')

@section('title', 'Change Password')
@section('page_title', 'Change Password')

@section('content')
<div class="{{ $usesAdminLayout ? 'max-w-2xl' : 'mx-auto max-w-xl' }}">
    <section class="{{ $usesAdminLayout ? 'card p-6' : 'shop-card p-6 sm:p-8' }}">
        <div class="{{ $usesAdminLayout ? 'text-xs font-bold uppercase tracking-[0.14em] text-slate-500' : 'section-kicker' }}">Account security</div>
        <h1 class="{{ $usesAdminLayout ? 'mt-1 text-2xl font-extrabold text-slate-950' : 'section-title mt-1' }}">Change password</h1>
        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">
            Enter your current password and choose a new password for your account.
        </p>

        <form method="POST" action="{{ route('password.change.update') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-extrabold text-slate-700">Current password</label>
                <input name="current_password" type="password" class="{{ $usesAdminLayout ? 'field mt-2' : 'shop-input mt-2' }}" required autocomplete="current-password">
                @error('current_password')
                    <div class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-700">New password</label>
                <input name="password" type="password" class="{{ $usesAdminLayout ? 'field mt-2' : 'shop-input mt-2' }}" required autocomplete="new-password">
                @error('password')
                    <div class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-700">Confirm new password</label>
                <input name="password_confirmation" type="password" class="{{ $usesAdminLayout ? 'field mt-2' : 'shop-input mt-2' }}" required autocomplete="new-password">
            </div>

            <button class="{{ $usesAdminLayout ? 'btn-primary w-full' : 'shop-btn shop-btn-primary w-full' }}" type="submit">Update Password</button>
        </form>
    </section>
</div>
@endsection
