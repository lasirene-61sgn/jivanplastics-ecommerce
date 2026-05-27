@extends('frontend.layouts.app')

@section('title', $product->name . ' - E-Commerce Store')

@section('content')
    <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
        <!-- Product Images -->
        <div style="flex: 1; min-width: 300px;">
            @if($product->images->count() > 0)
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 1rem;">
                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                         alt="{{ $product->name }}" 
                         style="width: 100%; height: 400px; object-fit: cover;">
                </div>
                
                @if($product->images->count() > 1)
                    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem;">
                        @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                 alt="{{ $product->name }}" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 0.25rem; cursor: pointer;"
                                 onclick="changeMainImage(this.src)">
                        @endforeach
                    </div>
                @endif
            @else
                <div style="background-color: #e5e7eb; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); height: 400px; display: flex; align-items: center; justify-content: center;">
                    <span>No Image Available</span>
                </div>
            @endif
        </div>
        
        <!-- Product Details -->
        <div style="flex: 2; min-width: 300px;">
            <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem;">
                <h1 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem; color: #1f2937;">
                    {{ $product->name }}
                </h1>
                
                <!-- Check if the authenticated user is a B2B customer and there's a discount -->
                @php
                    $isB2B = auth()->check() && auth()->user()->customer && auth()->user()->customer->customer_type === 'dealer';
                    $customer = auth()->check() ? auth()->user()->customer : null;
                    $discountedPrice = $isB2B ? $product->getB2BDiscountedPrice($customer) : null;
                    $finalPrice = $discountedPrice ?? $product->price;
                    $perUnitPieces = $product->per_quantity_pieces ?? 1;
                    $perUnitPieces = max(1, $perUnitPieces);
                    
                    if (!$isB2B) {
                        $finalPrice = $finalPrice / $perUnitPieces;
                    }
                    
                    $gstAmount = $finalPrice * $product->gst_percentage / 100;
                    $priceWithGst = $finalPrice + $gstAmount;
                @endphp
                
                @if($isB2B && $discountedPrice && $discountedPrice < $product->price)
                    <div style="margin-bottom: 1rem;">
                        <p style="font-size: 1rem; color: #6b7280; text-decoration: line-through; margin: 0;">
                            ₹{{ number_format($product->price, 2) }} / unit
                        </p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #10b981; margin: 0;">
                            ₹{{ number_format($discountedPrice, 2) }} / unit
                        </p>
                        <span style="background-color: #10b981; color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; margin-top: 0.25rem; display: inline-block;">
                            {{ number_format(($product->price - $discountedPrice) / $product->price * 100, 2) }}% OFF for B2B Customers
                        </span>
                    </div>
                @else
                    <p style="font-size: 1.25rem; font-weight: 700; color: #3b82f6; margin-bottom: 1rem;">
                        ₹{{ number_format($finalPrice, 2) }} {{ $isB2B ? '/ unit' : '/ piece' }}
                    </p>
                @endif
                
                <!-- GST Information -->
                @if($product->gst_percentage > 0)
                    <div style="margin-bottom: 1rem; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.375rem; border: 1px solid #e5e7eb;">
                        <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">
                            <strong>GST ({{ number_format($product->gst_percentage, 2) }}%):</strong> 
                            ₹{{ number_format($gstAmount, 2) }}
                        </p>
                        <p style="margin: 0.25rem 0 0 0; font-size: 1rem; font-weight: 600; color: #1f2937;">
                            Final Price (incl. GST): ₹{{ number_format($priceWithGst, 2) }}
                        </p>
                    </div>
                @endif
                
                @if($product->category)
                    <p style="color: #6b7280; margin-bottom: 1rem;">
                        <strong>Category:</strong> {{ $product->category->name }}
                    </p>
                @endif
                
                @if($product->subcategory)
                    <p style="color: #6b7280; margin-bottom: 1rem;">
                        <strong>Subcategory:</strong> {{ $product->subcategory->name }}
                    </p>
                @endif
                
                @if($product->subSubcategory)
                    <p style="color: #6b7280; margin-bottom: 1rem;">
                        <strong>Sub-Subcategory:</strong> {{ $product->subSubcategory->name }}
                    </p>
                @endif
                
                @if($product->description)
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">
                            Description
                        </h3>
                        <div style="color: #6b7280; line-height: 1.6;">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif
                
                <!-- MOQ Information -->
                @php
                    // Determine if customer is B2B or B2C
                    $isB2B = false;
                    if (auth()->check() && auth()->user()->customer) {
                        $customer = auth()->user()->customer;
                        $isB2B = $customer->customer_type === 'dealer';
                    }
                    
                    $perUnitPieces = $product->per_quantity_pieces ?? 1;
                    $perUnitPieces = max(1, $perUnitPieces);
                    
                    // Select appropriate MOQ values based on customer type
                    if ($isB2B) {
                        $minQty = $product->min_order_qty_b2b ?? $product->min_order_qty ?? 1;
                        $maxQty = $product->max_order_qty_b2b;
                        $orderUnit = 'Units';
                    } else {
                        $minQty = ($product->min_order_qty_b2c ?? $product->min_order_qty ?? 1) * $perUnitPieces;
                        $maxQty = $product->max_order_qty_b2c ? $product->max_order_qty_b2c * $perUnitPieces : null;
                        $orderUnit = 'Pieces';
                    }
                @endphp
                @if($minQty != 1 || $maxQty)
                    <div style="margin-bottom: 1rem; padding: 0.75rem; background-color: #f9fafb; border-radius: 0.375rem; border: 1px solid #e5e7eb;">
                        <p style="margin: 0; font-size: 0.875rem; color: #6b7280;">
                            <strong>{{ $isB2B ? 'B2B' : 'B2C' }} Order Quantity Requirements:</strong><br>
                            @if($minQty && $maxQty)
                                Minimum: {{ $minQty }} {{ $orderUnit }} | Maximum: {{ $maxQty }} {{ $orderUnit }}
                            @elseif($minQty)
                                Minimum: {{ $minQty }} {{ $orderUnit }}
                            @elseif($maxQty)
                                Maximum: {{ $maxQty }} {{ $orderUnit }}
                            @endif
                        </p>
                    </div>
                @endif
                
                <form action="{{ route('cart.add', $product) }}" method="POST" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    @csrf
                    @if(!$isB2B)
                        <input type="hidden" name="is_pieces" value="1">
                    @endif
                    <div style="display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 0.375rem; overflow: hidden;">
                        <button type="button" onclick="decreaseQuantity()" style="padding: 0.5rem; background-color: #f9fafb; border: none; cursor: pointer;">-</button>
                        <input type="number" id="quantity" name="quantity" value="{{ $minQty }}" min="{{ $minQty }}" @if($maxQty) max="{{ $maxQty }}" @endif style="width: 70px; text-align: center; border: none; padding: 0.5rem;" title="Quantity in {{ $orderUnit }}">
                        <button type="button" onclick="increaseQuantity()" style="padding: 0.5rem; background-color: #f9fafb; border: none; cursor: pointer;">+</button>
                    </div>
                    <button type="submit" class="btn" style="background-color: #8B0000; color: white; padding: 0.75rem 1.5rem; border-radius: 0.375rem; border: none; cursor: pointer;">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div style="margin-top: 3rem;">
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1.5rem;">
                Related Products
            </h2>
            
            <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                @foreach($relatedProducts as $relatedProduct)
                    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; width: 250px;">
                        @if($relatedProduct->images->count() > 0)
                            <img src="{{ asset('storage/' . $relatedProduct->images->first()->image_path) }}" 
                                 alt="{{ $relatedProduct->name }}" 
                                 style="width: 100%; height: 150px; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 150px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                                <span>No Image</span>
                            </div>
                        @endif
                        
                        <div style="padding: 1rem;">
                            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                                <a href="{{ route('products.show', $relatedProduct) }}" style="text-decoration: none; color: #1f2937;">
                                    {{ $relatedProduct->name }}
                                </a>
                            </h3>
                            
                            <!-- Check if the authenticated user is a B2B customer and there's a discount -->
                            @php
                                $relatedDiscountedPrice = $isB2B ? $relatedProduct->getB2BDiscountedPrice($customer) : null;
                                $relatedFinalPrice = $relatedDiscountedPrice ?? $relatedProduct->price;
                                $relatedGstAmount = $relatedFinalPrice * $relatedProduct->gst_percentage / 100;
                                $relatedPriceWithGst = $relatedFinalPrice + $relatedGstAmount;
                            @endphp
                            
                            @if($isB2B && $relatedDiscountedPrice && $relatedDiscountedPrice < $relatedProduct->price)
                                <div style="margin-bottom: 0.5rem;">
                                    <p style="color: #6b7280; text-decoration: line-through; margin: 0; font-size: 0.875rem;">
                                        ₹{{ number_format($relatedProduct->price, 2) }}
                                    </p>
                                    <p style="color: #10b981; font-weight: 600; margin: 0;">
                                        ₹{{ number_format($relatedDiscountedPrice, 2) }}
                                    </p>
                                </div>
                            @else
                                <p style="color: #6b7280; margin-bottom: 1rem;">
                                    ₹{{ number_format($relatedProduct->price, 2) }}
                                </p>
                            @endif
                            
                            <a href="{{ route('products.show', $relatedProduct) }}" class="btn" style="width: 100%; text-align: center; padding: 0.5rem;">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <script>
        function changeMainImage(src) {
            document.querySelector('.product-main-image').src = src;
        }
        
        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const minVal = parseInt(quantityInput.min);
            
            if (quantityInput.value > minVal) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
            }
        }
        
        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const maxVal = quantityInput.max ? parseInt(quantityInput.max) : Infinity;
            
            if (parseInt(quantityInput.value) < maxVal) {
                quantityInput.value = parseInt(quantityInput.value) + 1;
            }
        }
    </script>
@endsection