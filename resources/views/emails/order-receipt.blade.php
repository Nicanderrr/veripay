<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Veripay Receipt #{{ $order->id }}</title>
</head>
<body style="margin:0;background:#f6f6f6;font-family:Arial,sans-serif;color:#1f2933;">
    <div style="max-width:680px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border-top:5px solid #ff6863;padding:28px;">
            <h1 style="margin:0;font-size:28px;">Receipt #{{ $order->id }}</h1>
            <p style="margin:8px 0 0;color:#68717d;">Show this QR code to security before leaving the store.</p>

            <div style="margin:24px 0;text-align:center;">
                <img src="{{ $message->embedData($qrBinary, $qrFilename, 'image/png') }}" alt="Receipt verification QR code" width="220" height="220" style="display:block;margin:0 auto;border:8px solid #ffffff;box-shadow:0 10px 24px rgba(32,35,41,0.12);">
                <p style="margin:12px 0 0;font-size:12px;color:#68717d;word-break:break-all;">{{ $verifyUrl }}</p>
            </div>

            <table style="width:100%;border-collapse:collapse;margin-top:20px;">
                <thead>
                    <tr>
                        <th align="left" style="padding:10px;border-bottom:1px solid #e8e8e8;">Product</th>
                        <th align="center" style="padding:10px;border-bottom:1px solid #e8e8e8;">Qty</th>
                        <th align="right" style="padding:10px;border-bottom:1px solid #e8e8e8;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #f0f0f0;">{{ $item->product?->name ?? 'Product' }}</td>
                            <td align="center" style="padding:10px;border-bottom:1px solid #f0f0f0;">{{ $item->quantity }}</td>
                            <td align="right" style="padding:10px;border-bottom:1px solid #f0f0f0;">GHS {{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="text-align:right;font-size:20px;font-weight:bold;margin:18px 0 0;">Total: GHS {{ number_format($order->total, 2) }}</p>
            <p style="margin:16px 0 0;color:#68717d;">Payment: {{ strtoupper($order->payment?->provider ?? 'N/A') }} / {{ strtoupper($order->payment?->status ?? 'N/A') }}</p>
        </div>
    </div>
</body>
</html>
