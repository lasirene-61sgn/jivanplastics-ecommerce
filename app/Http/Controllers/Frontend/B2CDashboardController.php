<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class B2CDashboardController extends Controller
{
    /**
     * Display the B2C dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access the B2C dashboard.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Get some statistics for the dashboard
        $totalProducts = Product::where('is_active', true)->count();
        $recentProducts = Product::where('is_active', true)->with('images')->latest()->take(5)->get();
        $totalOrders = Order::where('customer_id', $customer->id)->count();
        $pendingOrders = Order::where('customer_id', $customer->id)->where('status', 'pending')->count();
        $completedOrders = Order::where('customer_id', $customer->id)->where('status', 'completed')->count();

        return view('frontend.b2c.dashboard', compact('customer', 'totalProducts', 'recentProducts', 'totalOrders', 'pendingOrders', 'completedOrders'));
    }

    /**
     * Display the B2C product catalog.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function products(Request $request)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access the product catalog.');
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

        $products = $query->with('images')->latest()->paginate(12)->withQueryString();

        // Breadcrumb data
        $root = request('category') ? Category::find(request('category')) : null;
        $sub = request('subcategory') ? Subcategory::find(request('subcategory')) : null;
        $deep = request('sub_subcategory') ? SubSubcategory::find(request('sub_subcategory')) : null;

        return view('frontend.b2c.products', compact(
            'products',
            'categories',
            'customer',
            'availableSizes',
            'availableThicknesses',
            'availableColors',
            'displayMode',
            'childCategories',
            'root',
            'sub',
            'deep'
        ));
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function showProduct(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();

        return view('frontend.b2c.product-details', compact('product', 'customer', 'relatedProducts'));
    }

    /**
     * Display the B2C orders page.
     *
     * @return \Illuminate\View\View
     */
    public function orders()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your orders.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();
        $orders = Order::where('customer_id', $customer->id)->with('items.product.images')->latest()->paginate(10);

        return view('frontend.b2c.orders', compact('orders'));
    }

    /**
     * Display the details of a specific order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function showOrder(Order $order)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your order.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('b2c.orders')->with('error', 'You are not authorized to view this order.');
        }

        $order->load('items.product');

        return view('frontend.b2c.order-details', compact('order'));
    }

    /**
     * Display the invoice for a specific order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function showInvoice(Order $order)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your invoice.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('b2c.orders')->with('error', 'You are not authorized to view this invoice.');
        }

        $order->load('items.product', 'dispatchImages.orderItem');

        // Force refresh all items to ensure latest data
        foreach ($order->items as $item) {
            $item->refresh();
        }
        $order->refresh();

        return view('frontend.b2c.invoice', compact('order'));
    }

    /**
     * Display the B2C profile page.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access your profile.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        return view('frontend.b2c.profile', compact('customer'));
    }

    /**
     * Update the B2C profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to update your profile.');
        }

        $customer = Customer::where('email', Auth::user()->email)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
        ]);

        $customer->update($request->all());

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
