<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_spend',
        'max_discount',
        'is_active',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function isValid(?float $subtotal = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($subtotal !== null && $subtotal < (float) $this->min_spend) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->isValid($subtotal)) {
            return 0.0;
        }

        if ($this->type === 'percent') {
            $amount = ($subtotal * (float) $this->value) / 100;
            if ($this->max_discount && $amount > (float) $this->max_discount) {
                $amount = (float) $this->max_discount;
            }

            return round($amount, 0);
        }

        return min((float) $this->value, $subtotal);
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->type === 'percent') {
            return ((int) $this->value).'% OFF';
        }

        return 'Hemat Rp '.number_format($this->value, 0, ',', '.');
    }
}
