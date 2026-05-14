<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'variant_id',
        'order_id',
        'rating',
        'title',
        'body',
        'is_verified_purchase',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'rating'               => 'integer',
            'is_verified_purchase' => 'boolean',
            'approved_at'          => 'datetime',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->isApproved();
    }

    public function setIsApprovedAttribute($value): void
    {
        $this->attributes['approved_at'] = $value ? now() : null;
    }
}
