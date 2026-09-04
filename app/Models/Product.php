<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'category', 'description', 'price', 'stock', 'size', 'color', 'image_url', 'is_featured'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_featured' => 'boolean'];
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
