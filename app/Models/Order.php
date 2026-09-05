<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cart_id',
        'total',
        'status',
        'payment_status',
        'receipt_token',
        'receipt_sent_at',
        'receipt_verified_at',
        'receipt_verified_by',
        'fulfilled_at',
        'placed_at',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'placed_at' => 'datetime',
        'receipt_sent_at' => 'datetime',
        'receipt_verified_at' => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function receiptVerifier()
    {
        return $this->belongsTo(User::class, 'receipt_verified_by');
    }
}
