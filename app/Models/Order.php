<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'total',
        'status',
        'payment_method',
        'amount_paid',
        'change_amount',
        'points_earned',
        'points_redeemed',
        'points_discount_amount',
        'cashier_name',
        'source',
        'notes',
        'whatsapp_reference',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'points_earned' => 'integer',
            'points_redeemed' => 'integer',
            'points_discount_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPos(): bool
    {
        return $this->source === 'pos';
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->subtotal ?? $this->total), 0, ',', '.');
    }

    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->discount_amount, 0, ',', '.');
    }

    public function getFormattedAmountPaidAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->amount_paid ?? $this->total), 0, ',', '.');
    }

    public function getFormattedChangeAttribute(): string
    {
        return 'Rp ' . number_format((float) ($this->change_amount ?? 0), 0, ',', '.');
    }

    public function getPaymentLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Tunai (Cash)',
            'qris' => 'QRIS Instan',
            'transfer' => 'Transfer Bank',
            'whatsapp' => 'WhatsApp Concierge',
            default => ucfirst($this->payment_method ?? 'WhatsApp'),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'processed' => 'Sedang Diproses',
            'completed' => 'Selesai / Terbayar',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order): void {
            if (empty($order->invoice_number)) {
                $prefix = $order->source === 'pos' ? 'POS' : 'INV';
                $order->invoice_number = $prefix . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
            }
        });
    }
}
