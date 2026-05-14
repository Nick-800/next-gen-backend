<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waitlist extends Model
{
    protected $fillable = [
        'user_id',
        'variant_id',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
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

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Entries that have never been notified — used by the restock notification system.
     */
    public function scopePendingNotification($query)
    {
        return $query->whereNull('notified_at');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function markNotified(): void
    {
        $this->update(['notified_at' => now()]);
    }
}
