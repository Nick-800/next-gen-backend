<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'minimum_order_amount',
        'usage_limit',
        'times_used',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type'                 => CouponType::class,
            'value'                => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'expires_at'           => 'datetime',
            'is_active'            => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                     ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                     ->where(fn ($q) => $q->whereNull('usage_limit')->orWhereColumn('times_used', '<', 'usage_limit'));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function calculateDiscount(float $subtotal): float
    {
        return $this->type->calculate($this->value, $subtotal);
    }

    public function isValid(): bool
    {
        $withinUsage  = is_null($this->usage_limit) || $this->times_used < $this->usage_limit;
        $notExpired   = is_null($this->expires_at) || $this->expires_at->isFuture();

        return $this->is_active && $withinUsage && $notExpired;
    }
}
