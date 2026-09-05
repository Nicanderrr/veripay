@extends('layouts.customer')

@section('title', 'Reset Password')

@section('content')
<div class="mx-auto max-w-xl">
    <section class="shop-card p-6 sm:p-8">
        <div class="section-kicker">Account recovery</div>
        <h1 class="section-title mt-1">Choose a new password</h1>

        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="text-sm font-extrabold text-slate-700">Email</label>
                <input name="email" type="email" value="{{ old('email', $email) }}" class="shop-input mt-2" required autocomplete="email" autofocus>
                @error('email')
                    <div class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-700">New password</label>
                <input name="password" type="password" class="shop-input mt-2" required autocomplete="new-password">
                @error('password')
                    <div class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-extrabold text-slate-700">Confirm password</label>
                <input name="password_confirmation" type="password" class="shop-input mt-2" required autocomplete="new-password">
            </div>

            <button class="shop-btn shop-btn-primary w-full" type="submit">Reset Password</button>
        </form>
    </section>
</div>
@endsection
