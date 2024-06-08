<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);

    Route::get('cart', [CartController::class, 'index']);
    
    Route::post('/cart/add', [CartController::class, 'addProduct']);
    Route::post('/cart/remove', [CartController::class, 'removeProduct']);
    Route::post('/cart/empty', [CartController::class, 'emptyCart']);

});
