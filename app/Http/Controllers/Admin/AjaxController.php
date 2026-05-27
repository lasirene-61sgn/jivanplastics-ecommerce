<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    /**
     * Get subcategories for a given category.
     */
    public function getSubcategories($categoryId)
    {
        $subcategories = Subcategory::where('category_id', $categoryId)->where('is_active', true)->get();
        return response()->json($subcategories);
    }

    /**
     * Get sub-subcategories for a given subcategory.
     */
    public function getSubSubcategories($subcategoryId)
    {
        $subSubcategories = SubSubcategory::where('subcategory_id', $subcategoryId)->where('is_active', true)->get();
        return response()->json($subSubcategories);
    }

    /**
     * Quick add a new category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        return response()->json($category);
    }

    /**
     * Quick add a new subcategory.
     */
    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $subcategory = Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return response()->json($subcategory);
    }

    /**
     * Quick add a new sub-subcategory.
     */
    public function storeSubSubcategory(Request $request)
    {
        $request->validate([
            'subcategory_id' => 'required|exists:subcategories,id',
            'name' => 'required|string|max:255',
        ]);

        $subSubcategory = SubSubcategory::create([
            'subcategory_id' => $request->subcategory_id,
            'name' => $request->name,
            'is_active' => true,
        ]);

        return response()->json($subSubcategory);
    }
}
