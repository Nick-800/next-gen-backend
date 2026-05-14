<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'variant_id',
        'quantity',
        'unit_price',
        'is_service',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'is_service' => 'boolean',
            'quantity'   => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function lineTotal(): float
    {
        return round($this->quantity * $this->unit_price, 2);
    }
}
