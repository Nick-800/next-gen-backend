<?php

namespace App\Listeners;

use App\Events\StockUpdated;
use App\Models\Waitlist;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listens for StockUpdated events and queues notifications for all
 * waitlist entries on the restocked variant.
 *
 * The actual email dispatch is wired in Milestone 3.
 * This listener marks the hook point and queues jobs to notify users.
 */
class NotifyWaitlistOnRestock implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(StockUpdated $event): void
    {
        $variant = $event->variant;

        // Only fire if the variant is transitioning from out-of-stock to in-stock
        if ($event->previousStock > 0 || $variant->stock_quantity === 0) {
            return;
        }

        Waitlist::query()
            ->where('variant_id', $variant->id)
            ->pendingNotification()
            ->with('user')
            ->each(function (Waitlist $entry) {
                // TODO (Milestone 3): dispatch(new SendRestockNotificationJob($entry));
                $entry->markNotified();
            });
    }
}
