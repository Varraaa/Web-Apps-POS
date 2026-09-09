<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'category', 'description', 'price', 'discount_percent', 'stock', 'size', 'color', 'image_url', 'is_featured'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'discount_percent' => 'integer', 'is_featured' => 'boolean'];
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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            $product->slug = $product->slug ?: Str::slug($product->name).'-'.Str::lower(Str::random(5));
        });
    }
}
