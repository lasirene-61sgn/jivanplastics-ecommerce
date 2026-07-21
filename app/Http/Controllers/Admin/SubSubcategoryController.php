<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SubSubcategoryController extends Controller
{
    /**
     * Display a listing of the sub-subcategories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $subSubcategories = SubSubcategory::with('subcategory.category')->latest()->paginate(10);
        return view('admin.sub_subcategories.index', compact('subSubcategories'));
    }

    /**
     * Show the form for creating a new sub-subcategory.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $subcategories = Subcategory::with('category')->get();
        $dealers = Customer::dealers()->get();
        return view('admin.sub_subcategories.create', compact('subcategories', 'dealers'));
    }

    /**
     * Store a newly created sub-subcategory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sub_subcategories', 'public');
        }

        $subSubcategory = SubSubcategory::create([
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
            'image' => $imagePath,
        ]);

        // Handle dealer-specific discounts
        if ($request->has('dealer_discounts')) {
            foreach ($request->dealer_discounts as $dealerId => $discountPercentage) {
                if ($discountPercentage !== null && $discountPercentage >= 0) {
                    $subSubcategory->dealerDiscounts()->updateOrCreate(
                        ['customer_id' => $dealerId],
                        ['discount_percentage' => $discountPercentage, 'is_active' => true]
                    );
                }
            }
        }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'created',
            'model_type' => 'SubSubcategory',
            'model_id' => $subSubcategory->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $subSubcategory->name]
        ]);

        return redirect()->route('admin.sub_subcategories.index')
            ->with('success', 'Sub-Subcategory created successfully.');
    }

    /**
     * Show the form for editing the specified sub-subcategory.
     *
     * @param  \App\Models\SubSubcategory  $subSubcategory
     * @return \Illuminate\View\View
     */
    public function edit(SubSubcategory $subSubcategory)
    {
        $subcategories = Subcategory::with('category')->get();
        $dealers = Customer::dealers()->get();
        return view('admin.sub_subcategories.edit', compact('subSubcategory', 'subcategories', 'dealers'));
    }

    /**
     * Update the specified sub-subcategory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubSubcategory  $subSubcategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, SubSubcategory $subSubcategory)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $subSubcategory->update([
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        if ($request->hasFile('image')) {
            if ($subSubcategory->image) {
                Storage::disk('public')->delete($subSubcategory->image);
            }
            $subSubcategory->update(['image' => $request->file('image')->store('sub_subcategories', 'public')]);
        }

        // Handle dealer-specific discounts
        if ($request->has('dealer_discounts')) {
            foreach ($request->dealer_discounts as $dealerId => $discountPercentage) {
                if ($discountPercentage !== null && $discountPercentage >= 0) {
                    $subSubcategory->dealerDiscounts()->updateOrCreate(
                        ['customer_id' => $dealerId],
                        ['discount_percentage' => $discountPercentage, 'is_active' => true]
                    );
                } else {
                    // Remove discount if percentage is null or negative
                    $subSubcategory->dealerDiscounts()->where('customer_id', $dealerId)->delete();
                }
            }
        }

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'updated',
            'model_type' => 'SubSubcategory',
            'model_id' => $subSubcategory->id,
            'ip_address' => $request->ip(),
            'details' => ['name' => $subSubcategory->name]
        ]);

        return redirect()->route('admin.sub_subcategories.index')
            ->with('success', 'Sub-Subcategory updated successfully.');
    }

    /**
     * Remove the specified sub-subcategory from storage.
     *
     * @param  \App\Models\SubSubcategory  $subSubcategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(SubSubcategory $subSubcategory)
    {
        $name = $subSubcategory->name;
        $subSubcategory->delete();

        ActivityLog::create([
            'admin_id' => Auth::guard('admin')->id(),
            'action' => 'deleted',
            'model_type' => 'SubSubcategory',
            'model_id' => null,
            'ip_address' => request()->ip(),
            'details' => ['name' => $name]
        ]);

        return redirect()->route('admin.sub_subcategories.index')
            ->with('success', 'Sub-Subcategory deleted successfully.');
    }
}