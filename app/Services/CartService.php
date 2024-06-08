<?php

namespace App\Services;

use App\Models\Cart;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;

class CartService
{
    private $cartRepository;
    private $productRepository;

    /**
     * Create a new CartService instance.
     *
     * @param \App\Repositories\CartRepository $cartRepository The cart repository instance
     * @param \App\Repositories\ProductRepository $productRepository The product repository instance
     */
    public function __construct(CartRepository $cartRepository, ProductRepository $productRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Get the user's cart.
     *
     * @return \App\Models\Cart The user's cart
     */
    public function getUserCart(): Cart
    {
        $user = auth()->user();
        return $this->cartRepository->findOrCreateCartForUser($user->id);
    }

    /**
     * Add a product to the cart.
     *
     * @param array $data The data containing product information
     * @return \App\Models\Cart The updated cart
     * @throws \Exception If requested quantity exceeds available stock
     */
    public function addProductToCart(array $data): Cart
    {
        $user = auth()->user();
        $product = $this->productRepository->findProductById($data['product_id']);
        $cart = $this->cartRepository->findOrCreateCartForUser($user->id);
        $cartItem = $this->cartRepository->findOrCreateCartItem($cart, $product);

        $totalQuantity = $cartItem->exists ? $cartItem->quantity + $data['quantity'] : $data['quantity'];

        if ($totalQuantity > $product->stock) {
            throw new \Exception("Requested quantity exceeds available stock");
        }

        $this->cartRepository->updateCartItemQuantity($cartItem, $totalQuantity);
        $this->cartRepository->updateCartTotalPrice($cart, $product->price, $data['quantity']);

        return $cart;
    }

    /**
     * Remove a product from the cart.
     *
     * @param array $data The data containing product information
     * @return \App\Models\Cart The updated cart
     * @throws \Exception If the product is not found in the cart
     */
    public function removeProductFromCart(array $data): Cart
    {
        $user = auth()->user();
        $productId = $data['product_id'];
        $cart = $this->cartRepository->findOrCreateCartForUser($user->id);

        $cartItem = $this->cartRepository->findCartItemByProductId($cart, $productId);

        if (!$cartItem) {
            throw new \Exception('Product not found in the cart.', 400);
        }

        $quantityToRemove = $data['quantity'] ?? $cartItem->quantity;
        $quantityToRemove = min($cartItem->quantity, max(0, $quantityToRemove));

        if ($quantityToRemove === $cartItem->quantity) {
            $this->cartRepository->deleteCartItem($cartItem);
        } else {
            $cartItem->quantity -= $quantityToRemove;
            $this->cartRepository->updateCartItemQuantity($cartItem, $cartItem->quantity);
        }

        $product = $this->productRepository->findProductById($productId);
        $totalPriceToRemove = $product->price * $quantityToRemove;

        $cart->total_price -= $totalPriceToRemove;
        $cart->save();

        return $cart;
    }

    /**
     * Empty the cart.
     *
     * @return void
     */
    public function emptyCart(): void
    {
        $user = auth()->user();
        $cart = $this->cartRepository->findOrCreateCartForUser($user->id);
        $this->cartRepository->emptyCart($cart);
    }

}
