<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cashier_name',
        'opened_at',
        'closed_at',
        'starting_cash',
        'expected_cash',
        'actual_cash',
        'difference',
        'total_cash_sales',
        'total_non_cash_sales',
        'total_transactions',
        'status',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'starting_cash' => 'float',
        'expected_cash' => 'float',
        'actual_cash' => 'float',
        'difference' => 'float',
        'total_cash_sales' => 'float',
        'total_non_cash_sales' => 'float',
        'total_transactions' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function drawerTransactions(): HasMany
    {
        return $this->hasMany(CashierDrawerTransaction::class);
    }

    public function totalCashIn(): float
    {
        return (float) $this->drawerTransactions()->where('type', 'cash_in')->sum('amount');
    }

    public function totalCashOut(): float
    {
        return (float) $this->drawerTransactions()->where('type', 'cash_out')->sum('amount');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'open')->latest('opened_at');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }
}
