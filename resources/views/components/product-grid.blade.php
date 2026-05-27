@props(['products'])

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
    @foreach($products as $product)
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden;">
            @if($product->images->count() > 0)
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                     alt="{{ $product->name }}" 
                     style="width: 100%; height: 200px; object-fit: cover;">
            @else
                <div style="width: 100%; height: 200px; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                    <span>No Image</span>
                </div>
            @endif
            
            <div style="padding: 1rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">
                    <a href="{{ route('products.show', $product) }}" style="text-decoration: none; color: #1f2937;">
                        {{ $product->name }}
                    </a>
                </h3>
                
                <!-- Check if the authenticated user is a B2B customer and there's a discount -->
                @php
                    $isB2B = auth()->check() && auth()->user()->customer && auth()->user()->customer->customer_type === 'dealer';
                    $customer = auth()->check() ? auth()->user()->customer : null;
                    $discountedPrice = $isB2B ? $product->getB2BDiscountedPrice($customer) : null;
                    $finalPrice = $discountedPrice ?? $product->price;
                    $gstAmount = $finalPrice * $product->gst_percentage / 100;
                    $priceWithGst = $finalPrice + $gstAmount;
                @endphp
                
                <p style="color: #6b7280; margin-bottom: 1rem;">
                    @if($isB2B && $discountedPrice && $discountedPrice < $product->price)
                        <span style="text-decoration: line-through; font-size: 0.875rem;">₹{{ number_format($product->price, 2) }}</span>
                        <br>
                        <span style="font-weight: 600; color: #10b981;">₹{{ number_format($discountedPrice, 2) }}</span>
                    @else
                        <span style="font-weight: 600;">₹{{ number_format($product->price, 2) }}</span>
                    @endif
                    
                    @if($product->gst_percentage > 0)
                        <br>
                        <span style="font-size: 0.75rem; color: #9ca3af;">Incl. {{ number_format($product->gst_percentage, 2) }}% GST</span>
                    @endif
                </p>
                
                <a href="{{ route('products.show', $product) }}" class="btn" style="width: 100%; text-align: center;">View Details</a>
            </div>
        </div>
    @endforeach
</div>