<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'reason',
        'adjusted_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in' => 'Stok Masuk (+)',
            'out' => 'Stok Keluar (-)',
            'damage' => 'Barang Rusak / Reject (-)',
            'loss' => 'Barang Hilang / Selisih (-)',
            'opname_correction' => 'Koreksi Opname Fisik (=)',
            default => ucfirst($this->type),
        };
    }
}
