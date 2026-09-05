@extends('layouts.customer')

@section('title', 'Verify Email')

@section('content')
<div class="mx-auto max-w-xl">
    <section class="shop-card p-6 sm:p-8">
        <div class="section-kicker">Email verification</div>
        <h1 class="section-title mt-1">Enter your OTP</h1>
        <p class="mt-3 text-sm font-semibold leading-6 text-slate-600">
            We sent a 6-digit code to <strong>{{ $email }}</strong>. Verify it to activate your account.
        </p>

        @if(session('status'))
            <div class="mt-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.otp.verify') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="text-sm font-extrabold text-slate-700">Verification code</label>
                <input name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" class="shop-input mt-2 text-center text-2xl font-black tracking-[0.32em]" required autofocus>
                @error('code')
                    <div class="mt-2 text-sm font-bold text-rose-600">{{ $message }}</div>
                @enderror
            </div>
            <button class="shop-btn shop-btn-primary w-full" type="submit">Verify Account</button>
        </form>

        <form method="POST" action="{{ route('verification.otp.resend') }}" class="mt-4">
            @csrf
            <button class="shop-btn shop-btn-outline w-full" type="submit">Resend Code</button>
        </form>
    </section>
</div>
@endsection
