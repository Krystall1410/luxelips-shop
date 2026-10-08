<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'to_district_id',
        'to_ward_code',
        'subtotal',
        'ghn_total_fee',
        'total_price',
        'payment_method',
        'status',
        'shipping_status',
        'ghn_order_code',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = static::generateUniqueOrderCode();
            }
        });
    }

    public static function generateUniqueOrderCode(): string
    {
        do {
            $code = strtoupper(\Illuminate\Support\Str::random(8));
        } while (static::where('order_code', $code)->exists());

        return $code;
    }

    protected $casts = [
        'subtotal' => 'decimal:2',
        'ghn_total_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
        'to_district_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
