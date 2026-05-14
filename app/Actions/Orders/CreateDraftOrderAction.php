<?php

namespace App\Actions\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CreateDraftOrderAction
{
    /**
     * Create a Draft (quote) order when a bulk quantity > 10 is requested.
     *
     * @param  array{
     *   user_id: int,
     *   items: array<array{variant_id: int, quantity: int, unit_price: float, is_service: bool}>,
     *   notes: string|null
     * }  $data
     */
    public function execute(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(
                fn ($item) => $item['quantity'] * $item['unit_price']
            );

            $order = Order::create([
                'user_id'  => $data['user_id'],
                'status'   => OrderStatus::Draft,
                'subtotal' => $subtotal,
                'total'    => $subtotal,
                'is_quote' => true,
                'notes'    => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create($item);
            }

            return $order;
        });
    }
}
