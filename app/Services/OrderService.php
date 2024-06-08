<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    private $cartRepository;
    private $orderRepository;
    private $productRepository;

    public function __construct(
        CartRepository $cartRepository,
        OrderRepository $orderRepository,
        ProductRepository $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Create an order from the user's cart.
     *
     * @param int $userId The ID of the user
     * @param string $shippingAddress The shipping address for the order
     * @param string $billingAddress The billing address for the order
     * @return Order The created order
     * @throws \Exception If the user's cart is empty
     */
    public function createOrder(int $userId, string $shippingAddress, string $billingAddress): Order
    {
        $cart = $this->cartRepository->findOrCreateCartForUser($userId);
        if (!$cart) {
            throw new \Exception("User's cart is empty");
        }

        $order = $this->orderRepository->create([
            'user_id' => $userId,
            'shipping_address' => $shippingAddress,
            'billing_address' => $billingAddress,
            'status' => 'pending',
            'total_price' => $cart->total_price,
        ]);

        $cartItems = $this->cartRepository->getCartItems($cart);
        
        foreach ($cartItems as $cartItem) {
            $product = $this->productRepository->findProductById($cartItem->product_id);
            $orderItemData = [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $cartItem->quantity,
                'total_price' => $product->price * $cartItem->quantity,
            ];
            $this->orderRepository->createOrderItem($orderItemData);

            $this->productRepository->updateProductStock($product, $cartItem->quantity);
        }

        $this->cartRepository->emptyCart($cart);

        return $order;
    }

    /**
     * Get orders by user ID.
     *
     * @param int $userId The ID of the user
     * @return Collection The user's orders
     */
    public function getOrdersByUserId(int $userId): Collection
    {
        return $this->orderRepository->getOrdersByUserId($userId);
    }
}
