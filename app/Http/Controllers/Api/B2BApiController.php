<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Reward;
use App\Models\RewardClaim;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use App\Models\ProductImage;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class B2BApiController extends Controller
{
    /**
     * Handle B2B customer login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect.'
            ], 422);
        }

        // Check if user is a dealer (B2B customer)
        $customer = Customer::where('email', $request->email)->first();
        
        if (!$customer || $customer->customer_type !== 'dealer') {
            return response()->json([
                'error' => 'This account is not a B2B account.'
            ], 422);
        }

        // Check if the dealer account is active
        if (!$customer->is_active) {
            return response()->json([
                'error' => 'Your B2B account is not approved yet. Please contact admin.'
            ], 422);
        }

        // Revoke all previous tokens for this user
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('B2B Token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'customer_type' => $customer->customer_type,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Handle B2B customer logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Refresh the B2B authentication token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        // Delete the current token
        $request->user()->currentAccessToken()->delete();

        // Create a new token
        $token = $request->user()->createToken('B2B Token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get authenticated B2B user details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();

        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'customer_type' => $customer ? $customer->customer_type : null,
            ]
        ]);
    }

    /**
     * Get B2B dashboard data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        // Get recent orders
        $recentOrders = Order::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get total orders count
        $totalOrders = Order::where('customer_id', $customer->id)->count();
        
        // Get pending return requests
        $pendingReturns = ReturnRequest::whereIn('order_item_id', function($query) use ($customer) {
            $query->select('id')
                  ->from('order_items')
                  ->whereIn('order_id', function($subQuery) use ($customer) {
                      $subQuery->select('id')
                               ->from('orders')
                               ->where('customer_id', $customer->id);
                  });
        })->where('status', 'pending')->count();

        return response()->json([
            'dashboard_data' => [
                'customer' => $customer,
                'total_orders' => $totalOrders,
                'pending_returns' => $pendingReturns,
                'recent_orders' => $recentOrders,
            ]
        ]);
    }

    /**
     * Get B2B products with categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        $categories = Category::with([
            'subcategories' => function ($query) {
                $query->where('is_active', true)->with([
                    'subSubcategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }
                ]);
            }
        ])->where('is_active', true)->get();
        
        $products = Product::where('is_active', true)
            ->with(['images', 'category', 'subcategory', 'subSubcategory'])
            ->paginate(12);

        // Apply B2B discounts
        foreach ($products as $product) {
            $discountPercentage = 0;
            if ($product->category) {
                $discountPercentage = $product->category->b2b_discount ?? 0;
            }
            
            if ($discountPercentage > 0) {
                $discountedPrice = $product->price * (1 - ($discountPercentage / 100));
                $product->discounted_price = $discountedPrice;
                $product->b2b_discount = $discountPercentage;
            }
        }

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Get B2B orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $orders = Order::where('customer_id', $customer->id)
            ->with(['items.product', 'items.product.images'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Get specific B2B order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function order(Request $request, $orderId)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $order = Order::where('customer_id', $customer->id)
            ->where('id', $orderId)
            ->with(['items.product', 'items.product.images', 'items.returnRequests'])
            ->firstOrFail();

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Get B2B profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        return response()->json([
            'customer' => $customer
        ]);
    }

    /**
     * Update B2B profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:50',
        ]);
        
        $customer->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'gst_number' => $request->gst_number,
        ]);
        
        // Also update the User model
        $user = User::where('email', $request->user()->email)->first();
        $user->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Profile updated successfully',
            'customer' => $customer
        ]);
    }

    /**
     * Show return request form for an order item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @param  int  $orderItemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showReturnRequestForm(Request $request, $orderId, $orderItemId)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $order = Order::where('customer_id', $customer->id)
            ->where('id', $orderId)
            ->firstOrFail();
            
        $orderItem = OrderItem::where('id', $orderItemId)
            ->where('order_id', $order->id)
            ->firstOrFail();

        return response()->json([
            'order' => $order,
            'order_item' => $orderItem
        ]);
    }

    /**
     * Submit return request for an order item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @param  int  $orderItemId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitReturnRequest(Request $request, $orderId, $orderItemId)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $request->validate([
            'reason' => 'required|string|max:500',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = Order::where('customer_id', $customer->id)
            ->where('id', $orderId)
            ->firstOrFail();
            
        $orderItem = OrderItem::where('id', $orderItemId)
            ->where('order_id', $order->id)
            ->firstOrFail();

        // Check if quantity is valid
        if ($request->quantity > ($orderItem->quantity - $orderItem->returned_quantity)) {
            return response()->json([
                'error' => 'Return quantity exceeds available quantity'
            ], 422);
        }

        $returnRequest = ReturnRequest::create([
            'order_item_id' => $orderItem->id,
            'customer_id' => $customer->id,
            'reason' => $request->reason,
            'quantity' => $request->quantity,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Return request submitted successfully',
            'return_request' => $returnRequest
        ]);
    }

    /**
     * Show return requests for an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showReturnRequests(Request $request, $orderId)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $order = Order::where('customer_id', $customer->id)
            ->where('id', $orderId)
            ->with(['returnRequests.orderItem.product'])
            ->firstOrFail();

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Show available rewards for claiming.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRewards(Request $request)
    {
        $rewards = Reward::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->get();

        return response()->json([
            'rewards' => $rewards
        ]);
    }

    /**
     * Show claim form for a reward.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $rewardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function showClaimForm(Request $request, $rewardId)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $reward = Reward::findOrFail($rewardId);
        
        // Check if customer has enough loyalty points
        $hasEnoughPoints = $customer->loyalty_points >= $reward->points_required;
        
        // Check if customer has already claimed this reward
        $hasClaimed = RewardClaim::where('customer_id', $customer->id)
            ->where('reward_id', $rewardId)
            ->where('status', 'approved')
            ->exists();

        return response()->json([
            'reward' => $reward,
            'customer_points' => $customer->loyalty_points,
            'has_enough_points' => $hasEnoughPoints,
            'has_claimed' => $hasClaimed,
        ]);
    }

    /**
     * Submit claim for a reward.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $rewardId
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitClaim(Request $request, $rewardId)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $reward = Reward::findOrFail($rewardId);
        
        // Check if customer has enough loyalty points
        if ($customer->loyalty_points < $reward->points_required) {
            return response()->json([
                'error' => 'Insufficient loyalty points to claim this reward'
            ], 422);
        }
        
        // Check if customer has already claimed this reward
        $existingClaim = RewardClaim::where('customer_id', $customer->id)
            ->where('reward_id', $rewardId)
            ->where('status', 'approved')
            ->first();
            
        if ($existingClaim) {
            return response()->json([
                'error' => 'You have already claimed this reward'
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Create reward claim
            $rewardClaim = RewardClaim::create([
                'customer_id' => $customer->id,
                'reward_id' => $rewardId,
                'status' => 'pending',
                'claimed_at' => now(),
            ]);
            
            // Deduct loyalty points (will be added back if claim is rejected)
            $customer->decrement('loyalty_points', $reward->points_required);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Reward claim submitted successfully',
                'reward_claim' => $rewardClaim
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Failed to submit reward claim'
            ], 500);
        }
    }

    /**
     * Show customer's reward claims.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMyClaims(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $claims = RewardClaim::where('customer_id', $customer->id)
            ->with('reward')
            ->orderBy('claimed_at', 'desc')
            ->get();

        return response()->json([
            'claims' => $claims
        ]);
    }
    
    /**
     * Add product to cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get the product
        $product = Product::findOrFail($request->product_id);

        // Check if product is active
        if (!$product->is_active) {
            return response()->json([
                'error' => 'Product is not available'
            ], 422);
        }

        // Check if item already exists in cart
        $cartItem = CartItem::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Update existing cart item
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
            ]);
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'customer_id' => $customer->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'name' => $product->name,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart_item' => $cartItem
        ]);
    }

    /**
     * Get cart contents.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCart(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $cartItems = CartItem::where('customer_id', $customer->id)
            ->with('product')
            ->get();

        // Calculate totals
        $total = 0;
        $itemCount = 0;
        
        foreach ($cartItems as $item) {
            $itemTotal = $item->price * $item->quantity;
            $total += $itemTotal;
            $itemCount += $item->quantity;
        }

        return response()->json([
            'cart_items' => $cartItems,
            'cart_count' => $itemCount,
            'total' => $total
        ]);
    }

    /**
     * Update cart item quantity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCart(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json([
                'error' => 'Item not found in cart'
            ], 404);
        }

        if ($request->quantity <= 0) {
            $cartItem->delete();
        } else {
            $cartItem->update([
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Cart updated successfully',
            'cart_item' => $cartItem
        ]);
    }

    /**
     * Remove item from cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFromCart(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cartItem = CartItem::where('customer_id', $customer->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->delete();
        }

        return response()->json([
            'message' => 'Item removed from cart successfully'
        ]);
    }

    /**
     * Clear cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCart(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        CartItem::where('customer_id', $customer->id)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Place order from cart with delivery address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        // Manual validation instead of using validate() to ensure JSON responses
        if (empty($request->payment_method) || !in_array($request->payment_method, ['cod', 'online'])) {
            return response()->json([
                'error' => 'Payment method is required and must be cod or online'
            ], 422);
        }

        // Check if user wants to use same address as billing
        if ($request->has('same_as_billing') && $request->same_as_billing == true) {
            // Validate that customer has billing address
            if (!$customer->address || !$customer->city || !$customer->state || !$customer->zip_code || !$customer->phone) {
                return response()->json([
                    'error' => 'Billing address information is incomplete. Cannot use same as billing.'
                ], 422);
            }
            
            // Set delivery details to match billing address
            $deliveryAddress = $customer->address;
            $deliveryCity = $customer->city;
            $deliveryState = $customer->state;
            $deliveryPincode = $customer->zip_code;
            $deliveryPhone = $customer->phone;
            $deliveryCountry = 'India'; // Default country
            
            // Also use the same for billing address
            $billingAddress = $customer->address;
            $billingCity = $customer->city;
            $billingState = $customer->state;
            $billingPincode = $customer->zip_code;
            $billingPhone = $customer->phone;
            $billingZip = $customer->zip_code; // Add billing zip
            $billingCountry = 'India'; // Default country
        } else {
            // Manual validation for delivery fields
            if (empty($request->delivery_address)) {
                return response()->json([
                    'error' => 'Delivery address is required'
                ], 422);
            }
            if (empty($request->delivery_city)) {
                return response()->json([
                    'error' => 'Delivery city is required'
                ], 422);
            }
            if (empty($request->delivery_state)) {
                return response()->json([
                    'error' => 'Delivery state is required'
                ], 422);
            }
            if (empty($request->delivery_pincode)) {
                return response()->json([
                    'error' => 'Delivery pincode is required'
                ], 422);
            }
            if (empty($request->delivery_phone)) {
                return response()->json([
                    'error' => 'Delivery phone is required'
                ], 422);
            }
            
            $deliveryAddress = $request->delivery_address;
            $deliveryCity = $request->delivery_city;
            $deliveryState = $request->delivery_state;
            $deliveryPincode = $request->delivery_pincode;
            $deliveryPhone = $request->delivery_phone;
            $deliveryCountry = $request->delivery_country ?? 'India'; // Default to India if not provided
            
            // For now, use same address for billing (can be extended later to accept separate billing address)
            $billingAddress = $request->delivery_address;
            $billingCity = $request->delivery_city;
            $billingState = $request->delivery_state;
            $billingPincode = $request->delivery_pincode;
            $billingPhone = $request->delivery_phone;
            $billingZip = $request->delivery_pincode; // Add billing zip
            $billingCountry = $request->delivery_country ?? 'India'; // Default to India if not provided
        }

        $cartItems = CartItem::where('customer_id', $customer->id)
            ->with('product.category', 'product.subcategory', 'product.subSubcategory')
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'error' => 'Cart is empty'
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Calculate totals first
            $totalAmount = 0;
            $totalDiscount = 0;
            $itemCount = 0;

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                
                // Calculate B2B discount if applicable
                $discountPercentage = 0;
                if ($product->category) {
                    $discountPercentage = $product->category->b2b_discount ?? 0;
                }
                
                $unitPrice = $cartItem->price;
                $discountedPrice = $unitPrice * (1 - ($discountPercentage / 100));
                $itemTotal = $discountedPrice * $cartItem->quantity;
                
                $totalAmount += $itemTotal;
                $totalDiscount += ($unitPrice * $cartItem->quantity) - $itemTotal;
                $itemCount += $cartItem->quantity;
            }

            // Create order with all required fields
            $order = Order::create([
                'customer_id' => $customer->id,
                'customer_type' => $customer->customer_type,
                'order_number' => 'ORD-' . time() . '-' . $customer->id,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'online' ? 'paid' : 'pending',
                'payment_method' => $request->payment_method,
                'billing_address' => $billingAddress,
                'billing_city' => $billingCity,
                'billing_state' => $billingState,
                'billing_country' => $billingCountry,
                'billing_pincode' => $billingPincode,
                'billing_phone' => $billingPhone,
                'billing_zip' => $billingZip, // Add billing zip field
                
                'shipping_address' => $deliveryAddress, // Add shipping fields
                'shipping_city' => $deliveryCity,
                'shipping_state' => $deliveryState,
                'shipping_country' => $deliveryCountry,
                'shipping_pincode' => $deliveryPincode,
                'shipping_phone' => $deliveryPhone,
                'shipping_zip' => $deliveryPincode, // Add shipping zip field
                
                'delivery_address' => $deliveryAddress,
                'delivery_city' => $deliveryCity,
                'delivery_state' => $deliveryState,
                'delivery_pincode' => $deliveryPincode,
                'delivery_phone' => $deliveryPhone,
                'subtotal' => $totalAmount + $totalDiscount, // Add subtotal (original price before discount)
                'total_amount' => $totalAmount, // Final amount after discount
                'total' => $totalAmount, // Also add total field
                'discount_amount' => $totalDiscount,
                'quantity' => $itemCount, // Total quantity of items
            ]);

            // Create order items - include all required fields
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                
                // Calculate B2B discount if applicable
                $discountPercentage = 0;
                if ($product->category) {
                    $discountPercentage = $product->category->b2b_discount ?? 0;
                }
                
                $unitPrice = $cartItem->price;
                $discountedPrice = $unitPrice * (1 - ($discountPercentage / 100));
                $itemTotal = $discountedPrice * $cartItem->quantity;
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $product->name,
                    'price' => $unitPrice,
                    'unit_price' => $unitPrice,
                    'discounted_price' => $discountedPrice,
                    'total_price' => $itemTotal, // Add the total field that was missing
                    'total' => $itemTotal, // Add total field
                    'quantity' => $cartItem->quantity,
                    'discount_percentage' => $discountPercentage,
                    'product_price' => $product->price,
                    'product_image' => $product->image,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category ? $product->category->name : null,
                    'subcategory_id' => $product->subcategory_id,
                    'subcategory_name' => $product->subcategory ? $product->subcategory->name : null,
                    'sub_subcategory_id' => $product->sub_subcategory_id,
                    'sub_subcategory_name' => $product->subSubcategory ? $product->subSubcategory->name : null,
                ]);
            }

            // Clear the cart after successful order
            CartItem::where('customer_id', $customer->id)->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('items.product'),
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'error' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get checkout details (cart + delivery address).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkoutDetails(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $cartItems = CartItem::where('customer_id', $customer->id)
            ->with('product')
            ->get();

        // Calculate totals
        $total = 0;
        $itemCount = 0;
        $totalDiscount = 0;
        
        $formattedCartItems = [];
        
        foreach ($cartItems as $item) {
            if ($item->product) {
                // Calculate B2B discount if applicable
                $discountPercentage = 0;
                if ($item->product->category) {
                    $discountPercentage = $item->product->category->b2b_discount ?? 0;
                }
                
                $unitPrice = $item->price;
                $discountedPrice = $unitPrice * (1 - ($discountPercentage / 100));
                $itemTotal = $discountedPrice * $item->quantity;
                
                $formattedCartItems[] = [
                    'product' => $item->product,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'discounted_price' => $discountedPrice,
                    'discount_percentage' => $discountPercentage,
                    'item_total' => $itemTotal,
                ];
                
                $total += $itemTotal;
                $totalDiscount += ($unitPrice * $item->quantity) - $itemTotal;
                $itemCount += $item->quantity;
            }
        }

        return response()->json([
            'cart_items' => $formattedCartItems,
            'cart_count' => $itemCount,
            'total' => $total,
            'total_discount' => $totalDiscount,
            'customer' => $customer
        ]);
    }
    
    /**
     * Get cart count.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCartCount(Request $request)
    {
        $customer = Customer::where('email', $request->user()->email)->first();
        
        $cartItems = CartItem::where('customer_id', $customer->id)->get();

        $itemCount = 0;
        foreach ($cartItems as $item) {
            $itemCount += $item->quantity;
        }

        return response()->json([
            'cart_count' => $itemCount
        ]);
    }
}
