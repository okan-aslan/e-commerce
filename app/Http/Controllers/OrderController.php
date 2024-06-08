<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use ApiResponses;

    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the user's orders.
     *
     * @return JsonResponse The JSON response
     */
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $orders = $this->orderService->getOrdersByUserId($user->id);
            return $this->success(OrderResource::collection($orders), 'User orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->error(null, $e->getMessage(), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Order $order): JsonResponse
    {
        if (Auth::id() !== $order->user_id) {
            return $this->error(null, 'Unanhorized action !', 401);
        }

        return $this->success(new OrderResource($order), "Showing order.");
    }

    /**
     * Create an order for the authenticated user.
     *
     * @param CreateOrderRequest $request The request instance
     * @return JsonResponse The JSON response
     */
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $order = $this->orderService->createOrder(
                $user->id,
                $request->input('shipping_address'),
                $request->input('billing_address')
            );
            return $this->success(new OrderResource($order), 'Order created successfully', 201);
        } catch (\Exception $e) {
            return $this->error(null, $e->getMessage(), 400);
        }
    }
}
