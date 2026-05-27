<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // If user is logged in, redirect to their dashboard
        if (Auth::check()) {
            $customer = Customer::where('email', Auth::user()->email)->first();
            
            if ($customer && $customer->customer_type === 'dealer') {
                return redirect()->route('b2b.dashboard');
            }
            
            return redirect()->route('b2c.dashboard');
        }
        
        // Get categories with subcategories and sub-subcategories
        $categories = Category::with([
            'subcategories' => function ($query) {
                $query->where('is_active', true)->with([
                    'subSubcategories' => function ($subQuery) {
                        $subQuery->where('is_active', true);
                    }
                ]);
            }
        ])->where('is_active', true)->get();
        
        $featuredProducts = Product::where('is_active', true)->with('images')->take(8)->get();
        
        return view('frontend.home', compact('categories', 'featuredProducts'));
    }
    
    /**
     * Display the about us page.
     *
     * @return \Illuminate\View\View
     */
    public function about()
    {
        return view('frontend.about');
    }
    
    /**
     * Display the contact page.
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('frontend.contact');
    }
}