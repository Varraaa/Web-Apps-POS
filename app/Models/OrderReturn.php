<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'return_number',
        'quantity',
        'refund_amount',
        'reason',
        'condition',
        'refund_type',
        'restock_item',
        'notes',
        'status',
        'processed_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'refund_amount' => 'decimal:2',
        'restock_item' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getFormattedRefundAmountAttribute(): string
    {
        return 'Rp '.number_format((float) $this->refund_amount, 0, ',', '.');
    }

    public function getReasonLabelAttribute(): string
    {
        return match ($this->reason) {
            'wrong_size' => 'Ukuran Tidak Pas / Salah Size',
            'defective' => 'Cacat / Rusak Jahitan',
            'not_as_expected' => 'Tidak Sesuai Ekspektasi',
            'customer_change_mind' => 'Ingin Ganti Model / Batal',
            default => ucfirst(str_replace('_', ' ', $this->reason)),
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'intact_with_tags' => 'Utuh & Tag Lengkap (Layak Jual)',
            'opened_good' => 'Buka Segel (Kondisi Bagus)',
            'damaged' => 'Rusak Pemakaian Pelanggan',
            'defect_factory' => 'Cacat Produksi / Reject Pabrik',
            default => ucfirst(str_replace('_', ' ', $this->condition)),
        };
    }

    public function getRefundTypeLabelAttribute(): string
    {
        return match ($this->refund_type) {
            'cash' => 'Kembali Uang Tunai (Cash)',
            'store_credit' => 'Saldo Dompet Toko (Store Credit)',
            'exchange' => 'Tukar Barang (Exchange)',
            default => ucfirst($this->refund_type),
        };
    }

    protected static function booted(): void
    {
        static::creating(function (OrderReturn $return): void {
            if (empty($return->return_number)) {
                $return->return_number = 'RET-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -5));
            }
        });
    }
}
