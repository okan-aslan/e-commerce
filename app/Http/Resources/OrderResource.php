<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'shipping_address' => $this->shipping_address,
            'billing_address' => $this->billing_address,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'order_items' => OrderItemResource::collection($this->orderItems),
            'user' => new UserResource($this->user),
        ];
    }
}
