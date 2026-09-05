<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class SecurityPortalController extends Controller
{
    public function index()
    {
        $this->authorizeSecurityUser();

        return view('security.index');
    }

    public function lookup(Request $request)
    {
        $this->authorizeSecurityUser();

        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $token = trim($validated['token']);
        if (str_starts_with($token, 'http')) {
            $token = basename(parse_url($token, PHP_URL_PATH) ?: $token);
        }

        return redirect()->route('security.receipts.show', $token);
    }

    public function show(string $token)
    {
        $this->authorizeSecurityUser();

        $order = Order::with(['items.product', 'payment', 'user', 'receiptVerifier'])
            ->where('receipt_token', $token)
            ->first();

        return view('security.show', [
            'order' => $order,
            'token' => $token,
        ]);
    }

    public function verify(Request $request, string $token)
    {
        $this->authorizeSecurityUser();

        $order = Order::where('receipt_token', $token)->firstOrFail();

        if (! $order->receipt_verified_at) {
            $order->update([
                'receipt_verified_at' => now(),
                'receipt_verified_by' => $request->user()->id,
            ]);
        }

        return redirect()->route('security.receipts.show', $token)->with('success', 'Receipt verified.');
    }

    private function authorizeSecurityUser(): void
    {
        $user = request()->user();

        abort_unless($user && in_array($user->role, ['admin', 'staff'], true), 403);
    }
}
