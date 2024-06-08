<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddOrRemoveProductFromCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
    use ApiResponses;

    private $cartService;

    /**
     * Create a new CartController instance.
     *
     * @param \App\Services\CartService $cartService The cart service instance
     */
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display the user's cart.
     *
     * @return \App\Http\Resources\CartResource The user's cart resource
     */
    public function index(): CartResource
    {
        $cart = $this->cartService->getUserCart();
        return new CartResource($cart);
    }

    /**
     * Add a product to the cart.
     *
     * @param \App\Http\Requests\AddOrRemoveProductFromCartRequest $request The request instance
     * @return \Illuminate\Http\JsonResponse The JSON response
     */
    public function addProduct(AddOrRemoveProductFromCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->addProductToCart($request->all());
            return $this->success(new CartResource($cart), 'Product added to cart successfully', 201);
        } catch (\Exception $e) {
            return $this->error(null, $e->getMessage(), 400);
        }
    }

    /**
     * Remove a product from the cart.
     *
     * @param \App\Http\Requests\AddOrRemoveProductFromCartRequest $request The request instance
     * @return \Illuminate\Http\JsonResponse The JSON response
     */
    public function removeProduct(AddOrRemoveProductFromCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->removeProductFromCart($request->all());
            return $this->success(new CartResource($cart), 'Product removed from cart successfully', 200);
        } catch (\Exception $e) {
            return $this->error(null, $e->getMessage(), 400);
        }
    }

    /**
     * Empty the cart.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response
     */
    public function emptyCart(): JsonResponse
    {
        try {
            $this->cartService->emptyCart();
            return $this->success(null, '', 204);
        } catch (\Exception $e) {
            return $this->error(null, $e->getMessage(), 400);
        }
    }
}
