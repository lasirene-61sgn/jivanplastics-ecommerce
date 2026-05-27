<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ManufacturingTeamController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReturnRequestController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\RewardClaimController;
use App\Http\Controllers\Admin\SalesTeamController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\SubSubcategoryController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\ManufacturingTeamAuthController;
use App\Http\Controllers\Auth\SalesTeamAuthController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\B2BDashboardController;
use App\Http\Controllers\Frontend\B2CDashboardController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\ManufacturingTeam\DashboardController;
use App\Http\Controllers\SalesTeam\DashboardController as SalesTeamDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Frontend Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Product Routes
Route::get('/products', [FrontendProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [FrontendProductController::class, 'show'])->name('products.show');
Route::get('/categories/{category}', [FrontendProductController::class, 'byCategory'])->name('categories.show');
Route::get('/subcategories/{subcategory}', [FrontendProductController::class, 'bySubcategory'])->name('subcategories.show');
Route::get('/sub-subcategories/{subSubcategory}', [FrontendProductController::class, 'bySubSubcategory'])->name('sub-subcategories.show');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
});

// B2B Routes
Route::prefix('b2b')->name('b2b.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [B2BDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [B2BDashboardController::class, 'products'])->name('products');
    Route::get('/products/{product}', [B2BDashboardController::class, 'showProduct'])->name('products.show');
    Route::get('/orders', [B2BDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [B2BDashboardController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{order}/invoices/{invoice}', [B2BDashboardController::class, 'showInvoice'])->name('orders.invoice');
    Route::get('/profile', [B2BDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [B2BDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Return request routes
    Route::get('/orders/{order}/items/{orderItem}/return-request', [B2BDashboardController::class, 'showReturnRequestForm'])->name('orders.return-request.form');
    Route::post('/orders/{order}/items/{orderItem}/return-request', [B2BDashboardController::class, 'submitReturnRequest'])->name('orders.return-request.submit');
    Route::get('/orders/{order}/return-requests', [B2BDashboardController::class, 'showReturnRequests'])->name('orders.return-requests');
    Route::get('/orders/{order}/return-notes/{returnNote}', [B2BDashboardController::class, 'showReturnNote'])->name('orders.return-note');
    
    // Rewards routes
    Route::get('/rewards', [B2BDashboardController::class, 'showRewards'])->name('rewards.index');
    Route::get('/rewards/{reward}/claim', [B2BDashboardController::class, 'showClaimForm'])->name('rewards.claim.form');
    Route::post('/rewards/{reward}/claim', [B2BDashboardController::class, 'submitClaim'])->name('rewards.claim.submit');
    Route::get('/reward-claims', [B2BDashboardController::class, 'showMyClaims'])->name('reward-claims.index');
    Route::get('/reward-claims/{claim}/invoice', [B2BDashboardController::class, 'showRewardInvoice'])->name('reward-claims.invoice');
    
    // B2B Discounts
    Route::get('/discounts', [B2BDashboardController::class, 'showB2BDiscounts'])->name('discounts.index');
});


// B2C Routes
Route::prefix('b2c')->name('b2c.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [B2CDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [B2CDashboardController::class, 'products'])->name('products');
    Route::get('/products/{product}', [B2CDashboardController::class, 'showProduct'])->name('products.show');
    Route::get('/orders', [B2CDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [B2CDashboardController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [B2CDashboardController::class, 'showInvoice'])->name('orders.invoice');
    Route::get('/profile', [B2CDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [B2CDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Login Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login')->middleware('admin.guest');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Admin Dashboard Route
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard')->middleware('auth:admin');
    
    // Category Routes
    Route::middleware('auth:admin')->group(function () {
        // Customer type specific routes (must be before resource route)
        Route::get('/customers/dealers', [CustomerController::class, 'dealers'])->name('customers.dealers');
        Route::get('/customers/individuals', [CustomerController::class, 'individuals'])->name('customers.individuals');
        
        // Order management routes
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoices/{invoice}', [OrderController::class, 'showInvoice'])->name('orders.invoice');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('/orders/allocate', [OrderController::class, 'allocateToManufacturingTeam'])->name('orders.allocate');
        Route::put('/orders/{order}/manufacturing-status', [OrderController::class, 'updateManufacturingStatus'])->name('orders.update-manufacturing-status');
        Route::put('/orders/{order}/partial-dispatch', [OrderController::class, 'partialDispatch'])->name('orders.partial-dispatch');
        Route::post('/orders/{order}/dispatch', [OrderController::class, 'markAsDispatched'])->name('orders.dispatch');
        
        // Return request management routes
        Route::get('/return-requests', [ReturnRequestController::class, 'index'])->name('return-requests.index');
        Route::get('/return-requests/{returnRequest}', [ReturnRequestController::class, 'show'])->name('return-requests.show');
        Route::get('/return-requests/{returnRequest}/return-note/{returnNote}', [ReturnRequestController::class, 'showReturnNote'])->name('return-requests.return-note');
        Route::put('/return-requests/{returnRequest}/status', [ReturnRequestController::class, 'updateStatus'])->name('return-requests.update-status');
        
        // Reward management routes
        Route::post('/rewards', [RewardController::class, 'store'])->name('rewards.store');
        Route::get('/rewards', [RewardController::class, 'index'])->name('rewards.index');
        Route::get('/rewards/create', [RewardController::class, 'create'])->name('rewards.create');
        Route::get('/rewards/{reward}/edit', [RewardController::class, 'edit'])->name('rewards.edit');
        Route::put('/rewards/{reward}', [RewardController::class, 'update'])->name('rewards.update');
        Route::delete('/rewards/{reward}', [RewardController::class, 'destroy'])->name('rewards.destroy');
        
        // Reward claim management routes
        Route::get('/reward-claims', [RewardClaimController::class, 'index'])->name('reward-claims.index');
        Route::get('/reward-claims/{claim}', [RewardClaimController::class, 'show'])->name('reward-claims.show');
        Route::get('/reward-claims/{claim}/invoice', [RewardClaimController::class, 'showInvoice'])->name('reward-claims.invoice');
        Route::put('/reward-claims/{claim}/status', [RewardClaimController::class, 'updateStatus'])->name('reward-claims.update-status');
        
        // Sales team routes
        Route::get('/sales-team/{salesTeam}/orders', [SalesTeamController::class, 'orders'])->name('sales-team.orders');
        Route::get('/sales-team/{salesTeam}/dealer-support', [SalesTeamController::class, 'dealerSupport'])->name('sales-team.dealer-support');
        Route::get('/sales-team/{salesTeam}/enhanced-dealer-support', [SalesTeamController::class, 'enhancedDealerSupport'])->name('sales-team.enhanced-dealer-support');
        Route::post('/sales-team/{salesTeam}/select-dealer', [SalesTeamController::class, 'selectDealer'])->name('sales-team.select-dealer');
        Route::post('/sales-team/{salesTeam}/add-to-cart', [SalesTeamController::class, 'addToCart'])->name('sales-team.add-to-cart');
        Route::post('/sales-team/{salesTeam}/update-cart', [SalesTeamController::class, 'updateCart'])->name('sales-team.update-cart');
        Route::post('/sales-team/{salesTeam}/remove-from-cart', [SalesTeamController::class, 'removeFromCart'])->name('sales-team.remove-from-cart');
        Route::post('/sales-team/{salesTeam}/clear-cart', [SalesTeamController::class, 'clearCart'])->name('sales-team.clear-cart');
        Route::post('/sales-team/{salesTeam}/place-order-cart', [SalesTeamController::class, 'placeOrderFromCart'])->name('sales-team.place-order-cart');
        Route::post('/sales-team/{salesTeam}/place-order', [SalesTeamController::class, 'placeOrderForDealer'])->name('sales-team.place-order');
        Route::get('/sales-team/{salesTeam}/analytics', [SalesTeamController::class, 'orderAnalytics'])->name('sales-team.analytics');
        Route::get('/sales-team/{salesTeam}/customer-relationships', [SalesTeamController::class, 'customerRelationships'])->name('sales-team.customer-relationships');
        Route::get('/sales-team/{salesTeam}/customers/{customer}', [SalesTeamController::class, 'customerDetails'])->name('sales-team.customer-details');
        
        // AJAX Routes
        Route::get('/ajax/subcategories/{categoryId}', [AjaxController::class, 'getSubcategories'])->name('ajax.subcategories');
        Route::get('/ajax/sub-subcategories/{subcategoryId}', [AjaxController::class, 'getSubSubcategories'])->name('ajax.sub-subcategories');
        Route::post('/ajax/categories', [AjaxController::class, 'storeCategory'])->name('ajax.categories.store');
        Route::post('/ajax/subcategories', [AjaxController::class, 'storeSubcategory'])->name('ajax.subcategories.store');
        Route::post('/ajax/sub-subcategories', [AjaxController::class, 'storeSubSubcategory'])->name('ajax.sub-subcategories.store');

        Route::resource('categories', CategoryController::class);
        Route::resource('subcategories', SubcategoryController::class);
        Route::resource('sub_subcategories', SubSubcategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('customers', CustomerController::class);
        Route::resource('manufacturing-teams', ManufacturingTeamController::class);
        Route::resource('sales-team', SalesTeamController::class);
        
        // Product image deletion route
        Route::delete('/products/{product}/images/{imageId}', [ProductController::class, 'destroyImage'])->name('products.destroy-image');
        
        // Product details routes
        Route::get('/product-details', [ProductController::class, 'productDetails'])->name('product-details');
        Route::post('/product-details', [ProductController::class, 'storeProductDetails'])->name('product-details.store');
        Route::get('/product-details/list', [ProductController::class, 'listProductDetails'])->name('product-details.list');
        Route::get('/product-details/{product}/edit', [ProductController::class, 'editProductDetails'])->name('product-details.edit');
        Route::put('/product-details/{product}', [ProductController::class, 'updateProductDetails'])->name('product-details.update');
        Route::delete('/product-details/{product}', [ProductController::class, 'deleteProductDetails'])->name('product-details.delete');
    });
});

// Sales Team Routes
Route::prefix('sales-team')->name('sales-team.')->group(function () {
    // Sales Team Login Routes
    Route::get('/login', [SalesTeamAuthController::class, 'showLoginForm'])->name('login')->middleware('sales-team.guest');
    Route::post('/login', [SalesTeamAuthController::class, 'login'])->name('login');
    Route::post('/logout', [SalesTeamAuthController::class, 'logout'])->name('logout');
    
    // Sales Team Dashboard Route
    Route::get('/dashboard', [SalesTeamDashboardController::class, 'index'])->name('dashboard')->middleware('auth:sales-team');
    
    // Sales Team Protected Routes
    Route::middleware('auth:sales-team')->group(function () {
        Route::get('/orders', [SalesTeamDashboardController::class, 'orders'])->name('orders.index');
        Route::get('/orders/{order}', [SalesTeamDashboardController::class, 'showOrder'])->name('orders.show');
        Route::get('/customers', [SalesTeamDashboardController::class, 'customers'])->name('customers.index');
        Route::get('/customers/{customer}', [SalesTeamDashboardController::class, 'showCustomer'])->name('customers.show');
        Route::get('/dealer-support', [SalesTeamDashboardController::class, 'dealerSupport'])->name('dealer-support');
        Route::get('/enhanced-dealer-support', [SalesTeamDashboardController::class, 'enhancedDealerSupport'])->name('enhanced-dealer-support');
        Route::post('/select-dealer', [SalesTeamDashboardController::class, 'selectDealer'])->name('select-dealer');
        Route::post('/add-to-cart', [SalesTeamDashboardController::class, 'addToCart'])->name('add-to-cart');
        Route::post('/update-cart', [SalesTeamDashboardController::class, 'updateCart'])->name('update-cart');
        Route::post('/remove-from-cart', [SalesTeamDashboardController::class, 'removeFromCart'])->name('remove-from-cart');
        Route::post('/clear-cart', [SalesTeamDashboardController::class, 'clearCart'])->name('clear-cart');
        Route::post('/place-order-cart', [SalesTeamDashboardController::class, 'placeOrderFromCart'])->name('place-order-cart');
        Route::post('/checkout', [SalesTeamDashboardController::class, 'checkout'])->name('checkout');
        Route::post('/dealer-support/place-order', [SalesTeamDashboardController::class, 'placeOrderForDealer'])->name('dealer-support.place-order');
    });
});

// Manufacturing Team Routes
Route::prefix('manufacturing-team')->name('manufacturing-team.')->group(function () {
    // Manufacturing Team Login Routes
    Route::get('/login', [ManufacturingTeamAuthController::class, 'showLoginForm'])->name('login')->middleware('manufacturing-team.guest');
    Route::post('/login', [ManufacturingTeamAuthController::class, 'login'])->name('login');
    Route::post('/logout', [ManufacturingTeamAuthController::class, 'logout'])->name('logout');
    
    // Manufacturing Team Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth:manufacturing-team');
    
    // Manufacturing Team Order Management Routes
    Route::middleware('auth:manufacturing-team')->group(function () {
        Route::get('/orders/{order}', [DashboardController::class, 'showOrder'])->name('orders.show');
        Route::put('/orders/{order}/status', [DashboardController::class, 'updateOrderStatus'])->name('orders.update-status');
        Route::put('/orders/{order}/partial-complete', [DashboardController::class, 'partialComplete'])->name('orders.partial-complete');
        Route::post('/orders/bulk-accept', [DashboardController::class, 'bulkAcceptOrders'])->name('orders.bulk-accept');
        Route::post('/orders/bulk-update', [DashboardController::class, 'bulkUpdateStatus'])->name('orders.bulk-update');
    });
});