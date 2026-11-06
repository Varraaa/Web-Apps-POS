<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierDrawerTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cashier_shift_id',
        'user_id',
        'type',
        'amount',
        'category',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function cashierShift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format((float) $this->amount, 0, ',', '.');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'cash_in' ? 'Kas Masuk (+)' : 'Kas Keluar (-)';
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'change_replenishment' => 'Tambah Uang Kembalian',
            'supplies' => 'Beli Perlengkapan Toko / ATK',
            'courier_delivery' => 'Ongkir Kurir Pickup / Logistik',
            'cleaning_security' => 'Kebersihan & Keamanan',
            'snack_refreshment' => 'Konsumsi / Galon Toko',
            default => 'Lain-lain / Operasional',
        };
    }
}
