<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock_quantity',
        'low_stock_threshold',
        'attributes_json',
        'images_json',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'               => 'decimal:2',
            'attributes_json'     => 'array',
            'images_json'         => 'array',
            'is_default'          => 'boolean',
            'is_active'           => 'boolean',
            'stock_quantity'      => 'integer',
            'low_stock_threshold' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlistEntries(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(Waitlist::class);
    }

    /**
     * Products that include this variant as a bundle suggestion.
     */
    public function bundledInProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_bundles',
            'bundled_variant_id',
            'product_id'
        );
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                     ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isLowStock(): bool
    {
        return $this->stock_quantity > 0
            && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity === 0;
    }
}
