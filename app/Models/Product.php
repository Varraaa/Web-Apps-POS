<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'barcode',
        'category',
        'description',
        'price',
        'discount_percent',
        'stock',
        'size',
        'color',
        'image_url',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percent' => 'integer',
            'stock' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_percent > 0;
    }

    public function getFinalPriceAttribute(): float
    {
        if (! $this->has_discount) {
            return (float) $this->price;
        }

        return round((float) $this->price * (1 - ($this->discount_percent / 100)), 0);
    }

    public function getDiscountSavingsAttribute(): float
    {
        if (! $this->has_discount) {
            return 0.0;
        }

        return (float) $this->price - $this->final_price;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= 5;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock <= 0;
    }

    public function decreaseStock(int $quantity): void
    {
        $this->decrement('stock', min($quantity, $this->stock));
    }

    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class)->latest();
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            $product->slug = $product->slug ?: Str::slug($product->name).'-'.Str::lower(Str::random(5));
            if (empty($product->sku)) {
                $categoryCode = Str::upper(substr($product->category, 0, 3));
                $product->sku = "VAR-{$categoryCode}-" . strtoupper(Str::random(4));
            }
            if (empty($product->barcode)) {
                $product->barcode = '899' . mt_rand(100000000, 999999999);
            }
        });
    }
}
