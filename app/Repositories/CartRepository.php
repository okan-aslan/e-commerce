<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CartRepository
{
    /**
     * Find or create a cart for the given user ID.
     *
     * @param int $userId The ID of the user
     * @return \App\Models\Cart The found or created cart
     */
    public function findOrCreateCartForUser(int $userId): Cart
    {
        return Cart::with("cartItems.product")->firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Get all items in the cart.
     *
     * @param \App\Models\Cart $cart The cart instance
     * @return \Illuminate\Database\Eloquent\Collection The collection of cart items
     */
    public function getCartItems(Cart $cart): Collection
    {
        return $cart->cartItems()->with('product')->get();
    }

    /**
     * Find a cart item by its product ID.
     *
     * @param \App\Models\Cart $cart The cart instance
     * @param int $productId The ID of the product
     * @return \App\Models\CartItem|null The found cart item, or null if not found
     */
    public function findCartItemByProductId(Cart $cart, int $productId): ?CartItem
    {
        return $cart->cartItems()->where('product_id', $productId)->first();
    }

    /**
     * Find or create a cart item for the given cart and product.
     *
     * @param \App\Models\Cart $cart The cart instance
     * @param \Illuminate\Database\Eloquent\Model $product The product instance
     * @return \App\Models\CartItem The found or created cart item
     */
    public function findOrCreateCartItem(Cart $cart, Model $product): CartItem
    {
        return $cart->cartItems()->firstOrNew(['product_id' => $product->id]);
    }

    /**
     * Update the quantity of the given cart item.
     *
     * @param \App\Models\CartItem $cartItem The cart item instance
     * @param int $quantity The new quantity
     * @return void
     */
    public function updateCartItemQuantity(CartItem $cartItem, int $quantity): void
    {
        $cartItem->quantity = $quantity;
        $cartItem->save();
    }

    /**
     * Update the total price of the given cart.
     *
     * @param \App\Models\Cart $cart The cart instance
     * @param float $productPrice The price of the product
     * @param int $quantity The quantity of the product
     * @return void
     */
    public function updateCartTotalPrice(Cart $cart, float $productPrice, int $quantity): void
    {
        $cart->total_price += $productPrice * $quantity;
        $cart->save();
    }

    /**
     * Delete the given cart item.
     *
     * @param \App\Models\CartItem $cartItem The cart item instance
     * @return void
     */
    public function deleteCartItem(CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    /**
     * Empty the given cart.
     *
     * @param \App\Models\Cart $cart The cart instance
     * @return void
     */
    public function emptyCart(Cart $cart): void
    {
        $cart->cartItems()->delete();
        $cart->total_price = 0;
        $cart->save();
    }
}
