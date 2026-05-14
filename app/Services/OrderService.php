<?php

namespace App\Services;

use App\Actions\Orders\CreateDraftOrderAction;
use App\Actions\Orders\TransitionOrderStatusAction;
use App\Enums\OrderStatus;
use App\Models\Order;

class OrderService
{
    public function __construct(
        private readonly CreateDraftOrderAction      $createDraft,
        private readonly TransitionOrderStatusAction $transitionStatus,
    ) {}

    /**
     * Detect if a cart should become a quote (bulk request > 10 items total).
     */
    public function isBulkQuote(array $items): bool
    {
        return collect($items)->sum('quantity') > 10;
    }

    /**
     * Create a draft quote order for bulk requests.
     */
    public function createQuote(array $data): Order
    {
        return $this->createDraft->execute($data);
    }

    /**
     * Approve a draft quote — transitions Draft → Pending.
     * Called from the Filament OrderResource bulk action.
     */
    public function approveQuote(Order $order): Order
    {
        return $this->transitionStatus->execute($order, OrderStatus::Pending);
    }

    /**
     * Transition an order to any target status.
     * The action enforces the state machine rules.
     */
    public function transition(Order $order, OrderStatus $newStatus): Order
    {
        return $this->transitionStatus->execute($order, $newStatus);
    }
}
