<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the cart page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        $isB2B = false;
        $authUser = Auth::user();
        if ($authUser && isset($authUser->customer)) {
            $customer = $authUser->customer;
            $isB2B = $customer->customer_type === 'dealer';
        }
        
        return view('frontend.cart.index', compact('cart', 'total', 'isB2B'));
    }
    
    /**
     * Add a product to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        
        $productId = $product->id;
        $requestedQuantity = $request->input('quantity', 1);
        $size = $request->input('size');
        $thickness = $request->input('thickness');
        $color = $request->input('color');
        $isPieces = $request->input('is_pieces', false);
        
        // Generate a unique key for this product + attribute combination
        $cartKey = $productId;
        if ($size || $thickness || $color) {
            $cartKey .= '-' . md5(serialize([$size, $thickness, $color]));
        }
        
        // Determine if customer is B2B or B2C
        $isB2B = false;
        $customer = null;
        
        // Check if there's an authenticated user and get their customer details
        $authUser = Auth::user();
        if ($authUser && isset($authUser->customer)) {
            $customer = $authUser->customer;
            $isB2B = $customer->customer_type === 'dealer';
        }

        // Find variation to know perUnitPieces for MOQ calculation early
        $variation = $product->variations()
            ->where(function($q) use ($size) {
                if (empty($size)) $q->whereNull('size')->orWhere('size', '');
                else $q->where('size', $size);
            })
            ->where(function($q) use ($thickness) {
                if (empty($thickness)) $q->whereNull('thickness')->orWhere('thickness', '');
                else $q->where('thickness', $thickness);
            })
            ->where(function($q) use ($color) {
                if (empty($color)) $q->whereNull('color')->orWhere('color', '');
                else $q->where('color', $color);
            })
            ->first();
            
        $perUnitPieces = $variation ? $variation->total_pieces : ($product->per_quantity_pieces ?? 1);
        if ($perUnitPieces < 1) $perUnitPieces = 1;
        
        // Select appropriate MOQ values based on customer type
        if ($isB2B) {
            $minOrderQty = $product->min_order_qty_b2b ?? $product->min_order_qty ?? 1;
            $maxOrderQty = $product->max_order_qty_b2b;
        } else {
            $minOrderQty = $product->min_order_qty_b2c ?? $product->min_order_qty ?? 1;
            $maxOrderQty = $product->max_order_qty_b2c;
        }

        if ($isPieces) {
            // For piece-wise B2C ordering, they can order as little as 1 piece, ignoring unit-based MOQs
            $minOrderQty = 1; 
            if ($maxOrderQty) {
                $maxOrderQty = $maxOrderQty * $perUnitPieces;
            }
        }
        
        // Validate MOQ (Minimum Order Quantity)
        if ($requestedQuantity < $minOrderQty) {
            $customerType = $isB2B ? 'B2B' : 'B2C';
            $unitStr = $isPieces ? 'pieces' : 'units';
            $msg = "{$customerType} minimum order quantity for {$product->name} is {$minOrderQty} {$unitStr}";
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }
        
        // Validate Maximum Order Quantity
        if ($maxOrderQty && $requestedQuantity > $maxOrderQty) {
            $customerType = $isB2B ? 'B2B' : 'B2C';
            $unitStr = $isPieces ? 'pieces' : 'units';
            $msg = "{$customerType} maximum order quantity for {$product->name} is {$maxOrderQty} {$unitStr}";
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg]);
            }
            return redirect()->back()->with('error', $msg);
        }
        
        $isExisting = isset($cart[$cartKey]);

        if ($isExisting) {
            $newQuantity = $cart[$cartKey]['quantity'] + $requestedQuantity;
            
            // Re-validate MOQ when updating existing cart item
            if ($newQuantity < $minOrderQty) {
                $customerType = $isB2B ? 'B2B' : 'B2C';
                $unitStr = $isPieces ? 'pieces' : 'units';
                $msg = "{$customerType} minimum order quantity for {$product->name} is {$minOrderQty} {$unitStr}";
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg]);
                }
                return redirect()->back()->with('error', $msg);
            }
            
            // Re-validate maximum quantity when updating existing cart item
            if ($maxOrderQty && $newQuantity > $maxOrderQty) {
                $customerType = $isB2B ? 'B2B' : 'B2C';
                $unitStr = $isPieces ? 'pieces' : 'units';
                $msg = "{$customerType} maximum order quantity for {$product->name} is {$maxOrderQty} {$unitStr}";
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $msg]);
                }
                return redirect()->back()->with('error', $msg);
            }
            
            $cart[$cartKey]['quantity'] = $newQuantity;
        } else {
            // Find matching variation for price calculation
            $variation = $product->variations()
                ->where(function($q) use ($size) {
                    if (empty($size)) $q->whereNull('size')->orWhere('size', '');
                    else $q->where('size', $size);
                })
                ->where(function($q) use ($thickness) {
                    if (empty($thickness)) $q->whereNull('thickness')->orWhere('thickness', '');
                    else $q->where('thickness', $thickness);
                })
                ->where(function($q) use ($color) {
                    if (empty($color)) $q->whereNull('color')->orWhere('color', '');
                    else $q->where('color', $color);
                })
                ->first();

            // Use variation price or product price
            // Note: variation total_price is GST-inclusive as per our admin logic
            $gstInclusivePrice = $variation ? $variation->total_price : $product->price;
            $gstPercentage = $variation ? $variation->gst_percentage : ($product->gst_percentage ?? 0);
            
            // Calculate Base Price (Without GST)
            $basePriceWithoutGst = $gstInclusivePrice / (1 + ($gstPercentage / 100));
            
            $actualPrice = $basePriceWithoutGst;
            $originalPrice = $basePriceWithoutGst;

            if ($isB2B && $customer) {
                // Special discount silently sets the dealer's base rate
                $specialDiscount = $variation ? $variation->getSpecialDiscountForDealer($customer) : null;
                $effectiveBase = $basePriceWithoutGst;
                if ($specialDiscount !== null && $specialDiscount > 0) {
                    $effectiveBase = $basePriceWithoutGst - ($basePriceWithoutGst * $specialDiscount / 100);
                }

                // Dealer Discount is the visible discount shown on products, cart, order, and invoice
                $dealerDiscount = $variation 
                    ? $variation->getDealerDiscountForDealer($customer) 
                    : $product->getB2BDiscountPercentage($customer);

                if ($dealerDiscount > 0) {
                    $originalPrice = $effectiveBase;
                    $actualPrice = $effectiveBase - ($effectiveBase * $dealerDiscount / 100);
                } else {
                    // If no visible dealer discount, original price equals actual price
                    $originalPrice = $effectiveBase;
                    $actualPrice = $effectiveBase;
                }
            }

            if ($isPieces) {
                $actualPrice = $actualPrice / $perUnitPieces;
                $originalPrice = $originalPrice / $perUnitPieces;
            }

            $cart[$cartKey] = [
                'product_id' => $productId,
                'name' => $product->name,
                'price' => $actualPrice, // Base price after dealer discount, before GST
                'original_price' => $originalPrice,
                'gst_percentage' => $gstPercentage,
                'quantity' => $requestedQuantity,
                'is_pieces' => $isPieces,
                'per_unit_pieces' => $isPieces ? 1 : $perUnitPieces,
                'size' => $size,
                'thickness' => $thickness,
                'color' => $color,
                'image' => $product->images->first() ? $product->images->first()->image_path : null
            ];
        }
        
        session()->put('cart', $cart);
        
        $successMsg = $isExisting ? 'Product quantity updated in cart successfully!' : 'Product added to cart successfully!';
        
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'is_already_in_cart' => true,
                'cart_count' => array_sum(array_column($cart, 'quantity')),
                'cart_product_ids' => array_values(array_unique(array_column($cart, 'product_id')))
            ]);
        }
        
        return redirect()->back()->with('success', $successMsg);
    }
    
    /**
     * Update the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $cart = session()->get('cart', []);
        
        foreach ($request->input('quantities') as $cartKey => $quantity) {
            if (isset($cart[$cartKey])) {
                $productId = $cart[$cartKey]['product_id'];
                $product = Product::find($productId);
                
                if ($product) {
                    // Determine if customer is B2B or B2C
                    $isB2B = false;
                    $customer = null;
                    
                    // Check if there's an authenticated user and get their customer details
                    $authUser = Auth::user();
                    if ($authUser && isset($authUser->customer)) {
                        $customer = $authUser->customer;
                        $isB2B = $customer->customer_type === 'dealer';
                    }
                    
                    // Select appropriate MOQ values based on customer type
                    if ($isB2B) {
                        $minOrderQty = $product->min_order_qty_b2b ?? $product->min_order_qty ?? 1;
                        $maxOrderQty = $product->max_order_qty_b2b;
                    } else {
                        $minOrderQty = $product->min_order_qty_b2c ?? $product->min_order_qty ?? 1;
                        $maxOrderQty = $product->max_order_qty_b2c;
                    }
                    
                    // Validate MOQ (Minimum Order Quantity)
                    $isPieces = isset($cart[$cartKey]['is_pieces']) && $cart[$cartKey]['is_pieces'];
                    $perUnitPieces = isset($cart[$cartKey]['per_unit_pieces']) ? $cart[$cartKey]['per_unit_pieces'] : ($product->per_quantity_pieces ?? 1);
                    
                    if ($isPieces) {
                        // For B2C piece-wise, they can order as little as 1 piece
                        $minOrderQty = 1;
                        if ($maxOrderQty) $maxOrderQty = $maxOrderQty * ($product->per_quantity_pieces ?? 1);
                    }

                    if ($quantity < $minOrderQty) {
                        $customerType = $isB2B ? 'B2B' : 'B2C';
                        $unitStr = $isPieces ? 'pieces' : 'units';
                        return redirect()->back()->with('error', "{$customerType} minimum order quantity for {$product->name} is {$minOrderQty} {$unitStr}");
                    }
                    
                    // Validate Maximum Order Quantity
                    if ($maxOrderQty && $quantity > $maxOrderQty) {
                        $customerType = $isB2B ? 'B2B' : 'B2C';
                        $unitStr = $isPieces ? 'pieces' : 'units';
                        return redirect()->back()->with('error', "{$customerType} maximum order quantity for {$product->name} is {$maxOrderQty} {$unitStr}");
                    }
                    
                    if ($quantity <= 0) {
                        unset($cart[$cartKey]);
                    } else {
                        $cart[$cartKey]['quantity'] = $quantity;
                    }
                }
            }
        }
        
        session()->put('cart', $cart);
        
        return redirect()->back()->with('success', 'Cart updated successfully!');
    }
    
    /**
     * Remove a product from the cart.
     *
     * @param  string  $cartKey
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($cartKey)
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
        }
        
        return redirect()->back()->with('success', 'Product removed from cart successfully!');
    }
    
    /**
     * Clear the cart.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        session()->forget('cart');
        
        return redirect()->back()->with('success', 'Cart cleared successfully!');
    }
}