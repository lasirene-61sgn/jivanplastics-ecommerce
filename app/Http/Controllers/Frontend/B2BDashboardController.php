<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\Reward;
use App\Models\RewardClaim;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\ReturnNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class B2BDashboardController extends Controller
{
    /**
     * Display the B2B dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access the B2B dashboard.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Get some statistics for the dashboard
        $totalProducts = Product::where('is_active', true)->count();
        $recentProducts = Product::where('is_active', true)->with('images')->latest()->take(5)->get();
        $totalOrders = Order::where('customer_id', $customer->id)->count();
        $pendingOrders = Order::where('customer_id', $customer->id)->where('status', 'pending')->count();
        $completedOrders = Order::where('customer_id', $customer->id)->where('status', 'completed')->count();

        return view('frontend.b2b.dashboard', compact('customer', 'totalProducts', 'recentProducts', 'totalOrders', 'pendingOrders', 'completedOrders'));
    }

    public function products(Request $request)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access the B2B product catalog.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Get categories with subcategories and sub-subcategories for the sidebar
        $categories = Category::with([
            'subcategories' => function ($query) {
                $query->where('is_active', true)->with([
                    'subSubcategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }
                ]);
            }
        ])->where('is_active', true)->get();

        // Start Product Query
        $query = Product::where('is_active', true);

        // Filter by Search
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm)
                    ->orWhere('id', 'like', $searchTerm)
                    ->orWhere('slug', 'like', $searchTerm)
                    ->orWhere('size', 'like', $searchTerm)
                    ->orWhere('thickness', 'like', $searchTerm)
                    ->orWhere('color', 'like', $searchTerm);
            });
        }

        // Filter by Categories
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('subcategory')) {
            $query->where('subcategory_id', $request->subcategory);
        }
        if ($request->filled('sub_subcategory')) {
            $query->where('sub_subcategory_id', $request->sub_subcategory);
        }

        // Drill-down Logic: Determine what to display (Categories or Products)
        $displayMode = 'products'; // Default to products
        $childCategories = collect();

        if (!$request->filled('search') && !$request->filled('size') && !$request->filled('thickness') && !$request->filled('color')) {
            if (!$request->filled('category')) {
                $displayMode = 'categories';
                $childCategories = $categories;
            } else {
                // User requested to show products immediately when category/subcategory is clicked
                $displayMode = 'products';
            }
        }

        // Get Available Attributes BASED ON CURRENT FILTERS (if in product mode)
        $availableSizes = collect();
        $availableThicknesses = collect();
        $availableColors = collect();

        if ($displayMode === 'products') {
            $attributeQuery = clone $query;

            $availableSizes = $attributeQuery->whereNotNull('size')->where('size', '!=', '')
                ->pluck('size')
                ->flatMap(fn($item) => explode(',', $item))
                ->unique()->sort()->values();

            $availableThicknesses = (clone $attributeQuery)->whereNotNull('thickness')->where('thickness', '!=', '')
                ->pluck('thickness')
                ->flatMap(fn($item) => explode(',', $item))
                ->unique()->sort()->values();

            $availableColors = (clone $attributeQuery)->whereNotNull('color')->where('color', '!=', '')
                ->pluck('color')
                ->flatMap(fn($item) => explode(',', $item))
                ->unique()->sort()->values();

            // Filter by Attributes
            if ($request->filled('size')) {
                $query->where(function ($q) use ($request) {
                    $q->where('size', $request->size)
                        ->orWhere('size', 'like', $request->size . ',%')
                        ->orWhere('size', 'like', '%,' . $request->size)
                        ->orWhere('size', 'like', '%,' . $request->size . ',%');
                });
            }
            if ($request->filled('thickness')) {
                $query->where(function ($q) use ($request) {
                    $q->where('thickness', $request->thickness)
                        ->orWhere('thickness', 'like', $request->thickness . ',%')
                        ->orWhere('thickness', 'like', '%,' . $request->thickness)
                        ->orWhere('thickness', 'like', '%,' . $request->thickness . ',%');
                });
            }
            if ($request->filled('color')) {
                $query->where(function ($q) use ($request) {
                    $q->where('color', $request->color)
                        ->orWhere('color', 'like', $request->color . ',%')
                        ->orWhere('color', 'like', '%,' . $request->color)
                        ->orWhere('color', 'like', '%,' . $request->color . ',%');
                });
            }
        }

        // Finalize results
        $products = $query->with(['images', 'variations', 'category', 'subcategory', 'subSubcategory'])->latest()->paginate(12)->withQueryString();

        return view('frontend.b2b.products', compact(
            'products',
            'categories',
            'customer',
            'availableSizes',
            'availableThicknesses',
            'availableColors',
            'displayMode',
            'childCategories'
        ));
    }

    /**
     * Display the B2B orders page.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Display the specified product details.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function showProduct(Product $product)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view product details.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Load product relationships
        $product->load(['images', 'category', 'subcategory', 'subSubcategory']);

        return view('frontend.b2b.product-details', compact('product', 'customer'));
    }

    public function orders()
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your orders.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();
        // Load orders with items and product information, including dispatched quantities
        $orders = Order::where('customer_id', $customer->id)
            ->with(['items.product.images', 'items.returnRequests', 'items'])
            ->latest()
            ->paginate(10);

        return view('frontend.b2b.orders', compact('orders', 'customer'));
    }

    /**
     * Display the details of a specific order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function showOrder(Order $order)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your order.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('b2b.orders')->with('error', 'You are not authorized to view this order.');
        }

        // Load order items with product information and return requests
        $order->load(['items.product.images', 'items.returnRequests', 'items', 'returnRequests.invoice', 'returnRequests.orderItem']);

        return view('frontend.b2b.order-details', compact('order', 'customer'));
    }

    /**
     * Display the invoice for a specific order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    /**
     * Display the invoice for a specific order.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\View\View
     */
    public function showInvoice(Order $order, Invoice $invoice)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your invoice.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('b2b.orders')->with('error', 'You are not authorized to view this invoice.');
        }

        $invoice->load(['items.orderItem.product', 'order.customer']);

        return view('frontend.b2b.invoice', compact('order', 'invoice', 'customer'));
    }

    /**
     * Display the B2B profile page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your profile.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        return view('frontend.b2b.profile', compact('customer'));
    }

    /**
     * Update the B2B profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to update your profile.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
        ]);

        $customer->update($request->all());

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the form for requesting a return or replacement.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\View\View
     */
    public function showReturnRequestForm(Order $order, OrderItem $orderItem)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to request a return.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('b2b.orders')->with('error', 'You are not authorized to request a return for this order.');
        }

        // Ensure the order item belongs to the order
        if ($orderItem->order_id != $order->id) {
            return redirect()->route('b2b.orders')->with('error', 'Invalid order item.');
        }

        // Ensure the item has been dispatched
        if ($orderItem->dispatched_quantity <= 0) {
            return redirect()->back()->with('error', 'This item has not been dispatched yet and cannot be returned.');
        }

        // Check if there's already a pending return request for this item
        $existingRequest = ReturnRequest::where('order_item_id', $orderItem->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'There is already a pending return request for this item.');
        }

        return view('frontend.b2b.return-request-form', compact('order', 'orderItem', 'customer'));
    }

    /**
     * Submit a return or replacement request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @param  \App\Models\OrderItem  $orderItem
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitReturnRequest(Request $request, Order $order, OrderItem $orderItem)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to request a return.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('b2b.orders')->with('error', 'You are not authorized to request a return for this order.');
        }

        // Ensure the order item belongs to the order
        if ($orderItem->order_id != $order->id) {
            return redirect()->route('b2b.orders')->with('error', 'Invalid order item.');
        }

        // Ensure the item has been dispatched
        if ($orderItem->dispatched_quantity <= 0) {
            return redirect()->back()->with('error', 'This item has not been dispatched yet and cannot be returned.');
        }

        // Validate the request
        $request->validate([
            'type' => 'required|in:return,replacement',
            'pieces' => 'required|integer|min:1|max:' . ($orderItem->dispatched_quantity * $orderItem->per_unit_pieces),
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'damage_proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'another_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        $damageProofPath = null;
        if ($request->hasFile('damage_proof_image')) {
            $damageProofPath = $request->file('damage_proof_image')->store('return_proofs', 'public');
        }

        $anotherImagePath = null;
        if ($request->hasFile('another_image')) {
            $anotherImagePath = $request->file('another_image')->store('return_proofs', 'public');
        }

        // Check if there's already a pending return request for this item
        $existingRequest = ReturnRequest::where('order_item_id', $orderItem->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'There is already a pending return request for this item.');
        }

        // Create the return request
        ReturnRequest::create([
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'customer_id' => $customer->id,
            'type' => $request->type,
            'quantity' => 0,
            'pieces' => $request->pieces,
            'reason' => $request->reason,
            'description' => $request->description,
            'damage_proof_image' => $damageProofPath,
            'another_image' => $anotherImagePath,
            'status' => 'pending',
        ]);

        return redirect()->route('b2b.orders.show', $order)->with('success', 'Return request submitted successfully. Our team will review your request shortly.');
    }

    /**
     * Display the return requests for a specific order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function showReturnRequests(Order $order)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your return requests.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('b2b.orders')->with('error', 'You are not authorized to view return requests for this order.');
        }

        // Load order with return requests
        $order->load(['returnRequests.orderItem.product']);

        return view('frontend.b2b.return-requests', compact('order', 'customer'));
    }

    /**
     * Display available rewards for claiming.
     *
     * @return \Illuminate\View\View
     */
    public function showRewards()
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view rewards.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Get active rewards
        $rewards = Reward::where('is_active', true)->with('product')->get();

        return view('frontend.b2b.rewards', compact('customer', 'rewards'));
    }

    /**
     * Show the form for claiming a reward.
     *
     * @param  \App\Models\Reward  $reward
     * @return \Illuminate\View\View
     */
    public function showClaimForm(Reward $reward)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to claim a reward.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Check if reward is active
        if (!$reward->is_active) {
            return redirect()->route('b2b.rewards.index')->with('error', 'This reward is no longer available.');
        }

        // Check if customer has enough points
        if ($customer->loyalty_points < $reward->required_points) {
            return redirect()->route('b2b.rewards.index')->with('error', 'You do not have enough loyalty points to claim this reward.');
        }

        // Check if customer has already claimed this reward and it's pending
        $existingClaim = RewardClaim::where('customer_id', $customer->id)
            ->where('reward_id', $reward->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingClaim) {
            return redirect()->route('b2b.rewards.index')->with('error', 'You have already claimed this reward.');
        }

        return view('frontend.b2b.claim-reward', compact('customer', 'reward'));
    }

    /**
     * Submit a reward claim.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reward  $reward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitClaim(Request $request, Reward $reward)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to claim a reward.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Check if reward is active
        if (!$reward->is_active) {
            return redirect()->route('b2b.rewards.index')->with('error', 'This reward is no longer available.');
        }

        // Check if customer has enough points
        if ($customer->loyalty_points < $reward->required_points) {
            return redirect()->route('b2b.rewards.index')->with('error', 'You do not have enough loyalty points to claim this reward.');
        }

        // Check if customer has already claimed this reward and it's pending
        $existingClaim = RewardClaim::where('customer_id', $customer->id)
            ->where('reward_id', $reward->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingClaim) {
            return redirect()->route('b2b.rewards.index')->with('error', 'You have already claimed this reward.');
        }

        // Deduct points from customer
        if (!$customer->deductLoyaltyPoints($reward->required_points)) {
            return redirect()->route('b2b.rewards.index')->with('error', 'Failed to deduct loyalty points. Please try again.');
        }

        // Create the reward claim
        RewardClaim::create([
            'customer_id' => $customer->id,
            'reward_id' => $reward->id,
            'status' => 'pending',
            'claimed_at' => now(),
        ]);

        return redirect()->route('b2b.reward-claims.index')->with('success', 'Reward claimed successfully! Our team will review your claim shortly.');
    }

    /**
     * Display the system-generated invoice for a reward claim.
     *
     * @param  \App\Models\RewardClaim  $claim
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRewardInvoice(RewardClaim $claim)
    {
        // Security check
        $customer = Customer::where('email', Auth::user()->email)->first();
        if ($claim->customer_id !== $customer->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        if (!$claim->invoice_id) {
            return redirect()->back()->with('error', 'No invoice generated for this claim.');
        }

        $invoice = $claim->invoice()->with(['items.orderItem.product'])->first();
        $order = null; // No order for reward claims

        return view('frontend.b2b.invoice', compact('invoice', 'order'));
    }

    /**
     * Display the reward claims for the current customer.
     *
     * @return \Illuminate\View\View
     */
    public function showMyClaims()
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your reward claims.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Load reward claims with rewards and invoices
        $claims = RewardClaim::where('customer_id', $customer->id)
            ->with(['reward', 'invoice'])
            ->latest()
            ->paginate(10);

        return view('frontend.b2b.my-claims', compact('customer', 'claims'));
    }

    /**
     * Display B2B discounts from highest to lowest for the dealer panel.
     *
     * @return \Illuminate\View\View
     */
    public function showB2BDiscounts()
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view discounts.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Get all categories with their B2B discounts, ordered from highest to lowest
        // Get all products that have B2B discounts (either through category, subcategory, sub-subcategory or dealer-specific discounts)
        $allProducts = Product::where('is_active', true)
            ->with(['category', 'subcategory', 'subSubcategory'])
            ->get();

        // Filter products that have actual B2B discounts for this customer
        $products = $allProducts->filter(function ($product) use ($customer) {
            $originalPrice = $product->price;
            $discountedPrice = $product->getB2BDiscountedPrice($customer);
            return $discountedPrice < $originalPrice; // Only include products with actual discounts
        });

        // Sort products by discount percentage from highest to lowest
        $products = $products->sortByDesc(function ($product) use ($customer) {
            $originalPrice = $product->price;
            $discountedPrice = $product->getB2BDiscountedPrice($customer);
            $discountAmount = $originalPrice - $discountedPrice;
            $discountPercentage = $originalPrice > 0 ? ($discountAmount / $originalPrice) * 100 : 0;
            return $discountPercentage; // Sort by discount percentage
        })
            ->values();

        return view('frontend.b2b.discounts', compact('customer', 'products'));
    }

    /**
     * Display the detailed Credit/Debit note for a dealer's order.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\ReturnNote  $returnNote
     * @return \Illuminate\View\View
     */
    public function showReturnNote(Order $order, ReturnNote $returnNote)
    {
        // Check if user is logged in and is a dealer
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your return note.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id || $returnNote->order_id != $order->id) {
            return redirect()->route('b2b.orders')->with('error', 'You are not authorized to view this return note.');
        }

        $returnNote->load(['items.orderItem.product', 'order.customer', 'returnRequest']);

        return view('frontend.b2b.return-note', compact('order', 'returnNote', 'customer'));
    }
}
