<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\B2BApiController;
use App\Http\Controllers\Api\B2CApiController;
use App\Http\Controllers\Api\SalesTeamApiController;
use App\Http\Controllers\Api\ManufacturingTeamApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// B2B API Routes
Route::prefix('b2b')->name('api.b2b.')->group(function () {
    // Authentication routes (no middleware)
    Route::post('/login', [B2BApiController::class, 'login'])->name('login');
    Route::post('/logout', [B2BApiController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    Route::post('/refresh', [B2BApiController::class, 'refresh'])->middleware('auth:sanctum')->name('refresh');
    Route::get('/user', [B2BApiController::class, 'user'])->middleware('auth:sanctum')->name('user');
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Dashboard
        Route::get('/dashboard', [B2BApiController::class, 'dashboard'])->name('dashboard');
        
        // Products
        Route::get('/products', [B2BApiController::class, 'products'])->name('products');
        
        // Orders
        Route::get('/orders', [B2BApiController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{orderId}', [B2BApiController::class, 'order'])->name('orders.show');
        
        // Profile
        Route::get('/profile', [B2BApiController::class, 'profile'])->name('profile');
        Route::post('/profile', [B2BApiController::class, 'updateProfile'])->name('profile.update');
        
        // Return requests
        Route::get('/orders/{orderId}/items/{orderItemId}/return-request', [B2BApiController::class, 'showReturnRequestForm'])->name('orders.return-request.form');
        Route::post('/orders/{orderId}/items/{orderItemId}/return-request', [B2BApiController::class, 'submitReturnRequest'])->name('orders.return-request.submit');
        Route::get('/orders/{orderId}/return-requests', [B2BApiController::class, 'showReturnRequests'])->name('orders.return-requests');
        
        // Rewards
        Route::get('/rewards', [B2BApiController::class, 'showRewards'])->name('rewards.index');
        Route::get('/rewards/{rewardId}/claim', [B2BApiController::class, 'showClaimForm'])->name('rewards.claim.form');
        Route::post('/rewards/{rewardId}/claim', [B2BApiController::class, 'submitClaim'])->name('rewards.claim.submit');
        Route::get('/reward-claims', [B2BApiController::class, 'showMyClaims'])->name('reward-claims.index');
        
        // Cart
        Route::get('/cart', [B2BApiController::class, 'getCart'])->name('cart.index');
        Route::post('/cart/add', [B2BApiController::class, 'addToCart'])->name('cart.add');
        Route::put('/cart/update', [B2BApiController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/remove', [B2BApiController::class, 'removeFromCart'])->name('cart.remove');
        Route::delete('/cart/clear', [B2BApiController::class, 'clearCart'])->name('cart.clear');
        Route::get('/cart/count', [B2BApiController::class, 'getCartCount'])->name('cart.count');
        
        // Checkout
        Route::get('/checkout', [B2BApiController::class, 'checkoutDetails'])->name('checkout.details');
        Route::post('/checkout', [B2BApiController::class, 'placeOrder'])->name('checkout.place');
    });
});

// B2C API Routes
Route::prefix('b2c')->name('api.b2c.')->group(function () {
    // Authentication routes (no middleware)
    Route::post('/login', [B2CApiController::class, 'login'])->name('login');
    Route::post('/logout', [B2CApiController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    Route::post('/refresh', [B2CApiController::class, 'refresh'])->middleware('auth:sanctum')->name('refresh');
    Route::get('/user', [B2CApiController::class, 'user'])->middleware('auth:sanctum')->name('user');
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Dashboard
        Route::get('/dashboard', [B2CApiController::class, 'dashboard'])->name('dashboard');
        
        // Products
        Route::get('/products', [B2CApiController::class, 'products'])->name('products');
        Route::get('/products/category/{categoryId}', [B2CApiController::class, 'productsByCategory'])->name('products.category');
        Route::get('/products/subcategory/{subcategoryId}', [B2CApiController::class, 'productsBySubcategory'])->name('products.subcategory');
        Route::get('/products/subsubcategory/{subSubcategoryId}', [B2CApiController::class, 'productsBySubSubcategory'])->name('products.subsubcategory');
        Route::get('/product/{id}', [B2CApiController::class, 'productDetails'])->name('product.details');
        
        // Orders
        Route::get('/orders', [B2CApiController::class, 'orders'])->name('orders.index');
        Route::get('/order/{orderId}', [B2CApiController::class, 'orderDetails'])->name('orders.show');
        
        // Profile
        Route::get('/profile', [B2CApiController::class, 'profile'])->name('profile');
        Route::post('/profile', [B2CApiController::class, 'updateProfile'])->name('profile.update');
        Route::put('/change-password', [B2CApiController::class, 'changePassword'])->name('change.password');
        
        // Cart
        Route::get('/cart', [B2CApiController::class, 'getCart'])->name('cart.index');
        Route::post('/cart/add', [B2CApiController::class, 'addToCart'])->name('cart.add');
        Route::put('/cart/update', [B2CApiController::class, 'updateCart'])->name('cart.update');
        Route::delete('/cart/remove', [B2CApiController::class, 'removeFromCart'])->name('cart.remove');
        Route::delete('/cart/clear', [B2CApiController::class, 'clearCart'])->name('cart.clear');
        Route::get('/cart/count', [B2CApiController::class, 'getCartCount'])->name('cart.count');
        
        // Checkout
        Route::get('/checkout', [B2CApiController::class, 'checkoutDetails'])->name('checkout.details');
        Route::post('/checkout', [B2CApiController::class, 'placeOrder'])->name('checkout.place');
    });
});

// Sales Team API Routes
Route::prefix('sales-team')->name('api.sales-team.')->group(function () {
    // Authentication routes (no middleware)
    Route::post('/login', [SalesTeamApiController::class, 'login'])->name('login');
    Route::post('/logout', [SalesTeamApiController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    Route::post('/refresh', [SalesTeamApiController::class, 'refresh'])->middleware('auth:sanctum')->name('refresh');
    Route::get('/user', [SalesTeamApiController::class, 'user'])->middleware('auth:sanctum')->name('user');
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SalesTeamApiController::class, 'dashboard'])->name('dashboard');
        
        // Orders
        Route::get('/orders', [SalesTeamApiController::class, 'orders'])->name('orders.index');
        Route::get('/order/{orderId}', [SalesTeamApiController::class, 'orderDetails'])->name('orders.show');
        Route::put('/order/{orderId}/status', [SalesTeamApiController::class, 'updateOrderStatus'])->name('orders.status.update');
        
        // Customers
        Route::get('/customers', [SalesTeamApiController::class, 'customers'])->name('customers.index');
        Route::get('/customer/{customerId}', [SalesTeamApiController::class, 'customerDetails'])->name('customers.show');
        
        // Profile
        Route::get('/profile', [SalesTeamApiController::class, 'profile'])->name('profile');
        Route::post('/profile', [SalesTeamApiController::class, 'updateProfile'])->name('profile.update');
        Route::put('/change-password', [SalesTeamApiController::class, 'changePassword'])->name('change.password');
    });
});

// Manufacturing Team API Routes
Route::prefix('manufacturing-team')->name('api.manufacturing-team.')->group(function () {
    // Authentication routes (no middleware)
    Route::post('/login', [ManufacturingTeamApiController::class, 'login'])->name('login');
    Route::post('/logout', [ManufacturingTeamApiController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    Route::post('/refresh', [ManufacturingTeamApiController::class, 'refresh'])->middleware('auth:sanctum')->name('refresh');
    Route::get('/user', [ManufacturingTeamApiController::class, 'user'])->middleware('auth:sanctum')->name('user');
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ManufacturingTeamApiController::class, 'dashboard'])->name('dashboard');
        
        // Orders
        Route::get('/orders', [ManufacturingTeamApiController::class, 'orders'])->name('orders.index');
        Route::get('/order/{orderId}', [ManufacturingTeamApiController::class, 'orderDetails'])->name('orders.show');
        Route::put('/order/{orderId}/status', [ManufacturingTeamApiController::class, 'updateOrderStatus'])->name('orders.status.update');
        
        // Products
        Route::get('/products', [ManufacturingTeamApiController::class, 'products'])->name('products.index');
        Route::get('/product/{productId}', [ManufacturingTeamApiController::class, 'productDetails'])->name('products.show');
        
        // Profile
        Route::get('/profile', [ManufacturingTeamApiController::class, 'profile'])->name('profile');
        Route::post('/profile', [ManufacturingTeamApiController::class, 'updateProfile'])->name('profile.update');
        Route::put('/change-password', [ManufacturingTeamApiController::class, 'changePassword'])->name('change.password');
    });
});