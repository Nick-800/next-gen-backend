<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Models\Order;
use Illuminate\Validation\ValidationException;

class TransitionOrderStatusAction
{
    /**
     * Validate and apply an OrderStatus transition.
     *
     * @throws ValidationException  if the transition is not allowed by the state machine.
     */
    public function execute(Order $order, OrderStatus $newStatus): Order
    {
        $currentStatus = $order->status;

        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => "Cannot transition order from [{$currentStatus->label()}] to [{$newStatus->label()}].",
            ]);
        }

        $order->update(['status' => $newStatus]);

        OrderStatusChanged::dispatch(
            order: $order->fresh(),
            previousStatus: $currentStatus,
            newStatus: $newStatus,
        );

        return $order->fresh();
    }
}
