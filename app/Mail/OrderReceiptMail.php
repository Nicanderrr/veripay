<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Milon\Barcode\DNS2D;

class OrderReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing(['items.product', 'payment', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Veripay receipt #' . $this->order->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-receipt',
            with: [
                'order' => $this->order,
                'qrBinary' => base64_decode($this->qrPngBase64()),
                'qrFilename' => 'receipt-' . $this->order->id . '-qr.png',
                'verifyUrl' => route('security.receipts.show', $this->order->receipt_token),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function qrPngBase64(): string
    {
        return (new DNS2D())->getBarcodePNG(route('security.receipts.show', $this->order->receipt_token), 'QRCODE', 8, 8);
    }
}
