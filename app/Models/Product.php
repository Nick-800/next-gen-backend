<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'type',
        'base_price',
        'is_featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'type'        => ProductType::class,
            'status'      => ProductStatus::class,
            'base_price'  => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    /**
     * "Frequently Bought Together" variants suggested alongside this product.
     */
    public function bundledVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            Variant::class,
            'product_bundles',
            'product_id',
            'bundled_variant_id'
        );
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::Active);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePhysical($query)
    {
        return $query->where('type', ProductType::Physical);
    }

    public function scopeService($query)
    {
        return $query->where('type', ProductType::Service);
    }
}
