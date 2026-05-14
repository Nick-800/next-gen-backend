<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'variant_id',
        'price_at_watch',
    ];

    protected function casts(): array
    {
        return [
            'price_at_watch' => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Returns true if the current variant price is lower than the watched price.
     * Used by the price alert scheduler (Milestone 3).
     */
    public function hasPriceDropped(): bool
    {
        return $this->variant->price < $this->price_at_watch;
    }
}
