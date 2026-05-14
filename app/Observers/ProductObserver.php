<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function saved(Product $product): void
    {
        Cache::tags(["product:{$product->id}", 'products', 'categories'])->flush();
    }

    public function deleted(Product $product): void
    {
        Cache::tags(["product:{$product->id}", 'products'])->flush();
    }
}
