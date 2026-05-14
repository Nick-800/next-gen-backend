<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    private const CATEGORY_TREE_TTL = 7200;  // 2 hours
    private const PRODUCT_TTL       = 3600;  // 1 hour

    /**
     * Return the full category tree (roots with children), cached.
     */
    public function getCategoryTree(): Collection
    {
        return Cache::tags(['categories'])->remember(
            key: 'category_tree',
            ttl: self::CATEGORY_TREE_TTL,
            callback: fn () => Category::active()
                ->roots()
                ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get()
        );
    }

    /**
     * Fetch a product with its active variants, cached by product ID.
     */
    public function getProductWithVariants(int $productId): ?Product
    {
        return Cache::tags(["product:{$productId}", 'products'])->remember(
            key: "product:{$productId}:with_variants",
            ttl: self::PRODUCT_TTL,
            callback: fn () => Product::active()
                ->with(['variants' => fn ($q) => $q->active()->orderBy('is_default', 'desc')])
                ->find($productId)
        );
    }

    /**
     * Get "Frequently Bought Together" variants for a product, cached.
     */
    public function getBundledVariants(int $productId): Collection
    {
        return Cache::tags(["product:{$productId}", 'bundles'])->remember(
            key: "product:{$productId}:bundles",
            ttl: self::PRODUCT_TTL,
            callback: fn () => Product::find($productId)
                ?->bundledVariants()
                ->with('product')
                ->active()
                ->get() ?? Collection::empty()
        );
    }

    /**
     * Get low-stock variants for the "Only X left!" badge trigger.
     * NOT cached — stock must always be live.
     */
    public function getLowStockVariants(): Collection
    {
        return Variant::active()->lowStock()->get();
    }
}
