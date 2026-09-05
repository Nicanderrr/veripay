@extends('layouts.customer')

@section('title', 'Forgot Password')

@section('content')
<div class="mx-auto max-w-xl">
    <section class="shop-card p-6 sm:p-8">
        <div class="section-kicker">Account recovery</div>
        <h1 class="section-title mt-1">Reset your password</h1>
        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">
            Enter your account email and we will send a secure password reset link.
        </p>

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-extrabold text-slate-700">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" class="shop-input mt-2" required autocomplete="email" autofocus>
                @error('email')
                    <div class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</div>
                @enderror
            </div>
            <button class="shop-btn shop-btn-primary w-full" type="submit">Send Reset Link</button>
        </form>

        <p class="mt-5 text-center text-sm font-semibold text-slate-600">
            Remembered it?
            <a href="{{ route('login') }}" class="font-extrabold text-sky-800">Login</a>
        </p>
    </section>
</div>
@endsection
