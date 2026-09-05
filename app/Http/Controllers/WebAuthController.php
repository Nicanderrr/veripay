<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class WebAuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $redirect = $request->string('redirect')->toString();
        if (str_starts_with($redirect, '/')) {
            $request->session()->put('url.intended', url($redirect));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = $request->user();

            if ($user && $user->role === User::ROLE_CUSTOMER && ! $user->email_verified_at) {
                Auth::logout();
                $request->session()->put('pending_verification_user_id', $user->id);
                $this->sendVerificationOtp($user);

                return redirect()
                    ->route('verification.otp.notice')
                    ->with('status', 'Verify your email before continuing. We sent you a new OTP.');
            }

            if ($user && $user->role === User::ROLE_ADMIN) {
                return redirect()->intended('/admin');
            }

            if ($user && $user->role === User::ROLE_STAFF) {
                return redirect()->intended('/security');
            }

            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showRegister(Request $request)
    {
        $redirect = $request->string('redirect')->toString();
        if (str_starts_with($redirect, '/')) {
            $request->session()->put('url.intended', url($redirect));
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_CUSTOMER,
        ]);

        $request->session()->put('pending_verification_user_id', $user->id);
        $this->sendVerificationOtp($user);

        return redirect()
            ->route('verification.otp.notice')
            ->with('status', 'Account created. Enter the OTP we sent to your email.');
    }

    public function showOtpVerification(Request $request)
    {
        $user = $this->pendingVerificationUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->email_verified_at) {
            Auth::login($user);

            return redirect()->intended('/');
        }

        return view('auth.verify-otp', [
            'email' => $user->email,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $this->pendingVerificationUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $otp = DB::table('email_verification_otps')
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (! $otp || $otp->attempts >= 5 || ! Hash::check($validated['code'], $otp->code_hash)) {
            if ($otp) {
                DB::table('email_verification_otps')
                    ->where('id', $otp->id)
                    ->update(['attempts' => $otp->attempts + 1]);
            }

            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        DB::transaction(function () use ($user, $otp) {
            DB::table('email_verification_otps')
                ->where('id', $otp->id)
                ->update(['used_at' => now()]);

            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();
        });

        $request->session()->forget('pending_verification_user_id');
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'Email verified. You are signed in.');
    }

    public function resendOtp(Request $request)
    {
        $user = $this->pendingVerificationUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->email_verified_at) {
            Auth::login($user);

            return redirect()->intended('/');
        }

        $this->sendVerificationOtp($user);

        return back()->with('status', 'A new OTP has been sent.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function pendingVerificationUser(Request $request): ?User
    {
        $userId = $request->session()->get('pending_verification_user_id');

        return $userId ? User::find($userId) : null;
    }

    private function sendVerificationOtp(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        DB::table('email_verification_otps')
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        DB::table('email_verification_otps')->insert([
            'user_id' => $user->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Mail::to($user)->send(new EmailVerificationOtpMail($user, $code));
    }
}
