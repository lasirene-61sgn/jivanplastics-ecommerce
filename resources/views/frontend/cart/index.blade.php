@extends(auth()->check() && auth()->user()->customer && auth()->user()->customer->customer_type === 'dealer' ? 'frontend.b2b.layouts.app' : 'frontend.layouts.app')

@section('title', 'Shopping Cart - V4 Kitchen Partner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 1.5rem;">Shopping Cart</h1>
    
    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            {{ session('error') }}
        </div>
    @endif
    
    @if(count($cart) > 0)
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 2rem;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    @php
                        $isB2B = auth()->check() && auth()->user()->customer && auth()->user()->customer->customer_type === 'dealer';
                    @endphp
                    <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                        <th style="padding: 1rem; text-align: left; font-weight: 600; color: #1f2937;">Product</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #1f2937;">{{ $isB2B ? 'Dealer Price' : 'Price' }}</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #1f2937;">{{ $isB2B ? 'Quantity (Units)' : 'Quantity (Pieces)' }}</th>
                        <th style="padding: 1rem; text-align: right; font-weight: 600; color: #1f2937;">Total</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600; color: #1f2937;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotal = 0;
                        $totalGst = 0;
                        $totalDiscount = 0;
                        $customer = auth()->user()->customer ?? null;
                    @endphp
                    
                    @foreach($cart as $cartKey => $item)
                        @php
                            $productId = $item['product_id'] ?? $cartKey;
                            $product = \App\Models\Product::with('variations')->find($productId);
                            
                            // Find matching variation for this cart item
                            $variation = null;
                            if ($product) {
                                if (!empty($item['variation_id'])) {
                                    $variation = $product->variations->find($item['variation_id']);
                                }
                                if (!$variation && (!empty($item['size']) || !empty($item['thickness']) || !empty($item['color']))) {
                                    $variation = $product->variations
                                        ->where('size', $item['size'] ?? null)
                                        ->where('thickness', $item['thickness'] ?? null)
                                        ->where('color', $item['color'] ?? null)
                                        ->first();
                                }
                                if (!$variation && $product->variations->isNotEmpty()) {
                                    $variation = $isB2B && $customer 
                                        ? ($product->variations->first(function($v) use ($customer) {
                                            return ($v->getDealerDiscountForDealer($customer) > 0) || ($v->getSpecialDiscountForDealer($customer) > 0);
                                        }) ?? $product->variations->first())
                                        : $product->variations->first();
                                }
                            }

                            // Use session price as the ACTUAL price to pay
                            $unitToPay = $item['price'];

                            // Resolve GST percentage: use session if > 0, else variation, else product
                            $gstPercentage = !empty($item['gst_percentage']) && $item['gst_percentage'] > 0 
                                ? (float)$item['gst_percentage'] 
                                : ($variation ? (float)$variation->gst_percentage : (float)($product ? $product->gst_percentage : 0));
                            if ($gstPercentage <= 0 && $variation) {
                                $gstPercentage = 18.0;
                            }

                            // Base Price resolution (with Special Discount applied for B2B)
                            $defaultBasePrice = $variation 
                                ? ($isB2B && $customer ? $variation->getEffectiveBasePriceForDealer($customer) : ($variation->piece_price * $variation->total_pieces))
                                : ($product ? ($isB2B && $customer ? $product->getEffectiveBasePriceForDealer($customer) : $product->price) : $item['price']);

                            $unitBase = !empty($item['original_price']) && $item['original_price'] > 0
                                ? $item['original_price']
                                : $defaultBasePrice;

                            // If B2B dealer and discount was missing or not computed on unitBase:
                            if ($isB2B && $customer) {
                                $dealerDisc = $variation 
                                    ? $variation->getDealerDiscountForDealer($customer) 
                                    : ($product ? $product->getB2BDiscountPercentage($customer) : 0);

                                $specialDisc = $variation ? $variation->getSpecialDiscountForDealer($customer) : null;
                                
                                if ($unitBase <= $unitToPay && $dealerDisc > 0) {
                                    $effBase = $defaultBasePrice;
                                    if ($specialDisc !== null && $specialDisc > 0) {
                                        $effBase = $defaultBasePrice - ($defaultBasePrice * $specialDisc / 100);
                                    }
                                    $unitBase = $effBase;
                                    $unitToPay = $effBase - ($effBase * $dealerDisc / 100);
                                }
                            }
                            
                            $itemBaseTotal = $unitBase * $item['quantity'];
                            $itemDiscountedTotal = $unitToPay * $item['quantity'];
                            $itemDiscountOnly = max(0, $itemBaseTotal - $itemDiscountedTotal);
                            
                            $itemGst = $itemDiscountedTotal * ($gstPercentage / 100);
                            $itemFinalTotal = $itemDiscountedTotal + $itemGst;

                            $subtotal += $itemBaseTotal;
                            $totalDiscount += $itemDiscountOnly;
                            $totalGst += $itemGst;
                        @endphp
                        
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 1rem;">
                                <div style="display: flex; align-items: center;">
                                    @if($item['image'])
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem; margin-right: 1rem;">
                                    @endif
                                    <div>
                                        <h3 style="font-size: 1rem; font-weight: 600; color: #1f2937;">{{ $item['name'] }}</h3>
                                        @if(($item['size'] ?? '') || ($item['thickness'] ?? '') || ($item['color'] ?? ''))
                                            <div style="display: flex; gap: 0.5rem; margin-top: 0.25rem;">
                                                @if($item['size'] ?? '')
                                                    <span style="font-size: 0.7rem; background-color: #f3f4f6; color: #4b5563; padding: 2px 6px; border-radius: 4px; border: 1px solid #e5e7eb;">Size: {{ $item['size'] }}</span>
                                                @endif
                                                @if($item['thickness'] ?? '')
                                                    <span style="font-size: 0.7rem; background-color: #f3f4f6; color: #4b5563; padding: 2px 6px; border-radius: 4px; border: 1px solid #e5e7eb;">Thk: {{ $item['thickness'] }}</span>
                                                @endif
                                                @if($item['color'] ?? '')
                                                    <span style="font-size: 0.7rem; background-color: #f3f4f6; color: #4b5563; padding: 2px 6px; border-radius: 4px; border: 1px solid #e5e7eb;">Color: {{ $item['color'] }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                @if($isB2B)
                                    @if($itemDiscountOnly > 0)
                                        <div style="text-decoration: line-through; color: #9ca3af; font-size: 0.85rem;">
                                            ₹{{ number_format($unitBase, 2) }}
                                        </div>
                                        <div style="color: #1f2937; font-weight: 700; font-size: 1rem;">
                                            ₹{{ number_format($unitToPay, 2) }}
                                        </div>
                                        <span style="background-color: #d1fae5; color: #065f46; font-size: 0.65rem; padding: 2px 5px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">Dealer Disc. Applied</span>
                                    @else
                                        <div style="color: #1f2937; font-weight: 700; font-size: 1rem;">
                                            ₹{{ number_format($unitToPay, 2) }}
                                        </div>
                                    @endif
                                @else
                                    <div style="{{ $itemDiscountOnly > 0 ? 'text-decoration: line-through; color: #9ca3af; font-size: 0.85rem;' : 'color: #1f2937; font-weight: 600;' }}">
                                        ₹{{ number_format($unitBase, 2) }}
                                    </div>
                                    @if($itemDiscountOnly > 0)
                                        <div style="color: #1f2937; font-weight: 700; font-size: 1rem;">
                                            ₹{{ number_format($unitToPay, 2) }}
                                        </div>
                                        <span style="background-color: #d1fae5; color: #065f46; font-size: 0.65rem; padding: 2px 5px; border-radius: 4px; text-transform: uppercase;">Discount Applied</span>
                                    @endif
                                @endif

                                @php
                                    $isItemPieces = isset($item['is_pieces']) && $item['is_pieces'];
                                @endphp

                                @if(!$isItemPieces && $product && $product->per_quantity_pieces > 1)
                                    <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px dashed #e5e7eb;">
                                        <div style="font-size: 0.75rem; color: #6b7280; font-weight: 600;">
                                            Piece Rate: <span style="color: #4f46e5;">₹{{ number_format($unitToPay / $product->per_quantity_pieces, 2) }}</span>
                                        </div>
                                        <div style="font-size: 0.65rem; color: #9ca3af;">
                                            (1 Unit = {{ $product->per_quantity_pieces }} pieces)
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <form action="{{ route('cart.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantities[{{ $cartKey }}]" value="{{ $item['quantity'] }}" min="1" onchange="this.form.submit()" style="width: 60px; text-align: center; border: 1px solid #d1d5db; border-radius: 0.25rem; padding: 0.25rem;">
                                </form>
                            </td>
                            <td style="padding: 1rem; text-align: right; font-weight: 700; color: #1f2937;">
                                @if($isB2B)
                                    ₹{{ number_format($itemDiscountedTotal, 2) }}
                                    <div style="font-size: 0.7rem; color: #6b7280; font-weight: normal;">(+ ₹{{ number_format($itemGst, 2) }} GST)</div>
                                @else
                                    ₹{{ number_format($itemFinalTotal, 2) }}
                                    <div style="font-size: 0.7rem; color: #6b7280; font-weight: normal;">(Incl. ₹{{ number_format($itemGst, 2) }} GST)</div>
                                @endif
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <form action="{{ route('cart.remove', $cartKey) }}" method="POST" onsubmit="return confirm('Remove this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #ef4444; background: none; border: none; cursor: pointer; font-weight: 600;">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="display: flex; justify-content: flex-end;">
            <div style="width: 100%; max-width: 400px; background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1.25rem; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem;">Order Summary</h3>
                
                @if($isB2B)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Subtotal (Gross):</span>
                        <span style="font-weight: 500;">₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($totalDiscount > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: #059669;">
                        <span style="font-weight: 600;">Special Discount:</span>
                        <span style="font-weight: 600;">- ₹{{ number_format($totalDiscount, 2) }}</span>
                    </div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed #e5e7eb;">
                        <span style="color: #374151; font-weight: 600;">Net Subtotal:</span>
                        <span style="font-weight: 700; color: #1f2937;">₹{{ number_format($subtotal - $totalDiscount, 2) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">GST Total:</span>
                        <span style="font-weight: 500;">+ ₹{{ number_format($totalGst, 2) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; padding-top: 1rem; border-top: 2px solid #f3f4f6; margin-top: 1rem;">
                        <span style="font-weight: 800; font-size: 1.3rem;">Grand Total:</span>
                        <span style="font-weight: 800; font-size: 1.3rem; color: #8B0000;">₹{{ number_format(($subtotal - $totalDiscount) + $totalGst, 2) }}</span>
                    </div>
                @else
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">Subtotal (Base):</span>
                        <span style="font-weight: 500;">₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($totalDiscount > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: #059669;">
                        <span style="font-weight: 600;">Discount:</span>
                        <span style="font-weight: 600;">- ₹{{ number_format($totalDiscount, 2) }}</span>
                    </div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: #6b7280;">GST Total:</span>
                        <span style="font-weight: 500;">+ ₹{{ number_format($totalGst, 2) }}</span>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; padding-top: 1rem; border-top: 2px solid #f3f4f6; margin-top: 1rem;">
                        <span style="font-weight: 800; font-size: 1.3rem;">Grand Total:</span>
                        <span style="font-weight: 800; font-size: 1.3rem; color: #8B0000;">₹{{ number_format(($subtotal - $totalDiscount) + $totalGst, 2) }}</span>
                    </div>
                @endif
                
                <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="{{ route('checkout.index') }}" style="display: block; text-align: center; background-color: #8B0000; color: white; padding: 0.8rem; border-radius: 0.5rem; font-weight: 700; text-decoration: none; transition: background 0.2s;">
                        Proceed to Checkout
                    </a>
                    <a href="{{ $isB2B ? route('b2b.products') : route('b2c.products') }}" style="text-align: center; color: #6b7280; font-size: 0.875rem; text-decoration: none;">
                        ← Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @else
        <div style="background-color: white; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 5rem 2rem; text-align: center;">
            <div style="margin-bottom: 1.5rem; color: #d1d5db;">
                <svg xmlns="http://www.w3.org/2000/svg" style="height: 80px; width: 80px; margin: 0 auto;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">Your cart is empty</h3>
            <p style="color: #6b7280; margin: 0.5rem 0 2rem;">Looks like you haven't added anything yet.</p>
            <a href="{{ $isB2B ? route('b2b.products') : route('b2c.products') }}" style="background-color: #8B0000; color: white; padding: 0.75rem 2.5rem; border-radius: 0.5rem; font-weight: 700; text-decoration: none;">Explore Products</a>
        </div>
    @endif
</div>
@endsection