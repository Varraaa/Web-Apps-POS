<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'name',
        'phone',
        'email',
        'tier',
        'points',
        'store_credit',
        'total_spent',
        'address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'store_credit' => 'decimal:2',
            'total_spent' => 'decimal:2',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function getFormattedTotalSpentAttribute(): string
    {
        return 'Rp '.number_format((float) $this->total_spent, 0, ',', '.');
    }

    public function getFormattedStoreCreditAttribute(): string
    {
        return 'Rp '.number_format((float) $this->store_credit, 0, ',', '.');
    }

    public function addStoreCredit(float $amount, string $reason = ''): void
    {
        $this->store_credit = (float) $this->store_credit + $amount;
        $this->save();
    }

    public function deductStoreCredit(float $amount): void
    {
        $this->store_credit = max(0.0, (float) $this->store_credit - $amount);
        $this->save();
    }

    public function getTierLabelAttribute(): string
    {
        return match ($this->tier) {
            'gold' => 'Gold Royale',
            'silver' => 'Silver VIP',
            default => 'Bronze Member',
        };
    }

    public function getTierBadgeClassAttribute(): string
    {
        return match ($this->tier) {
            'gold' => 'bg-amber-100 text-amber-800 border-amber-300',
            'silver' => 'bg-slate-200 text-slate-800 border-slate-300',
            default => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    /**
     * Add spending and recalculate loyalty tier.
     */
    public function recordTransaction(float $amount, int $pointsEarned = 0, int $pointsUsed = 0): void
    {
        $this->total_spent = (float) $this->total_spent + $amount;
        $this->points = max(0, (int) $this->points + $pointsEarned - $pointsUsed);

        // Update tier based on total spent
        if ($this->total_spent >= 5000000) {
            $this->tier = 'gold';
        } elseif ($this->total_spent >= 1500000) {
            $this->tier = 'silver';
        } else {
            $this->tier = 'bronze';
        }

        $this->save();
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer): void {
            if (empty($customer->member_code)) {
                $customer->member_code = 'MBR-'.strtoupper(Str::random(6));
            }
        });
    }
}
