<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductRepository
{
    /**
     * Find a product by its ID.
     *
     * @param int $productId The ID of the product
     * @return \App\Models\Product The found product
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the product is not found
     */
    public function findProductById(int $productId): Product
    {
        return Product::with(['user', 'category'])->findOrFail($productId);
    }

     /**
     * Update the stock of the given product.
     *
     * @param Product $product The product instance
     * @param int $quantity The quantity to subtract from the stock
     * @return void
     */
    public function updateProductStock(Product $product, int $quantity): void
    {
        $product->stock -= $quantity;
        $product->save();
    }
}
