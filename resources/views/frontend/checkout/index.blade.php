@extends(auth()->check() && auth()->user()->customer && auth()->user()->customer->customer_type === 'dealer' ? 'frontend.b2b.layouts.app' : 'frontend.layouts.app')

@section('title', 'Checkout - V4 Kitchen Partner')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 1.5rem;">Checkout</h1>
    
    @if(session('error'))
        <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            {{ session('error') }}
        </div>
    @endif
    
    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf
        
        <div style="display: flex; flex-wrap: wrap; gap: 2rem;">
            <div style="flex: 2; min-width: 300px;">
                
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Billing Address</h2>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div style="grid-column: span 2;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Address *</label>
                            <input type="text" id="billing_address" name="billing_address" value="{{ old('billing_address', $customer->address ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;" required>
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">City *</label>
                            <input type="text" id="billing_city" name="billing_city" value="{{ old('billing_city', $customer->city ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;" required>
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">State *</label>
                            <input type="text" id="billing_state" name="billing_state" value="{{ old('billing_state', $customer->state ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;" required>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">ZIP Code *</label>
                            <input type="text" id="billing_zip" name="billing_zip" value="{{ old('billing_zip', $customer->zip_code ?? '') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;" required>
                        </div>

                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Country *</label>
                            <input type="text" id="billing_country" name="billing_country" value="{{ old('billing_country', $customer->country ?? 'India') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;" required>
                        </div>
                    </div>
                </div>
                
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937;">Shipping Address</h2>
                        <label style="display: flex; align-items: center; cursor: pointer; color: #8B0000; font-weight: 600;">
                            <input type="checkbox" id="use_same_address" name="use_same_address" value="1" {{ old('use_same_address') ? 'checked' : '' }} style="margin-right: 0.5rem;">
                            Same as billing address
                        </label>
                    </div>
                    
                    <div id="shipping_address_fields" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div style="grid-column: span 2;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Address *</label>
                            <input type="text" id="shipping_address" name="shipping_address" value="{{ old('shipping_address') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">City *</label>
                            <input type="text" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">State *</label>
                            <input type="text" id="shipping_state" name="shipping_state" value="{{ old('shipping_state') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">ZIP Code *</label>
                            <input type="text" id="shipping_zip" name="shipping_zip" value="{{ old('shipping_zip') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #4b5563;">Country *</label>
                            <input type="text" id="shipping_country" name="shipping_country" value="{{ old('shipping_country') }}" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                    </div>
                </div>

                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Payment Method</h2>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @if($customer->is_cod_allowed)
                        <label style="display: flex; align-items: center; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; cursor: pointer;">
                            <input type="radio" name="payment_method" value="cod" {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }} style="margin-right: 1rem;" required>
                            <div>
                                <span style="display: block; font-weight: 600;">Cash on Delivery</span>
                                <span style="font-size: 0.75rem; color: #6b7280;">Pay when the order arrives.</span>
                            </div>
                        </label>
                        @else
                        <label style="display: flex; align-items: center; padding: 1rem; border: 1px solid #f3f4f6; border-radius: 0.5rem; cursor: not-allowed; opacity: 0.6;">
                            <input type="radio" name="payment_method" value="cod" disabled style="margin-right: 1rem;">
                            <div>
                                <span style="display: block; font-weight: 600; color: #9ca3af;">Cash on Delivery (Locked)</span>
                                <span style="font-size: 0.75rem; color: #9ca3af;">As decided by Admin, this option is locked for you.</span>
                            </div>
                        </label>
                        <input type="hidden" name="payment_method_locked" value="bank_transfer">
                        @endif

                        <div style="border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
                            <label style="display: flex; align-items: center; padding: 1rem; cursor: pointer; background-color: #f9fafb;">
                                <input type="radio" name="payment_method" id="bank_transfer_radio" value="bank_transfer" {{ (old('payment_method') === 'bank_transfer' || !$customer->is_cod_allowed) ? 'checked' : '' }} style="margin-right: 1rem;">
                                <div>
                                    <span style="display: block; font-weight: 600;">Bank Transfer / Online</span>
                                    <span style="font-size: 0.75rem; color: #6b7280;">Direct transfer, UPI, or Card payments.</span>
                                </div>
                            </label>
                            
                            <div id="bank_transfer_options" style="padding: 1rem; border-top: 1px solid #e5e7eb; display: {{ (old('payment_method') === 'bank_transfer' || !$customer->is_cod_allowed) ? 'block' : 'none' }}; background-color: white;">
                                @if($customer->bank_transfer_discount > 0)
                                    <div style="margin-bottom: 1rem; padding: 0.5rem; background-color: #ecfdf5; border: 1px dashed #10b981; border-radius: 0.375rem; color: #065f46; font-size: 0.875rem; font-weight: 600;">
                                        ⭐ Get {{ $customer->bank_transfer_discount }}% extra discount when you pay via Bank Transfer!
                                    </div>
                                @endif
                                
                                <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="radio" name="bt_sub_option" value="direct" checked>
                                        <span style="font-size: 0.875rem;">Direct Bank Transfer (IMPS/NEFT/RTGS)</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="radio" name="bt_sub_option" value="card">
                                        <span style="font-size: 0.875rem;">Credit / Debit Card (Dummy)</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                        <input type="radio" name="bt_sub_option" value="upi">
                                        <span style="font-size: 0.875rem;">UPI (GPay / PhonePe / Paytm)</span>
                                    </label>
                                </div>
                                
                                <div id="dummy_payment_data" style="margin-top: 1rem; padding: 1rem; background-color: #f3f4f6; border-radius: 0.375rem; font-size: 0.75rem; color: #4b5563;">
                                    <p><strong>Bank Details:</strong><br>
                                    Bank Name: HDFC Bank<br>
                                    Account No: 50200012345678<br>
                                    IFSC: HDFC0001234<br>
                                    Beneficiary: Jivan Plastics Pvt Ltd</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="flex: 1; min-width: 300px;">
                <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; position: sticky; top: 1rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1.5rem; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem;">Order Review</h2>
                    
                    @php
                        $subtotal = 0; $totalGst = 0; $totalDiscount = 0;
                        $customerModel = auth()->user()->customer ?? null;
                    @endphp

                    <div style="margin-bottom: 1rem;">
                        @foreach($cart as $id => $item)
                            @php
                                $product = \App\Models\Product::find($id);
                                // session price is the actual pay price
                                $unitToPay = $item['price'];
                                // original_price from session, fallback to product price
                                $unitBase = $item['original_price'] ?? ($product ? $product->price : $item['price']);
                                
                                $lineBase = $unitBase * $item['quantity'];
                                $lineDisc = $unitToPay * $item['quantity'];
                                
                                $gstPercentage = $item['gst_percentage'] ?? ($product ? $product->gst_percentage : 0);
                                $lineGst = $lineDisc * ($gstPercentage / 100);
                                
                                $subtotal += $lineBase;
                                $totalDiscount += ($lineBase - $lineDisc);
                                $totalGst += $lineGst;
                            @endphp
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f9fafb;">
                                <div style="font-size: 0.875rem;">
                                    <strong>{{ $item['name'] }}</strong><br>
                                    <small>Qty: {{ $item['quantity'] }} × ₹{{ number_format($unitToPay, 2) }}</small>
                                    @if($lineBase > $lineDisc)
                                        <span style="background-color: #d1fae5; color: #065f46; font-size: 0.6rem; padding: 1px 4px; border-radius: 3px; font-weight: 800; text-transform: uppercase; margin-left: 0.5rem;">Saved ₹{{ number_format($lineBase - $lineDisc, 2) }}</span>
                                    @endif
                                    @php
                                        $isItemPieces = isset($item['is_pieces']) && $item['is_pieces'];
                                    @endphp
                                    @if(!$isItemPieces && $product && $product->per_quantity_pieces > 1)
                                        <div style="font-size: 0.7rem; color: #4f46e5; font-weight: 600; margin-top: 0.2rem;">
                                            Rate/pc: ₹{{ number_format($unitToPay / $product->per_quantity_pieces, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <span style="font-weight: 600;">₹{{ number_format($lineDisc + $lineGst, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #6b7280;">Subtotal:</span>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        
                        @if($totalDiscount > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #059669; font-weight: 600;">
                            <span>Dealer Discount:</span>
                            <span>- ₹{{ number_format($totalDiscount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #6b7280;">GST Total:</span>
                            <span>+ ₹{{ number_format($totalGst, 2) }}</span>
                        </div>
                        
                        <div id="bank_transfer_discount_row" style="display: none; justify-content: space-between; margin-bottom: 0.5rem; color: #10b981; font-weight: 600;">
                            <span>Bank Transfer Discount ({{ $customer->bank_transfer_discount }}%):</span>
                            <span>- ₹<span id="bt_discount_value">0.00</span></span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; padding-top: 1rem; border-top: 2px solid #f3f4f6; margin-top: 1rem;">
                            <span style="font-weight: 800; font-size: 1.25rem;">Grand Total:</span>
                            <span style="font-weight: 800; font-size: 1.25rem; color: #8B0000;">₹<span id="grand_total_display">{{ number_format(($subtotal - $totalDiscount) + $totalGst, 2) }}</span></span>
                        </div>
                    </div>
                    
                    <button type="submit" style="width: 100%; padding: 1rem; background-color: #8B0000; color: white; border: none; border-radius: 0.5rem; font-weight: 700; cursor: pointer; margin-top: 1.5rem;">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkbox = document.getElementById('use_same_address');
        const container = document.getElementById('shipping_address_fields');
        const fields = ['address', 'city', 'state', 'zip', 'country'];

        function syncAddresses() {
            if (checkbox.checked) {
                container.style.opacity = '0.5';
                fields.forEach(f => {
                    const billing = document.getElementById('billing_' + f);
                    const shipping = document.getElementById('shipping_' + f);
                    shipping.value = billing.value;
                    shipping.readOnly = true;
                });
            } else {
                container.style.opacity = '1';
                fields.forEach(f => {
                    const shipping = document.getElementById('shipping_' + f);
                    shipping.readOnly = false;
                });
            }
        }

        checkbox.addEventListener('change', syncAddresses);
        
        // Update shipping if billing changes while checked
        fields.forEach(f => {
            document.getElementById('billing_' + f).addEventListener('input', function() {
                if (checkbox.checked) syncAddresses();
            });
        });

        // Payment Method Logic
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const bankTransferOptions = document.getElementById('bank_transfer_options');
        const btDiscountRow = document.getElementById('bank_transfer_discount_row');
        const btDiscountValue = document.getElementById('bt_discount_value');
        const grandTotalDisplay = document.getElementById('grand_total_display');
        
        const baseGrandTotal = {{ ($subtotal - $totalDiscount) + $totalGst }};
        const btDiscountPercent = {{ $customer->bank_transfer_discount ?? 0 }};
        
        function updateOrderSummary() {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (selectedMethod === 'bank_transfer') {
                bankTransferOptions.style.display = 'block';
                
                if (btDiscountPercent > 0) {
                    const discount = (baseGrandTotal * btDiscountPercent) / 100;
                    btDiscountValue.innerText = discount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    btDiscountRow.style.display = 'flex';
                    grandTotalDisplay.innerText = (baseGrandTotal - discount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                } else {
                    btDiscountRow.style.display = 'none';
                    grandTotalDisplay.innerText = baseGrandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            } else {
                bankTransferOptions.style.display = 'none';
                btDiscountRow.style.display = 'none';
                grandTotalDisplay.innerText = baseGrandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        }
        
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', updateOrderSummary);
        });
        
        // Initial call
        updateOrderSummary();
    });
</script>
@endsection