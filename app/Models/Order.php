<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'guest_email',
        'coupon_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax',
        'total',
        'shipping_address_json',
        'notes',
        'is_quote',
    ];

    protected function casts(): array
    {
        return [
            'status'               => OrderStatus::class,
            'subtotal'             => 'decimal:2',
            'discount_amount'      => 'decimal:2',
            'tax'                  => 'decimal:2',
            'total'                => 'decimal:2',
            'shipping_address_json' => 'array',
            'is_quote'             => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', OrderStatus::Draft);
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::Pending);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', OrderStatus::Completed);
    }

    public function scopeQuotes($query)
    {
        return $query->where('is_quote', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function hasPhysicalItems(): bool
    {
        return $this->items()->where('is_service', false)->exists();
    }

    public function isGuestOrder(): bool
    {
        return is_null($this->user_id) && !is_null($this->guest_email);
    }
}
