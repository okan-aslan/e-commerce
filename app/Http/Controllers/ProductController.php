<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    use ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['user', 'category'])->get();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        $validatedData['user_id'] = auth()->id();

        $product = Product::create($validatedData);

        $message = 'Product created successfully';

        return $this->success(new ProductResource($product), $message, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['user', 'category'])->findOrFail($id);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        if (Auth::user()->id !== $product->user_id) {
            return $this->error('', "You're not authorized to make this request", 403);
        }

        $product->update($request->all());

        $message = 'Product updated successfully';

        return $this->success(new ProductResource($product), $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if (Auth::user()->id !== $product->user_id) {
            return $this->error(null, "You're not authorized to make this request", 403);
        }

        $product->delete();

        return response(null, 204);
    }
}
