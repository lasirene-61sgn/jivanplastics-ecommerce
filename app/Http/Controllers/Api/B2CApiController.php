<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class B2CApiController extends Controller
{
    /**
     * Login B2C customer.
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

        $customer = Customer::where('email', $request->email)->where('customer_type', 'individual')->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        if (!$customer->is_active) {
            return response()->json([
                'error' => 'Account is deactivated'
            ], 403);
        }

        $token = $customer->createToken('B2CToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'customer' => $customer,
            'token' => $token
        ]);
    }

    /**
     * Logout B2C customer.
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
     * Refresh B2C customer token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $customer = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $customer->createToken('B2CToken')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'customer' => $customer,
            'token' => $token
        ]);
    }

    /**
     * Get authenticated B2C customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'customer' => $request->user()
        ]);
    }

    /**
     * Get B2C dashboard data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        $customer = $request->user();

        $recentOrders = Order::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $totalOrders = Order::where('customer_id', $customer->id)->count();

        return response()->json([
            'customer' => $customer,
            'recent_orders' => $recentOrders,
            'total_orders' => $totalOrders
        ]);
    }

    /**
     * Get all products for B2C.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        $products = Product::where('is_active', true)
            ->with(['category', 'subcategory', 'subSubcategory', 'images'])
            ->paginate(15);

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Get products by category for B2C.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsByCategory(Request $request, $categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['category', 'subcategory', 'subSubcategory', 'images'])
            ->paginate(15);

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Get products by subcategory for B2C.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $subcategoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsBySubcategory(Request $request, $subcategoryId)
    {
        $products = Product::where('subcategory_id', $subcategoryId)
            ->where('is_active', true)
            ->with(['category', 'subcategory', 'subSubcategory', 'images'])
            ->paginate(15);

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Get products by sub subcategory for B2C.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $subSubcategoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsBySubSubcategory(Request $request, $subSubcategoryId)
    {
        $products = Product::where('sub_subcategory_id', $subSubcategoryId)
            ->where('is_active', true)
            ->with(['category', 'subcategory', 'subSubcategory', 'images'])
            ->paginate(15);

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Get product details for B2C.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function productDetails(Request $request, $id)
    {
        $product = Product::with(['category', 'subcategory', 'subSubcategory', 'images'])->find($id);

        if (!$product || !$product->is_active) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Get B2C customer orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request)
    {
        $customer = $request->user();

        $orders = Order::where('customer_id', $customer->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Get B2C customer order details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderDetails(Request $request, $orderId)
    {
        $customer = $request->user();

        $order = Order::where('customer_id', $customer->id)
            ->where('id', $orderId)
            ->with('items.product')
            ->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Get B2C customer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $customer = $request->user();

        return response()->json([
            'customer' => $customer
        ]);
    }

    /**
     * Update B2C customer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $customer = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'city' => 'sometimes|string|max:100',
            'state' => 'sometimes|string|max:100',
            'zip_code' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
        ]);

        $customer->update($request->only(['name', 'phone', 'address', 'city', 'state', 'zip_code', 'country']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'customer' => $customer
        ]);
    }

    /**
     * Change B2C customer password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $customer = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json([
                'error' => 'Current password is incorrect'
            ], 400);
        }

        $customer->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
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
        $customer = $request->user();
        
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
     * Add product to cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        $customer = $request->user();
        
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
     * Update cart item quantity.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCart(Request $request)
    {
        $customer = $request->user();
        
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
        $customer = $request->user();
        
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
        $customer = $request->user();
        
        CartItem::where('customer_id', $customer->id)->delete();

        return response()->json([
            'message' => 'Cart cleared successfully'
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
        $customer = $request->user();
        
        $cartItems = CartItem::where('customer_id', $customer->id)->get();

        $itemCount = 0;
        foreach ($cartItems as $item) {
            $itemCount += $item->quantity;
        }

        return response()->json([
            'cart_count' => $itemCount
        ]);
    }

    /**
     * Get checkout details (cart + delivery address).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkoutDetails(Request $request)
    {
        $customer = $request->user();
        
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
                // Calculate B2C discount if applicable (using dealer discount for B2C)
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
     * Place order from cart with delivery address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeOrder(Request $request)
    {
        $customer = $request->user();
        
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
                
                // Calculate B2C discount if applicable
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
                
                // Calculate B2C discount if applicable
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
}