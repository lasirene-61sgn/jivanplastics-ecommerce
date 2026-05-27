<?php

namespace App\Http\Controllers\SalesTeam;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Display the sales team dashboard.
     */
    public function index()
    {
        $salesTeam = Auth::guard('sales-team')->user();
        
        // Get assigned dealers
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        $assignedDealers = Customer::whereIn('id', $assignedDealerIds)->get();
        
        // Get order statistics
        $totalOrders = Order::whereIn('customer_id', $assignedDealerIds)->count();
        $completedOrders = Order::whereIn('customer_id', $assignedDealerIds)->where('status', 'completed')->count();
        $pendingOrders = Order::whereIn('customer_id', $assignedDealerIds)->where('status', 'pending')->count();
        $totalRevenue = Order::whereIn('customer_id', $assignedDealerIds)->sum('total');
        
        // Get recent orders
        $recentOrders = Order::whereIn('customer_id', $assignedDealerIds)
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();
        
        return view('sales-team.dashboard', compact(
            'salesTeam',
            'assignedDealers',
            'totalOrders',
            'completedOrders',
            'pendingOrders',
            'totalRevenue',
            'recentOrders'
        ));
    }
    
    /**
     * Display orders for the sales team member.
     */
    public function orders()
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        $orders = Order::whereIn('customer_id', $assignedDealerIds)
            ->with('customer')
            ->latest()
            ->paginate(10);
        
        return view('sales-team.orders', compact('salesTeam', 'orders'));
    }
    
    /**
     * Display a specific order.
     */
    public function showOrder(Order $order)
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        
        // Check if the order belongs to an assigned dealer
        if (!in_array($order->customer_id, $assignedDealerIds)) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        $order->load('items.product');
        
        return view('sales-team.orders.show', compact('salesTeam', 'order'));
    }
    
    /**
     * Display customer relationship management.
     */
    public function customers()
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        $customers = Customer::whereIn('id', $assignedDealerIds)
            ->withCount('orders')
            ->get();
        
        return view('sales-team.customers', compact('salesTeam', 'customers'));
    }
    
    /**
     * Display a specific customer.
     */
    public function showCustomer(Customer $customer)
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        
        // Check if the customer is assigned to this sales team member
        if (!in_array($customer->id, $assignedDealerIds)) {
            abort(403, 'Unauthorized access to this customer.');
        }
        
        $orders = Order::where('customer_id', $customer->id)
            ->with('items')
            ->latest()
            ->paginate(10);
        
        $totalOrders = $orders->total();
        $totalSpent = Order::where('customer_id', $customer->id)->sum('total');
        $lastOrderDate = Order::where('customer_id', $customer->id)->latest()->first()?->created_at;
        
        return view('sales-team.customers.show', compact(
            'salesTeam',
            'customer',
            'orders',
            'totalOrders',
            'totalSpent',
            'lastOrderDate'
        ));
    }
    
    /**
     * Display enhanced dealer support panel with cart functionality.
     */
    public function enhancedDealerSupport()
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        $dealers = Customer::whereIn('id', $assignedDealerIds)->get();
        
        // Get categories for filtering
        $categories = Category::where('is_active', true)->with([
            'subcategories' => function ($query) {
                $query->where('is_active', true)->with([
                    'subSubcategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }
                ]);
            }
        ])->get();
        
        // Get products with search/filter
        $productsQuery = Product::where('is_active', true)->with('images');
        
        if (request('search')) {
            $productsQuery->where(function($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('sku', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request('category')) {
            $productsQuery->where('category_id', request('category'));
        }
        
        $products = $productsQuery->paginate(12);
        
        return view('sales-team.enhanced-dealer-support', compact(
            'salesTeam', 
            'dealers', 
            'categories', 
            'products'
        ));
    }
    
    /**
     * Select dealer for ordering.
     */
    public function selectDealer(Request $request)
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        
        $request->validate([
            'dealer_id' => 'required|exists:customers,id'
        ]);
        
        // Check if dealer is assigned to this sales team member
        if (!in_array($request->dealer_id, $assignedDealerIds)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized dealer']);
        }
        
        // Store selected dealer in session
        session(['selected_dealer_id' => $request->dealer_id]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Add product to sales team cart.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'min_qty' => 'required|integer|min:1',
            'max_qty' => 'nullable|integer|min:1'
        ]);
        
        // Validate quantity against min/max
        if ($request->quantity < $request->min_qty) {
            return response()->json([
                'success' => false, 
                'message' => "Minimum order quantity is {$request->min_qty}"
            ]);
        }
        
        if ($request->max_qty && $request->quantity > $request->max_qty) {
            return response()->json([
                'success' => false, 
                'message' => "Maximum order quantity is {$request->max_qty}"
            ]);
        }
        
        // Get cart from session
        $cart = session('sales_team_portal_cart', []);
        
        $productId = $request->product_id;
        
        // Add or update product in cart
        $cart[$productId] = [
            'name' => $request->product_name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'min_qty' => $request->min_qty,
            'max_qty' => $request->max_qty
        ];
        
        session(['sales_team_portal_cart' => $cart]);
        
        return response()->json(['success' => true, 'message' => 'Product added to cart']);
    }
    
    /**
     * Update product quantity in cart.
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $cart = session('sales_team_portal_cart', []);
        
        $productId = $request->product_id;
        
        if (!isset($cart[$productId])) {
            return response()->json(['success' => false, 'message' => 'Product not in cart']);
        }
        
        // Validate quantity against min/max
        $minQty = $cart[$productId]['min_qty'];
        $maxQty = $cart[$productId]['max_qty'];
        
        if ($request->quantity < $minQty) {
            return response()->json([
                'success' => false, 
                'message' => "Minimum order quantity is {$minQty}"
            ]);
        }
        
        if ($maxQty && $request->quantity > $maxQty) {
            return response()->json([
                'success' => false, 
                'message' => "Maximum order quantity is {$maxQty}"
            ]);
        }
        
        // Update quantity
        $cart[$productId]['quantity'] = $request->quantity;
        session(['sales_team_portal_cart' => $cart]);
        
        return response()->json(['success' => true, 'message' => 'Cart updated']);
    }
    
    /**
     * Remove product from cart.
     */
    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        
        $cart = session('sales_team_portal_cart', []);
        
        unset($cart[$request->product_id]);
        session(['sales_team_portal_cart' => $cart]);
        
        return response()->json(['success' => true, 'message' => 'Product removed from cart']);
    }
    
    /**
     * Clear entire cart.
     */
    public function clearCart()
    {
        session()->forget('sales_team_portal_cart');
        
        return response()->json(['success' => true, 'message' => 'Cart cleared']);
    }
    
    /**
     * Place order from cart.
     */
    public function placeOrderFromCart(Request $request)
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        
        $request->validate([
            'dealer_id' => 'required|exists:customers,id'
        ]);
        
        // Check if dealer is assigned to this sales team member
        if (!in_array($request->dealer_id, $assignedDealerIds)) {
            return redirect()->back()->with('error', 'You are not authorized to place orders for this dealer.');
        }
        
        // Get cart
        $cart = session('sales_team_portal_cart', []);
        
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty. Please add products first.');
        }
        
        // Get the dealer
        $dealer = Customer::findOrFail($request->dealer_id);
        
        // Create order items array
        $orderItems = [];
        $subtotal = 0;
        $totalGst = 0;
        
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            $quantity = $item['quantity'];
            
            if (!$product || !$quantity) continue;
            
            $itemTotal = $product->price * $quantity;
            $gstAmount = $itemTotal * $product->gst_percentage / 100;
            
            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku ?? null,
                'quantity' => $quantity,
                'price' => $product->price,
                'total' => $itemTotal,
                'gst_amount' => $gstAmount
            ];
            
            $subtotal += $itemTotal;
            $totalGst += $gstAmount;
        }
        
        if (empty($orderItems)) {
            return redirect()->back()->with('error', 'No valid products in cart.');
        }
        
        // Calculate final total
        $shipping = 0; // Free shipping
        $total = $subtotal + $shipping + $totalGst;
        
        // Create order with proper handling of nullable fields
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => $dealer->id,
            'customer_type' => $dealer->customer_type,
            'subtotal' => $subtotal,
            'tax' => $totalGst,
            'shipping' => $shipping,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => 'cod', // Cash on delivery for sales team orders
            'billing_address' => $dealer->address ?? '',
            'billing_city' => $dealer->city ?? '',
            'billing_state' => $dealer->state ?? '',
            'billing_zip' => $dealer->zip ?? '000000', // Provide default zip if null
            'billing_country' => $dealer->country ?? 'India',
            'shipping_address' => $dealer->address ?? '',
            'shipping_city' => $dealer->city ?? '',
            'shipping_state' => $dealer->state ?? '',
            'shipping_zip' => $dealer->zip ?? '000000', // Provide default zip if null
            'shipping_country' => $dealer->country ?? 'India',
        ]);
        
        // Create order items
        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }
        
        // Clear cart
        session()->forget('sales_team_portal_cart');
        session()->forget('selected_dealer_id');
        
        return redirect()->route('sales-team.enhanced-dealer-support')->with('success', 'Order placed successfully for dealer ' . $dealer->name . '. Order number: ' . $order->order_number);
    }
    
    /**
     * Handle checkout process.
     */
    public function checkout(Request $request)
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        
        // Validate dealer selection
        $request->validate([
            'dealer_id' => 'required|exists:customers,id'
        ]);
        
        // Check if dealer is assigned to this sales team member
        if (!in_array($request->dealer_id, $assignedDealerIds)) {
            return redirect()->back()->with('error', 'You are not authorized to place orders for this dealer.');
        }
        
        // Get cart
        $cart = session('sales_team_portal_cart', []);
        
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty. Please add products first.');
        }
        
        // Get the dealer
        $dealer = Customer::findOrFail($request->dealer_id);
        
        // Create order items array
        $orderItems = [];
        $subtotal = 0;
        $totalGst = 0;
        
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product) continue;
            
            $itemTotal = $product->price * $item['quantity'];
            $gstAmount = $itemTotal * $product->gst_percentage / 100;
            
            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku ?? null,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'total' => $itemTotal,
                'gst_amount' => $gstAmount
            ];
            
            $subtotal += $itemTotal;
            $totalGst += $gstAmount;
        }
        
        if (empty($orderItems)) {
            return redirect()->back()->with('error', 'No valid products in cart.');
        }
        
        // Calculate totals
        $tax = $totalGst;
        $total = $subtotal + $tax;
        
        // Create order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => $dealer->id,
            'customer_type' => 'dealer',
            'sales_team_id' => $salesTeam->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => 0,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => 'cod',
            'billing_address' => $dealer->address ?? '',
            'billing_city' => $dealer->city ?? '',
            'billing_state' => $dealer->state ?? '',
            'billing_zip' => $dealer->zip ?? '000000',
            'billing_country' => $dealer->country ?? 'India',
            'shipping_address' => $dealer->address ?? '',
            'shipping_city' => $dealer->city ?? '',
            'shipping_state' => $dealer->state ?? '',
            'shipping_zip' => $dealer->zip ?? '000000',
            'shipping_country' => $dealer->country ?? 'India',
        ]);
        
        // Create order items
        foreach ($orderItems as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total']
            ]);
        }
        
        // Clear cart and session
        session()->forget('sales_team_portal_cart');
        session()->forget('selected_dealer_id');
        
        return redirect()->route('sales-team.dealer-support')->with('success', 'Order placed successfully for ' . $dealer->company_name . '! Order number: ' . $order->order_number);
    }
    
    /**
     * Display the dealer support interface.
     */
    public function dealerSupport()
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        $dealers = Customer::whereIn('id', $assignedDealerIds)->get();
        
        // Get categories for filtering
        $categories = Category::where('is_active', true)->with([
            'subcategories' => function ($query) {
                $query->where('is_active', true)->with([
                    'subSubcategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }
                ]);
            }
        ])->get();
        
        // Get products with search/filter
        $productsQuery = Product::where('is_active', true)->with('images');
        
        if (request('search')) {
            $productsQuery->where(function($query) {
                $query->where('name', 'like', '%' . request('search') . '%')
                      ->orWhere('sku', 'like', '%' . request('search') . '%');
            });
        }
        
        if (request('category')) {
            $productsQuery->where('category_id', request('category'));
        }
        
        $products = $productsQuery->paginate(12);
        
        return view('sales-team.dealer-support', compact('salesTeam', 'dealers', 'categories', 'products'));
    }
    
    /**
     * Place an order on behalf of a dealer.
     */
    public function placeOrderForDealer(Request $request)
    {
        $salesTeam = Auth::guard('sales-team')->user();
        $assignedDealerIds = is_array($salesTeam->assigned_dealers) ? $salesTeam->assigned_dealers : [];
        
        $request->validate([
            'dealer_id' => 'required|exists:customers,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
        ]);
        
        // Check if dealer is assigned to this sales team member
        if (!in_array($request->dealer_id, $assignedDealerIds)) {
            return redirect()->back()->with('error', 'You are not authorized to place orders for this dealer.');
        }
        
        // Get the dealer
        $dealer = Customer::findOrFail($request->dealer_id);
        
        // Create order items array
        $orderItems = [];
        $subtotal = 0;
        $totalGst = 0;
        
        foreach ($request->product_ids as $index => $productId) {
            if (!$productId) continue;
            
            $product = Product::find($productId);
            $quantity = $request->quantities[$index];
            
            if (!$product || !$quantity) continue;
            
            $itemTotal = $product->price * $quantity;
            $gstAmount = $itemTotal * $product->gst_percentage / 100;
            
            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku ?? null,
                'quantity' => $quantity,
                'price' => $product->price,
                'total' => $itemTotal,
                'gst_amount' => $gstAmount
            ];
            
            $subtotal += $itemTotal;
            $totalGst += $gstAmount;
        }
        
        // Calculate final total
        $shipping = 0; // Free shipping
        $total = $subtotal + $shipping + $totalGst;
        
        // Create order with proper handling of nullable fields
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => $dealer->id,
            'customer_type' => $dealer->customer_type,
            'subtotal' => $subtotal,
            'tax' => $totalGst,
            'shipping' => $shipping,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => 'cod', // Cash on delivery for sales team orders
            'billing_address' => $dealer->address ?? '',
            'billing_city' => $dealer->city ?? '',
            'billing_state' => $dealer->state ?? '',
            'billing_zip' => $dealer->zip ?? '000000', // Provide default zip if null
            'billing_country' => $dealer->country ?? 'India',
            'shipping_address' => $dealer->address ?? '',
            'shipping_city' => $dealer->city ?? '',
            'shipping_state' => $dealer->state ?? '',
            'shipping_zip' => $dealer->zip ?? '000000', // Provide default zip if null
            'shipping_country' => $dealer->country ?? 'India',
        ]);
        
        // Create order items
        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }
        
        return redirect()->route('sales-team.dealer-support')->with('success', 'Order placed successfully for dealer ' . $dealer->name . '. Order number: ' . $order->order_number);
    }
}