@extends('frontend.b2b.layouts.app')

@section('title', 'Request Return/Replacement - E-Commerce Store')
@section('header', 'Request Return/Replacement')

@section('content')
<div class="b2b-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="b2b-section-title">Request Return/Replacement</h2>
        <a href="{{ route('b2b.orders.show', $order) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; border: 2px solid #8B0000; color: #8B0000;">Back to Order</a>
    </div>
    
    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Product Details</h3>
        
        <div style="display: flex; align-items: center; margin-bottom: 1rem;">
            @if($orderItem->product && $orderItem->product->images->count() > 0)
                <img src="{{ asset('storage/' . $orderItem->product->images->first()->image_path) }}" 
                     alt="{{ $orderItem->product_name }}" 
                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 0.25rem; margin-right: 1rem;">
            @else
                <div style="width: 80px; height: 80px; background-color: #e5e7eb; border-radius: 0.25rem; margin-right: 1rem; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 0.75rem;">No Image</span>
                </div>
            @endif
            <div>
                <h4 style="font-size: 1.125rem; font-weight: 500; color: #1f2937; margin: 0;">
                    {{ $orderItem->product_name }}
                </h4>
                <p style="color: #6b7280; margin: 0.25rem 0;">
                    SKU: {{ $orderItem->product_sku }}
                </p>
                <p style="color: #6b7280; margin: 0.25rem 0;">
                    Price: ₹{{ number_format($orderItem->price, 2) }}
                </p>
                <p style="color: #6b7280; margin: 0.25rem 0;">
                    Ordered Quantity: {{ $orderItem->quantity }}
                </p>
                <p style="color: #6b7280; margin: 0.25rem 0;">
                    Dispatched Quantity: {{ $orderItem->dispatched_quantity }}
                </p>
            </div>
        </div>
    </div>
    
    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Return/Replacement Request</h3>
        
        @if(session('error'))
            <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('b2b.orders.return-request.submit', [$order, $orderItem]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 500; color: #1f2937; margin-bottom: 0.5rem;">Request Type *</label>
                <div style="display: flex; gap: 1rem;">
                    <label style="display: flex; align-items: center;">
                        <input type="radio" name="type" value="return" required style="margin-right: 0.5rem;">
                        Return
                    </label>
                    <label style="display: flex; align-items: center;">
                        <input type="radio" name="type" value="replacement" required style="margin-right: 0.5rem;">
                        Replacement
                    </label>
                </div>
                @error('type')
                    <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <div>
                    <label for="pieces" style="display: block; font-weight: 500; color: #1f2937; margin-bottom: 0.5rem;">Pieces</label>
                    <input type="number" id="pieces" name="pieces" min="1" 
                           max="{{ $orderItem->dispatched_quantity * $orderItem->product->per_quantity_pieces }}"
                           value="{{ old('pieces', 1) }}"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;" required>
                    <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">
                        Max pieces: {{ $orderItem->dispatched_quantity * $orderItem->product->per_quantity_pieces }}
                    </p>
                    @error('pieces')
                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="reason" style="display: block; font-weight: 500; color: #1f2937; margin-bottom: 0.5rem;">Reason *</label>
                <select id="reason" name="reason" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">
                    <option value="">Select a reason</option>
                    <option value="Damaged Product" {{ old('reason') == 'Damaged Product' ? 'selected' : '' }}>Damaged Product</option>
                    <option value="Defective Product" {{ old('reason') == 'Defective Product' ? 'selected' : '' }}>Defective Product</option>
                    <option value="Wrong Item Received" {{ old('reason') == 'Wrong Item Received' ? 'selected' : '' }}>Wrong Item Received</option>
                    <option value="Not as Described" {{ old('reason') == 'Not as Described' ? 'selected' : '' }}>Not as Described</option>
                    <option value="Changed Mind" {{ old('reason') == 'Changed Mind' ? 'selected' : '' }}>Changed Mind</option>
                    <option value="Other" {{ old('reason') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('reason')
                    <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div>
                    <label for="damage_proof_image" style="display: block; font-weight: 500; color: #1f2937; margin-bottom: 0.5rem;">Damage Proof Image *</label>
                    <input type="file" id="damage_proof_image" name="damage_proof_image" accept="image/*" required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                    @error('damage_proof_image')
                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="another_image" style="display: block; font-weight: 500; color: #1f2937; margin-bottom: 0.5rem;">Another Image</label>
                    <input type="file" id="another_image" name="another_image" accept="image/*"
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                    @error('another_image')
                        <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="description" style="display: block; font-weight: 500; color: #1f2937; margin-bottom: 0.5rem;">Description</label>
                <textarea id="description" name="description" rows="4" 
                          placeholder="Please provide additional details about the issue..." 
                          style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 1rem;">{{ old('description') }}</textarea>
                <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">
                    Please describe the issue in detail to help us process your request faster.
                </p>
                @error('description')
                    <div style="color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
            
            <div id="return-calculation" style="margin-bottom: 1.5rem; padding: 1rem; background-color: #f9fafb; border-radius: 0.375rem; border: 1px solid #e5e7eb; display: none;">
                <h4 style="font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">Estimated Return Value</h4>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="font-size: 0.875rem; color: #6b7280;">Units Total:</span>
                    <span id="units-total-val" style="font-size: 0.875rem; font-weight: 500; color: #111827;">₹0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.875rem; color: #6b7280;">Pieces Total (<span id="piece-rate-label">₹0.00</span>/pc):</span>
                    <span id="pieces-total-val" style="font-size: 0.875rem; font-weight: 500; color: #111827;">₹0.00</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-t: 1px solid #e5e7eb; pt: 0.5rem; margin-top: 0.5rem; font-weight: 700;">
                    <span style="font-size: 1rem; color: #111827;">Total Return Amount:</span>
                    <span id="grand-total-val" style="font-size: 1rem; color: #8B0000;">₹0.00</span>
                </div>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="{{ route('b2b.orders.show', $order) }}" class="btn btn-outline" style="padding: 0.75rem 1.5rem; border: 1px solid #d1d5db; color: #374151; text-decoration: none; border-radius: 0.375rem;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; background-color: #8B0000; color: white; border: none; border-radius: 0.375rem; cursor: pointer;">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const piecesInput = document.getElementById('pieces');
    const calcBox = document.getElementById('return-calculation');
    const unitsVal = document.getElementById('units-total-val');
    const piecesVal = document.getElementById('pieces-total-val');
    const grandVal = document.getElementById('grand-total-val');
    const pieceRateLabel = document.getElementById('piece-rate-label');

    const unitPrice = {{ $orderItem->price }};
    const piecePrice = {{ $orderItem->product->piece_price ?? 0 }};
    
    pieceRateLabel.textContent = '₹' + piecePrice.toFixed(2);

    function calculate() {
        const qty = parseInt(quantityInput.value) || 0;
        const pcs = parseInt(piecesInput.value) || 0;

        if (qty > 0 || pcs > 0) {
            calcBox.style.display = 'block';
            
            const uTotal = qty * unitPrice;
            const pTotal = pcs * piecePrice;
            const gTotal = uTotal + pTotal;

            unitsVal.textContent = '₹' + uTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            piecesVal.textContent = '₹' + pTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            grandVal.textContent = '₹' + gTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        } else {
            calcBox.style.display = 'none';
        }
    }

    quantityInput.addEventListener('input', calculate);
    piecesInput.addEventListener('input', calculate);
    
    // Initial calculation
    calculate();
});
</script>

<style>
.btn-outline {
    background-color: #f3f4f6;
    color: #374151;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
    border: 1px solid #e5e7eb;
}

.btn-outline:hover {
    background-color: #e5e7eb;
}

.btn-primary:hover {
    background-color: #6b0000;
}
</style>
@endsection