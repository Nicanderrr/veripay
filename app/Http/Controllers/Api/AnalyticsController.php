<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:100',
            'product_id' => 'nullable|exists:products,id',
            'payload' => 'nullable|array',
        ]);

        $event = AnalyticsEvent::create([
            'user_id' => $request->user()?->id,
            'event_type' => $validated['event_type'],
            'product_id' => $validated['product_id'] ?? null,
            'payload' => $validated['payload'] ?? null,
        ]);

        return response()->json(['status' => 'ok', 'id' => $event->id]);
    }
}
