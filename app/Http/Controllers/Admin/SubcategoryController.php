<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Subcategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the subcategories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $subcategories = Subcategory::with('category')->latest()->paginate(10);
        return view('admin.subcategories.index', compact('subcategories'));
    }

    /**
     * Show the form for creating a new subcategory.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        $dealers = Customer::dealers()->get();
        return view('admin.subcategories.create', compact('categories', 'dealers'));
    }

    /**
     * Store a newly created subcategory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('subcategories', 'public');
        }

        $subcategory = Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'image' => $imagePath,
        ]);

        // Handle dealer-specific discounts
        if ($request->has('dealer_discounts')) {
            foreach ($request->dealer_discounts as $dealerId => $discountPercentage) {
                if ($discountPercentage !== null && $discountPercentage >= 0) {
                    $subcategory->dealerDiscounts()->updateOrCreate(
                        ['customer_id' => $dealerId],
                        ['discount_percentage' => $discountPercentage, 'is_active' => true]
                    );
                }
            }
        }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'created',
            'model_type' => 'Subcategory',
            'model_id' => $subcategory->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $subcategory->name]
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory created successfully.');
    }

    /**
     * Show the form for editing the specified subcategory.
     *
     * @param  \App\Models\Subcategory  $subcategory
     * @return \Illuminate\View\View
     */
    public function edit(Subcategory $subcategory)
    {
        $categories = Category::all();
        $dealers = Customer::dealers()->get();
        return view('admin.subcategories.edit', compact('subcategory', 'categories', 'dealers'));
    }

    /**
     * Update the specified subcategory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Subcategory  $subcategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        if ($request->hasFile('image')) {
            if ($subcategory->image) {
                Storage::disk('public')->delete($subcategory->image);
            }
            $subcategory->update(['image' => $request->file('image')->store('subcategories', 'public')]);
        }

        // Handle dealer-specific discounts
        if ($request->has('dealer_discounts')) {
            foreach ($request->dealer_discounts as $dealerId => $discountPercentage) {
                if ($discountPercentage !== null && $discountPercentage >= 0) {
                    $subcategory->dealerDiscounts()->updateOrCreate(
                        ['customer_id' => $dealerId],
                        ['discount_percentage' => $discountPercentage, 'is_active' => true]
                    );
                } else {
                    // Remove discount if percentage is null or negative
                    $subcategory->dealerDiscounts()->where('customer_id', $dealerId)->delete();
                }
            }
        }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'updated',
            'model_type' => 'Subcategory',
            'model_id' => $subcategory->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $subcategory->name]
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory updated successfully.');
    }

    /**
     * Remove the specified subcategory from storage.
     *
     * @param  \App\Models\Subcategory  $subcategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Subcategory $subcategory)
    {
        $name = $subcategory->name;
        $subcategory->delete();

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'deleted',
            'model_type' => 'Subcategory',
            'model_id' => null,
            'ip_address' => request()->ip(),
            'details' => ['name' => $name]
        ]);

        return redirect()->route('admin.subcategories.index')
            ->with('success', 'Subcategory deleted successfully.');
    }
}