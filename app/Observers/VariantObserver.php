<?php

namespace App\Observers;

use App\Events\StockUpdated;
use App\Models\Variant;
use Illuminate\Support\Facades\Cache;

class VariantObserver
{
    public function saved(Variant $variant): void
    {
        // Flush the product/variant cache so catalog reflects new data immediately
        Cache::tags(["variant:{$variant->id}", "product:{$variant->product_id}", 'products'])->flush();

        // Fire StockUpdated if stock_quantity changed
        if ($variant->wasChanged('stock_quantity')) {
            StockUpdated::dispatch(
                variant: $variant,
                previousStock: $variant->getOriginal('stock_quantity') ?? 0,
            );
        }
    }

    public function deleted(Variant $variant): void
    {
        Cache::tags(["variant:{$variant->id}", "product:{$variant->product_id}", 'products'])->flush();
    }
}
