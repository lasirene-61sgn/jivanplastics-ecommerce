<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\SalesTeam;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SalesTeamController extends Controller
{
    /**
     * Display a listing of the sales team members.
     */
    public function index()
    {
        $salesTeams = SalesTeam::latest()->paginate(10);
        return view('admin.sales-team.index', compact('salesTeams'));
    }

    /**
     * Show the form for creating a new sales team member.
     */
    public function create()
    {
        // Get all dealers to assign to sales team members
        $dealers = Customer::dealers()->get();
        return view('admin.sales-team.create', compact('dealers'));
    }

    /**
     * Store a newly created sales team member in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sales_teams,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:100',
            'assigned_dealers' => 'nullable|array',
            'assigned_dealers.*' => 'exists:customers,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('assigned_dealers', 'password_confirmation');
        
        // Hash the password
        $data['password'] = Hash::make($request->password);
        
        // Let the model handle the array casting
        $data['assigned_dealers'] = $request->assigned_dealers;

        $salesTeam = SalesTeam::create($data);

        return redirect()->route('admin.sales-team.index')
            ->with('success', 'Sales team member created successfully.');
    }

    /**
     * Display the specified sales team member.
     */
    public function show(SalesTeam $salesTeam)
    {
        // Load assigned dealers
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        $assignedDealers = Customer::whereIn('id', $assignedDealerIds)->get();
        
        // Get orders from assigned dealers
        $orders = Order::whereIn('customer_id', $assignedDealerIds)->with('customer')->latest()->paginate(10);
        
        return view('admin.sales-team.show', compact('salesTeam', 'assignedDealers', 'orders'));
    }

    /**
     * Show the form for editing the specified sales team member.
     */
    public function edit(SalesTeam $salesTeam)
    {
        // Get all dealers to assign to sales team members
        $dealers = Customer::dealers()->get();
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        
        return view('admin.sales-team.edit', compact('salesTeam', 'dealers', 'assignedDealerIds'));
    }

    /**
     * Update the specified sales team member in storage.
     */
    public function update(Request $request, SalesTeam $salesTeam)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:sales_teams,email,' . $salesTeam->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'required|string|max:100',
            'assigned_dealers' => 'nullable|array',
            'assigned_dealers.*' => 'exists:customers,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('assigned_dealers', 'password_confirmation');
        
        // Hash the password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        // Let the model handle the array casting
        $data['assigned_dealers'] = $request->assigned_dealers;

        $salesTeam->update($data);

        return redirect()->route('admin.sales-team.index')
            ->with('success', 'Sales team member updated successfully.');
    }

    /**
     * Remove the specified sales team member from storage.
     */
    public function destroy(SalesTeam $salesTeam)
    {
        $salesTeam->delete();

        return redirect()->route('admin.sales-team.index')
            ->with('success', 'Sales team member deleted successfully.');
    }
    
    /**
     * Display orders for a specific sales team member.
     */
    public function orders(SalesTeam $salesTeam)
    {
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        $orders = Order::whereIn('customer_id', $assignedDealerIds)->with('customer')->latest()->paginate(10);
        
        return view('admin.sales-team.orders', compact('salesTeam', 'orders'));
    }
    
    /**
     * Display dealer support interface for a sales team member.
     */
    public function dealerSupport(SalesTeam $salesTeam)
    {
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        $dealers = Customer::whereIn('id', $assignedDealerIds)->get();
        
        return view('admin.sales-team.dealer-support', compact('salesTeam', 'dealers'));
    }
    
    /**
     * Display enhanced dealer support panel with cart functionality.
     */
    public function enhancedDealerSupport(SalesTeam $salesTeam)
    {
        // Get assigned dealers
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
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
        
        return view('admin.sales-team.enhanced-dealer-support', compact(
            'salesTeam', 
            'dealers', 
            'categories', 
            'products'
        ));
    }
    
    /**
     * Select dealer for ordering.
     */
    public function selectDealer(Request $request, SalesTeam $salesTeam)
    {
        $request->validate([
            'dealer_id' => 'required|exists:customers,id'
        ]);
        
        // Check if dealer is assigned to this sales team member
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
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
    public function addToCart(Request $request, SalesTeam $salesTeam)
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
        $cartKey = 'sales_team_cart_' . $salesTeam->id;
        $cart = session($cartKey, []);
        
        $productId = $request->product_id;
        
        // Add or update product in cart
        $cart[$productId] = [
            'name' => $request->product_name,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'min_qty' => $request->min_qty,
            'max_qty' => $request->max_qty
        ];
        
        session([$cartKey => $cart]);
        
        return response()->json(['success' => true, 'message' => 'Product added to cart']);
    }
    
    /**
     * Update product quantity in cart.
     */
    public function updateCart(Request $request, SalesTeam $salesTeam)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $cartKey = 'sales_team_cart_' . $salesTeam->id;
        $cart = session($cartKey, []);
        
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
        session([$cartKey => $cart]);
        
        return response()->json(['success' => true, 'message' => 'Cart updated']);
    }
    
    /**
     * Remove product from cart.
     */
    public function removeFromCart(Request $request, SalesTeam $salesTeam)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        
        $cartKey = 'sales_team_cart_' . $salesTeam->id;
        $cart = session($cartKey, []);
        
        unset($cart[$request->product_id]);
        session([$cartKey => $cart]);
        
        return response()->json(['success' => true, 'message' => 'Product removed from cart']);
    }
    
    /**
     * Clear entire cart.
     */
    public function clearCart(SalesTeam $salesTeam)
    {
        $cartKey = 'sales_team_cart_' . $salesTeam->id;
        session()->forget($cartKey);
        
        return response()->json(['success' => true, 'message' => 'Cart cleared']);
    }
    
    /**
     * Place order from cart.
     */
    public function placeOrderFromCart(Request $request, SalesTeam $salesTeam)
    {
        $request->validate([
            'dealer_id' => 'required|exists:customers,id'
        ]);
        
        // Check if dealer is assigned to this sales team member
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        if (!in_array($request->dealer_id, $assignedDealerIds)) {
            return redirect()->back()->with('error', 'You are not authorized to place orders for this dealer.');
        }
        
        // Get cart
        $cartKey = 'sales_team_cart_' . $salesTeam->id;
        $cart = session($cartKey, []);
        
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
        session()->forget($cartKey);
        session()->forget('selected_dealer_id');
        
        return redirect()->back()->with('success', 'Order placed successfully for dealer ' . $dealer->name . '. Order number: ' . $order->order_number);
    }
    
    /**
     * Place an order on behalf of a dealer.
     */
    public function placeOrderForDealer(Request $request, SalesTeam $salesTeam)
    {
        $request->validate([
            'dealer_id' => 'required|exists:customers,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
        ]);
        
        // Check if dealer is assigned to this sales team member
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
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
        
        // Create order
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
        
        return redirect()->back()->with('success', 'Order placed successfully for dealer ' . $dealer->name . '. Order number: ' . $order->order_number);
    }
    
    /**
     * Get order analytics for a sales team member.
     */
    public function orderAnalytics(SalesTeam $salesTeam)
    {
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        
        // Get order statistics
        $totalOrders = Order::whereIn('customer_id', $assignedDealerIds)->count();
        $completedOrders = Order::whereIn('customer_id', $assignedDealerIds)->where('status', 'completed')->count();
        $pendingOrders = Order::whereIn('customer_id', $assignedDealerIds)->where('status', 'pending')->count();
        $totalRevenue = Order::whereIn('customer_id', $assignedDealerIds)->sum('total');
        
        // Get recent orders
        $recentOrders = Order::whereIn('customer_id', $assignedDealerIds)->with('customer')->latest()->take(10)->get();
        
        // Get top products
        $topProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.customer_id', $assignedDealerIds)
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->take(10)
            ->get();
        
        return view('admin.sales-team.analytics', compact(
            'salesTeam', 
            'totalOrders', 
            'completedOrders', 
            'pendingOrders', 
            'totalRevenue', 
            'recentOrders', 
            'topProducts'
        ));
    }
    
    /**
     * Display customer relationship management interface.
     */
    public function customerRelationships(SalesTeam $salesTeam)
    {
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        $dealers = Customer::whereIn('id', $assignedDealerIds)->withCount('orders')->get();
        
        // Get customer interaction statistics
        $totalCustomers = $dealers->count();
        $activeCustomers = $dealers->filter(function ($dealer) {
            return $dealer->orders_count > 0;
        })->count();
        
        $inactiveCustomers = $totalCustomers - $activeCustomers;
        
        // Get customer order value statistics
        $customerValues = DB::table('orders')
            ->whereIn('customer_id', $assignedDealerIds)
            ->select('customer_id', DB::raw('SUM(total) as total_spent'))
            ->groupBy('customer_id')
            ->get();
        
        $highValueCustomers = $customerValues->filter(function ($item) {
            return $item->total_spent > 10000; // High value customers (> ₹10,000)
        })->count();
        
        $mediumValueCustomers = $customerValues->filter(function ($item) {
            return $item->total_spent >= 5000 && $item->total_spent <= 10000; // Medium value customers
        })->count();
        
        $lowValueCustomers = $customerValues->filter(function ($item) {
            return $item->total_spent < 5000; // Low value customers
        })->count();
        
        return view('admin.sales-team.customer-relationships', compact(
            'salesTeam',
            'dealers',
            'totalCustomers',
            'activeCustomers',
            'inactiveCustomers',
            'highValueCustomers',
            'mediumValueCustomers',
            'lowValueCustomers'
        ));
    }
    
    /**
     * Show customer details and order history.
     */
    public function customerDetails(SalesTeam $salesTeam, Customer $customer)
    {
        // Check if customer is assigned to this sales team member
        $assignedDealerIds = $salesTeam->getAssignedDealersList();
        if (!in_array($customer->id, $assignedDealerIds)) {
            return redirect()->route('admin.sales-team.customer-relationships', $salesTeam)
                ->with('error', 'You are not authorized to view this customer.');
        }
        
        // Get customer orders
        $orders = Order::where('customer_id', $customer->id)->with('items')->latest()->paginate(10);
        
        // Calculate customer statistics
        $totalOrders = $orders->total();
        $totalSpent = Order::where('customer_id', $customer->id)->sum('total');
        $lastOrderDate = Order::where('customer_id', $customer->id)->latest()->first()?->created_at;
        
        return view('admin.sales-team.customer-details', compact(
            'salesTeam',
            'customer',
            'orders',
            'totalOrders',
            'totalSpent',
            'lastOrderDate'
        ));
    }
}