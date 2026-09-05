@extends('layouts.customer')

@section('title', 'Login')

@section('content')
<div class="mx-auto grid min-h-[calc(100vh-190px)] w-full max-w-5xl items-center py-4 md:py-8">
    <div class="grid overflow-hidden rounded-md border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.08)] lg:grid-cols-[0.92fr_1.08fr]">
        <section class="hidden bg-[#202329] p-8 text-white lg:flex lg:flex-col lg:justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-[#ff6863]">Welcome back</div>
                <h1 class="mt-4 max-w-sm font-['Raleway'] text-4xl font-bold leading-tight">Shop faster with your live cart.</h1>
                <p class="mt-4 max-w-sm text-sm font-semibold leading-6 text-white/70">Sign in to sync your cart, scan product QR codes, and complete self checkout.</p>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-3">
                <div class="rounded-md border border-white/10 bg-white/10 p-4">
                    <div class="text-2xl font-black text-white">Pay</div>
                    <div class="mt-1 text-xs font-bold uppercase tracking-[0.12em] text-white/60">Self checkout</div>
                </div>
                <div class="rounded-md border border-white/10 bg-white/10 p-4">
                    <div class="text-2xl font-black text-white">Live</div>
                    <div class="mt-1 text-xs font-bold uppercase tracking-[0.12em] text-white/60">Cart sync</div>
                </div>
            </div>
        </section>

        <section class="p-6 sm:p-8 lg:p-10">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-[#ff6863]">Account</div>
        <h2 class="mt-2 font-['Raleway'] text-3xl font-bold leading-tight text-slate-950">Sign in</h2>
        <p class="mt-2 text-sm font-semibold text-slate-500">Access your cart, history, and checkout tools.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-extrabold text-slate-700">Email</label>
                <input name="email" type="email" class="shop-input mt-2" required autocomplete="email">
                @error('email')
                    <div class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label class="text-sm font-extrabold text-slate-700">Password</label>
                <input name="password" type="password" class="shop-input mt-2" required autocomplete="current-password">
            </div>
            <div class="text-right">
                <a href="{{ route('password.request') }}" class="text-sm font-extrabold text-sky-800">Forgot password?</a>
            </div>
            <button class="shop-btn shop-btn-primary w-full" type="submit">Login</button>
        </form>
        <p class="mt-5 text-center text-sm font-semibold text-slate-600">
            No account?
            <a href="{{ route('register', request('redirect') ? ['redirect' => request('redirect')] : []) }}" class="font-extrabold text-sky-800">Create one</a>
        </p>
        </section>
    </div>
</div>
@endsection
