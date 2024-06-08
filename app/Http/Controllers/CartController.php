<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddOrRemoveProductFromCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error(null, 'You must log-in first.', 401);
        }

        $cart = Cart::with("cartItems.product")->firstOrCreate(['user_id' => $user->id]);


        return $this->success(new CartResource($cart), 'Showing your cart');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function addProduct(AddOrRemoveProductFromCartRequest $request)
    {
        $user = $request->user(); // veya $request->user();

        // Ürünü veritabanından bul
        $product = Product::findOrFail($request->input('product_id'));

        // Kullanıcının sepetini bul veya oluştur
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Sepetteki ürünü bul veya yeni bir sepet öğesi oluştur
        $cartItem = $cart->cartItems()->firstOrNew([
            'product_id' => $product->id
        ]);

        $totalQuantity = $cartItem->exists ? $cartItem->quantity + $request->input('quantity') : $request->input('quantity');

        // Stok kontrolü
        if ($totalQuantity > $product->stock) {
            return $this->error(null, "Requested quantity exceeds available stock", 400);
        }

        // Sepet öğesini güncelle veya oluştur
        $cartItem->quantity = $totalQuantity;
        $cartItem->save();

        $cart->total_price += $product->price * $request->input('quantity');
        $cart->save();

        return $this->success(new CartResource($cart->load('cartItems.product')), 'Product added to cart successfully');
    }

    /**
     * Display the specified resource.
     */
    public function removeProduct(AddOrRemoveProductFromCartRequest $request)
{
    $user = Auth::user();

    // Kullanıcının sepetini bul
    $cart = Cart::where('user_id', $user->id)->firstOrFail();

    // Ürünü bul
    $product = Product::findOrFail($request->input('product_id'));

    // Sepetteki ürünü bul
    $cartItem = $cart->cartItems()->where('product_id', $product->id)->firstOrFail();

    // Eğer sadece adet düşürme isteniyorsa
    if ($request->has('quantity')) {
        $quantityToRemove = $request->input('quantity');
        // Sepet öğesindeki adedi düşür
        $cartItem->quantity = max(0, $cartItem->quantity - $quantityToRemove);
        // Sepetin toplam fiyatını güncelle
        $totalPriceToRemove = $product->price * $quantityToRemove;
        $cart->total_price -= $totalPriceToRemove;
        $cart->save();

        // Eğer adet sıfırsa öğeyi sil
        if ($cartItem->quantity === 0) {
            $cartItem->delete();
        } else {
            $cartItem->save();
        }
    } else {
        // Adet belirtilmemişse ürünü sepetteki tüm adetiyle birlikte sil
        $totalPriceToRemove = $product->price * $cartItem->quantity;
        $cartItem->delete();

        // Sepetin toplam fiyatını güncelle
        $cart->total_price -= $totalPriceToRemove;
        $cart->save();
    }

    return $this->success(null, 'Product removed from cart successfully');
}

    /**
     * Remove the specified resource from storage.
     */
    public function emptyCart()
{
    $user = Auth::user();

    // Kullanıcının sepetini bul
    $cart = Cart::where('user_id', $user->id)->firstOrFail();

    // Sepetteki tüm ürünleri sil
    $cart->cartItems()->delete();

    // Sepetin toplam fiyatını sıfırla
    $cart->total_price = 0;
    $cart->save();

    return $this->success(null, 'Cart emptied successfully');
}
}
