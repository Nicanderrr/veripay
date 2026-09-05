@extends('layouts.customer')

@section('title', 'Create Account')

@section('content')
<div class="mx-auto grid min-h-[calc(100vh-190px)] w-full max-w-5xl items-center py-4 md:py-8">
    <div class="grid overflow-hidden rounded-md border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.08)] lg:grid-cols-[0.92fr_1.08fr]">
        <section class="hidden bg-[#202329] p-8 text-white lg:flex lg:flex-col lg:justify-between">
            <div>
                <div class="text-xs font-bold uppercase tracking-[0.16em] text-[#ff6863]">Join Veripay</div>
                <h1 class="mt-4 max-w-sm font-['Raleway'] text-4xl font-bold leading-tight">A smarter way to shop in-store.</h1>
                <p class="mt-4 max-w-sm text-sm font-semibold leading-6 text-white/70">Create an account to save carts, track budgets, scan product QR codes, and pay from your phone.</p>
            </div>
            <div class="mt-10 space-y-3 text-sm font-bold text-white">
                <div class="rounded-md border border-white/10 bg-white/10 p-4">QR code scan to cart</div>
                <div class="rounded-md border border-white/10 bg-white/10 p-4">Scan and pay in store</div>
                <div class="rounded-md border border-white/10 bg-white/10 p-4">Fast checkout simulation</div>
            </div>
        </section>

        <section class="p-6 sm:p-8 lg:p-10">
        <div class="text-xs font-bold uppercase tracking-[0.16em] text-[#ff6863]">Account</div>
        <h2 class="mt-2 font-['Raleway'] text-3xl font-bold leading-tight text-slate-950">Create account</h2>
        <p class="mt-2 text-sm font-semibold text-slate-500">Verify your email with OTP and start shopping.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-extrabold text-slate-700">Name</label>
                <input name="name" type="text" class="shop-input mt-2" required autocomplete="name">
            </div>
            <div>
                <label class="text-sm font-extrabold text-slate-700">Email</label>
                <input name="email" type="email" class="shop-input mt-2" required autocomplete="email">
            </div>
            <div>
                <label class="text-sm font-extrabold text-slate-700">Password</label>
                <input name="password" type="password" class="shop-input mt-2" required autocomplete="new-password">
            </div>
            <div>
                <label class="text-sm font-extrabold text-slate-700">Confirm Password</label>
                <input name="password_confirmation" type="password" class="shop-input mt-2" required autocomplete="new-password">
            </div>
            <button class="shop-btn shop-btn-primary w-full" type="submit">Create Account</button>
        </form>
        <p class="mt-5 text-center text-sm font-semibold text-slate-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-extrabold text-sky-800">Login</a>
        </p>
        </section>
    </div>
</div>
@endsection
