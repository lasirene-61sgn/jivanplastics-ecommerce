<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'subcategory', 'subSubcategory', 'images', 'variations']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->subcategory_id);
        }

        if ($request->filled('sub_subcategory_id')) {
            $query->where('sub_subcategory_id', $request->sub_subcategory_id);
        }

        $perPage = $request->input('per_page', 10);
        $products = $query->latest()->paginate($perPage)->appends($request->all());

        $categories = Category::all();
        
        // Pass subcategories and subSubcategories if a category is selected to retain dropdown state
        $subcategories = [];
        $subSubcategories = [];
        if ($request->filled('category_id')) {
            $subcategories = Subcategory::where('category_id', $request->category_id)->get();
        }
        if ($request->filled('subcategory_id')) {
            $subSubcategories = SubSubcategory::where('subcategory_id', $request->subcategory_id)->get();
        }

        return view('admin.products.index', compact('products', 'categories', 'subcategories', 'subSubcategories'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $subSubcategories = SubSubcategory::all();
        $dealers = Customer::dealers()->get();
        return view('admin.products.create', compact('categories', 'subcategories', 'subSubcategories', 'dealers'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'variations' => 'required|array|min:1',
        // Attributes are nullable to support different product types
        'variations.*.size' => 'nullable|string',
        'variations.*.thickness' => 'nullable|string',
        'variations.*.color' => 'nullable|string',
        'variations.*.piece_price' => 'required|numeric|min:0',
        'variations.*.total_pieces' => 'required|integer|min:1',
        'variations.*.gst_percentage' => 'nullable|numeric|min:0|max:100',
    ]);

    // Create the Main Product
    $product = Product::create($request->only([
        'name', 'description', 'category_id', 'subcategory_id', 
        'sub_subcategory_id', 'is_active'
    ]));

    // Loop through variations
    $sizes = [];
    $thicknesses = [];
    $colors = [];

    if ($request->has('variations')) {
        foreach ($request->variations as $item) {
            $piecePrice = $item['piece_price'] ?? 0;
            $totalPieces = $item['total_pieces'] ?? 1;
            $gstPercent = $item['gst_percentage'] ?? 0;

            // Collect unique attributes
            if (!empty($item['size'])) $sizes[] = trim($item['size']);
            if (!empty($item['thickness'])) $thicknesses[] = trim($item['thickness']);
            if (!empty($item['color'])) $colors[] = trim($item['color']);

            // Math Logic
            $subtotal = $piecePrice * $totalPieces;
            $gstAmount = $subtotal * ($gstPercent / 100);
            $totalWithGst = $subtotal + $gstAmount;

            $product->variations()->create([
                'size' => $item['size'] ?? null,
                'thickness' => $item['thickness'] ?? null,
                'color' => $item['color'] ?? null,
                'piece_price' => $piecePrice,
                'total_pieces' => $totalPieces,
                'gst_percentage' => $gstPercent,
                'total_price' => $totalWithGst,
            ]);
        }
    }

    // Update main product attributes
    $firstVariation = $product->variations()->first();
    $product->update([
        'size' => implode(',', array_unique($sizes)),
        'thickness' => implode(',', array_unique($thicknesses)),
        'color' => implode(',', array_unique($colors)),
        'per_quantity_pieces' => $firstVariation->total_pieces ?? 1,
        'piece_price' => $firstVariation->piece_price ?? 0,
        // Store base price (without GST) as the main price
        'price' => $firstVariation ? ($firstVariation->piece_price * $firstVariation->total_pieces) : 0,
    ]);

    // Handle Image Uploads
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $imagePath = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'sort_order' => $index
            ]);
        }
    }

    // Handle Dealer Discounts
    if ($request->has('dealer_discounts')) {
        foreach ($request->dealer_discounts as $dealerId => $discount) {
            if ($discount !== null && $discount >= 0) {
                $product->dealerDiscounts()->updateOrCreate(
                    ['customer_id' => $dealerId],
                    ['discount_percentage' => $discount, 'is_active' => true]
                );
            }
        }
    }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'created',
            'model_type' => 'Product',
            'model_id' => $product->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $product->name]
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        $subSubcategories = SubSubcategory::all();
        $dealers = Customer::dealers()->get();
        $product->load('images'); // Load the product images
        return view('admin.products.edit', compact('product', 'categories', 'subcategories', 'subSubcategories', 'dealers'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'variations' => 'required|array|min:1',
        'variations.*.piece_price' => 'required|numeric',
        'variations.*.total_pieces' => 'required|integer',
    ]);

    // Update basic info
    $product->update($request->only(['name', 'description', 'category_id', 'subcategory_id', 'sub_subcategory_id', 'is_active']));

    // Sync Variations: Remove old and add new
    $product->variations()->delete();

    $sizes = [];
    $thicknesses = [];
    $colors = [];

    foreach ($request->variations as $item) {
        $subtotal = ($item['piece_price'] ?? 0) * ($item['total_pieces'] ?? 1);
        $gstAmount = $subtotal * (($item['gst_percentage'] ?? 0) / 100);

        // Collect unique attributes
        if (!empty($item['size'])) $sizes[] = trim($item['size']);
        if (!empty($item['thickness'])) $thicknesses[] = trim($item['thickness']);
        if (!empty($item['color'])) $colors[] = trim($item['color']);

        $product->variations()->create([
            'size' => $item['size'] ?? null,
            'thickness' => $item['thickness'] ?? null,
            'color' => $item['color'] ?? null,
            'piece_price' => $item['piece_price'],
            'total_pieces' => $item['total_pieces'],
            'gst_percentage' => $item['gst_percentage'] ?? 0,
            'total_price' => $subtotal + $gstAmount,
        ]);
    }

    // Update main product attributes
    $firstVariation = $product->variations()->first();
    $product->update([
        'size' => implode(',', array_unique($sizes)),
        'thickness' => implode(',', array_unique($thicknesses)),
        'color' => implode(',', array_unique($colors)),
        'per_quantity_pieces' => $firstVariation->total_pieces ?? 1,
        'piece_price' => $firstVariation->piece_price ?? 0,
        // Store base price (without GST) as the main price
        'price' => $firstVariation ? ($firstVariation->piece_price * $firstVariation->total_pieces) : 0,
    ]);

    // Handle New Images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $imagePath = $image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
                'sort_order' => $index
            ]);
        }
    }

    // Dealer Discounts
    if ($request->has('dealer_discounts')) {
        foreach ($request->dealer_discounts as $dealerId => $discount) {
            if ($discount !== null && $discount >= 0) {
                $product->dealerDiscounts()->updateOrCreate(
                    ['customer_id' => $dealerId],
                    ['discount_percentage' => $discount, 'is_active' => true]
                );
            } else {
                $product->dealerDiscounts()->where('customer_id', $dealerId)->delete();
            }
        }
    }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'updated',
            'model_type' => 'Product',
            'model_id' => $product->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $product->name]
        ]);

        return redirect()->route('admin.products.index', ['page' => $request->page])->with('success', 'Product updated successfully.');
    }

    /**
     * Remove an image from a product.
     *
     * @param  \App\Models\Product  $product
     * @param  int  $imageId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyImage(Product $product, $imageId)
    {
        $image = ProductImage::where('product_id', $product->id)->where('id', $imageId)->first();

        if ($image) {
            // Delete the image file
            Storage::disk('public')->delete($image->image_path);

            // Delete the image record
            $image->delete();
        }

        return redirect()->back()->with('success', 'Image deleted successfully.');
    }

    /**
     * Show the product details form.
     *
     * @return \Illuminate\View\View
     */
    public function productDetails()
    {
        $products = Product::with('images')->get();
        return view('admin.products.product_details', compact('products'));
    }

    /**
     * Store product details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeProductDetails(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Update product description
        if ($request->has('description')) {
            $product->description = $request->description;
            $product->save();
        }

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'sort_order' => $index
                ]);
            }
        }

        return redirect()->back()->with('success', 'Product details updated successfully.');
    }

    /**
     * Show the product details list.
     *
     * @return \Illuminate\View\View
     */
    public function listProductDetails()
    {
        $products = Product::with('images')->paginate(10);
        return view('admin.products.product_details_list', compact('products'));
    }

    /**
     * Show the form for editing product details.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function editProductDetails(Product $product)
    {
        $product->load('images');
        return view('admin.products.edit_product_details', compact('product'));
    }

    /**
     * Update product details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProductDetails(Request $request, Product $product)
    {
        $request->validate([
            'description' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update product description
        if ($request->has('description')) {
            $product->description = $request->description;
            $product->save();
        }

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'sort_order' => $index
                ]);
            }
        }

        return redirect()->route('admin.product-details.list')->with('success', 'Product details updated successfully.');
    }

    /**
     * Delete product details (description and images).
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteProductDetails(Product $product)
    {
        // Clear the product description
        $product->description = null;
        $product->save();

        // Delete all images associated with the product
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        return redirect()->route('admin.product-details.list')->with('success', 'Product details deleted successfully.');
    }
    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Delete images from storage
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        // Variations are handled by database cascade if set up, 
        // but let's be explicit if not sure.
        $product->variations()->delete();
        $product->dealerDiscounts()->delete();

        $name = $product->name;
        $product->delete();

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'deleted',
            'model_type' => 'Product',
            'model_id' => null,
            'ip_address' => request()->ip(),
            'details' => ['name' => $name]
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
