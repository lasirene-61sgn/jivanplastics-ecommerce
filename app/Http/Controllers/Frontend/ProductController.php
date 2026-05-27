<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\SubSubcategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->with('images');
        
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('subcategory')) {
            $query->where('subcategory_id', $request->subcategory);
        }
        
        if ($request->has('sub_subcategory')) {
            $query->where('sub_subcategory_id', $request->sub_subcategory);
        }
        
        if ($request->has('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('id', 'like', $searchTerm)
                  ->orWhere('size', 'like', $searchTerm)
                  ->orWhere('thickness', 'like', $searchTerm)
                  ->orWhere('color', 'like', $searchTerm);
            });
        }
        
        $products = $query->paginate(12);
        $categories = Category::all();
        
        return view('frontend.products.index', compact('products', 'categories'));
    }
    
    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }
        
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
            
        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }
    
    /**
     * Display subcategories by category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function byCategory(Category $category)
    {
        // Load subcategories with product counts
        $category->load([
            'subcategories' => function ($query) {
                $query->where('is_active', true)->withCount('products');
            }
        ]);
        
        return view('frontend.categories.show', compact('category'));
    }
    
    /**
     * Display products and sub-subcategories by subcategory.
     *
     * @param  \App\Models\Subcategory  $subcategory
     * @return \Illuminate\View\View
     */
    public function bySubcategory(Subcategory $subcategory)
    {
        // Load products and sub-subcategories with product counts
        $subcategory->load([
            'products' => function ($query) {
                $query->where('is_active', true)->with('images');
            },
            'subSubcategories' => function ($query) {
                $query->where('is_active', true)->withCount('products');
            },
            'category'
        ]);
        
        return view('frontend.subcategories.show', compact('subcategory'));
    }
    
    /**
     * Display products by sub-subcategory.
     *
     * @param  \App\Models\SubSubcategory  $subSubcategory
     * @return \Illuminate\View\View
     */
    public function bySubSubcategory(SubSubcategory $subSubcategory)
    {
        // Load products and relationships
        $subSubcategory->load([
            'products' => function ($query) {
                $query->where('is_active', true)->with('images');
            },
            'subcategory.category'
        ]);
        
        return view('frontend.sub-subcategories.show', compact('subSubcategory'));
    }
}