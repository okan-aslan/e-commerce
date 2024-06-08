# E-commerce Project

This project lays the foundation for an e-commerce platform where users can shop for products. Users can view products, add them to cart, and place orders. The project is developed using Laravel and utilizes Sanctum for API authentication.

## Authentication

API authentication is done using bearer tokens provided by Sanctum.

## Features

### User Management
- Users can create accounts, log in, and reset passwords.

### Product Management
- Admin can add, edit, and delete products. Each product can have attributes like name, description, price, and stock quantity.

### Cart Service
- **Get User Cart**: Retrieves the user's cart.
- **Add Product to Cart**: Adds a product to the user's cart. Checks if the requested quantity exceeds available stock.
- **Remove Product from Cart**: Removes a product from the user's cart. Handles cases where the requested quantity exceeds the quantity in the cart.
- **Empty Cart**: Clears the user's cart.

### Order Service
- **Create Order**: Creates an order from the user's cart. Checks if the user's cart is empty before creating the order. Calculates the total price of the order based on the items in the cart. Creates order items for each item in the cart. Updates product stock after creating the order.
- **Get Orders by User ID**: Retrieves orders for a given user ID.

## User Cart Creation
- Each registered user automatically gets a cart created for them.
- This is achieved using an event listener.

```
// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::apiResource('categories', CategoryController::class);

    Route::get('cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'addProduct']);
    Route::post('/cart/remove', [CartController::class, 'removeProduct']);
    Route::post('/cart/empty', [CartController::class, 'emptyCart']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'createOrder']);
});
```

## Screenshot of Postman Endpoints for the Project

![Screenshot 2024-06-08 at 8 17 54 PM](https://github.com/okan-aslann/e-commerce/assets/100617362/3c641ade-addc-4e9b-aef4-9b14caf26721)
