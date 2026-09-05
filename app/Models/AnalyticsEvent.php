<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'product_id',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
