<?php

namespace App\Providers;

use App\Events\StockUpdated;
use App\Listeners\NotifyWaitlistOnRestock;
use App\Models\Product;
use App\Models\Variant;
use App\Observers\ProductObserver;
use App\Observers\VariantObserver;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ─── Eloquent Observers ───────────────────────────────────────────────
        Variant::observe(VariantObserver::class);
        Product::observe(ProductObserver::class);

        // ─── Event → Listener Mappings ────────────────────────────────────────
        Event::listen(StockUpdated::class, NotifyWaitlistOnRestock::class);

        // ─── Scramble API Docs — gated by admin role in ALL environments ──────
        // Guard with class_exists so the app boots before dedoc/scramble is installed
        if (class_exists(\Dedoc\Scramble\Scramble::class)) {
            Scramble::routes(function (\Illuminate\Routing\Route $route) {
                // Only expose routes under api/* prefix
                return str_starts_with($route->uri, 'api/');
            });
        }

        Gate::define('viewApiDocs', function ($user) {
            return $user?->hasRole('admin') ?? false;
        });
    }
}

