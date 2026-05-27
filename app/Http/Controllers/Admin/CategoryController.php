<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $dealers = Customer::dealers()->get();
        return view('admin.categories.create', compact('dealers'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'b2b_discount' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'b2b_discount' => $request->b2b_discount,
            'image' => $imagePath,
        ]);

        // Handle dealer-specific discounts
        if ($request->has('dealer_discounts')) {
            foreach ($request->dealer_discounts as $dealerId => $discountPercentage) {
                if ($discountPercentage !== null && $discountPercentage >= 0) {
                    $category->dealerDiscounts()->updateOrCreate(
                        ['customer_id' => $dealerId],
                        ['discount_percentage' => $discountPercentage, 'is_active' => true]
                    );
                }
            }
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        $dealers = Customer::dealers()->get();
        return view('admin.categories.edit', compact('category', 'dealers'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'b2b_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'b2b_discount' => $request->b2b_discount,
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $category->update(['image' => $request->file('image')->store('categories', 'public')]);
        }

        // Handle dealer-specific discounts
        if ($request->has('dealer_discounts')) {
            foreach ($request->dealer_discounts as $dealerId => $discountPercentage) {
                if ($discountPercentage !== null && $discountPercentage >= 0) {
                    $category->dealerDiscounts()->updateOrCreate(
                        ['customer_id' => $dealerId],
                        ['discount_percentage' => $discountPercentage, 'is_active' => true]
                    );
                } else {
                    // Remove discount if percentage is null or negative
                    $category->dealerDiscounts()->where('customer_id', $dealerId)->delete();
                }
            }
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}