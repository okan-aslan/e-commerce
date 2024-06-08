<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * Create a new order.
     *
     * @param array $data The order data
     * @return Order The created order
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * Create a new order item.
     *
     * @param array $data The order item data
     * @return OrderItem The created order item
     */
    public function createOrderItem(array $data): OrderItem
    {
        return OrderItem::create($data);
    }

    /**
     * Get orders by user ID.
     *
     * @param int $userId The ID of the user
     * @return Collection The user's orders
     */
    public function getOrdersByUserId(int $userId): Collection
    {
        return Order::with(['orderItems.product', 'user'])->where('user_id', $userId)->get();
    }
}
