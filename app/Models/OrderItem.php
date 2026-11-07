<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'variant_size',
        'variant_color',
        'quantity',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->price * $this->quantity;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp '.number_format((float) $this->price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format($this->subtotal, 0, ',', '.');
    }

    public function getVariantLabelAttribute(): ?string
    {
        $parts = [];
        if ($this->variant_size) {
            $parts[] = 'Size '.$this->variant_size;
        }
        if ($this->variant_color) {
            $parts[] = $this->variant_color;
        }

        return ! empty($parts) ? implode(' · ', $parts) : null;
    }
}
