@extends('frontend.b2b.layouts.app')

@section('title', 'Exclusive Dealer Discounts - V4 Kitchen Partner')

@section('content')
<div class="space-y-10">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight text-uppercase">Partner <span class="text-red-600">Discounts</span></h1>
            <p class="text-slate-500 font-medium mt-1">Exclusive rates and volume benefits for your dealership.</p>
        </div>
        
        @php
            $cartItems = session('cart', []);
            $totalItems = collect($cartItems)->sum('quantity');
        @endphp
        @if($totalItems > 0)
            <a href="{{ route('cart.index') }}" class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl group transition-all hover:bg-emerald-100 shadow-lg shadow-emerald-500/5">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest leading-none">Your Cart</p>
                    <p class="text-sm font-bold text-slate-900 mt-0.5">{{ $totalItems }} Items Ready &rarr;</p>
                </div>
            </a>
        @endif
    </div>

    @if($products->count() > 0)
        <!-- Featured Discount Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                @php
                    // Prefer variation with explicit discount for this customer, or first variation
                    $discountedVariation = $product->variations->first(function($v) use ($customer) {
                        return ($v->getDealerDiscountForDealer($customer) > 0) || ($v->getSpecialDiscountForDealer($customer) > 0);
                    }) ?? $product->variations->first();

                    $activeVariation = $discountedVariation;
                    $discountedPrice = $activeVariation 
                        ? $activeVariation->getB2BDiscountedPrice($customer) 
                        : $product->getB2BDiscountedPrice($customer);
                    $dealerDiscount = $activeVariation
                        ? $activeVariation->getDealerDiscountForDealer($customer)
                        : $product->getB2BDiscountPercentage($customer);
                    
                    // Effective gross rate after subtracting special discount
                    $grossPrice = $activeVariation 
                        ? $activeVariation->getEffectiveBasePriceForDealer($customer) 
                        : ($product ? $product->getEffectiveBasePriceForDealer($customer) : 0);

                    $minQty = $product->min_order_qty_b2b ?? 1;
                    $piecesPerUnit = $activeVariation ? $activeVariation->total_pieces : max(1, $product->per_quantity_pieces ?? 1);
                    $gstPercentage = $activeVariation ? (float)$activeVariation->gst_percentage : (float)($product->gst_percentage ?? 0);
                @endphp
                <div class="group bg-white border border-slate-100 rounded-3xl p-6 hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 flex flex-col"
                     x-data="{ 
                        quantity: {{ $minQty }}, 
                        unitPrice: {{ $discountedPrice }},
                        grossPrice: {{ $grossPrice }},
                        dealerDiscount: {{ $dealerDiscount }},
                        piecesPerUnit: {{ $piecesPerUnit }},
                        gstPercentage: {{ $gstPercentage }},
                        selectedVariationId: '{{ $activeVariation ? $activeVariation->id : '' }}',
                        selectedSize: '{{ $activeVariation ? $activeVariation->size : '' }}',
                        selectedThickness: '{{ $activeVariation ? $activeVariation->thickness : '' }}',
                        selectedColor: '{{ $activeVariation ? $activeVariation->color : '' }}',
                        isAdding: false,
                        justAdded: false,
                        isInCart: {{ in_array($product->id, array_column(session('cart', []), 'product_id')) ? 'true' : 'false' }},
                        get pieceNetRate() { return (this.unitPrice / this.piecesPerUnit).toFixed(2) },
                        get pieceGrossRate() { return (this.grossPrice / this.piecesPerUnit).toFixed(2) },
                        get total() { return (this.quantity * this.unitPrice).toFixed(2) },
                        get totalPieces() { return (this.quantity * this.piecesPerUnit) },
                        get gstAmount() { return (this.total * (this.gstPercentage / 100)).toFixed(2) },
                        get netTotalWithGst() { return (parseFloat(this.total) + parseFloat(this.gstAmount)).toFixed(2) }
                     }">
                    <div class="flex items-center gap-6 mb-6">
                        <div class="relative w-24 h-24 rounded-2xl bg-slate-50 overflow-hidden flex-shrink-0 group-hover:scale-105 transition-transform duration-500 shadow-sm border border-slate-100">
                            @if($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-200">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">{{ $product->category->name ?? 'General' }}</span>
                            <h3 class="font-black text-slate-900 truncate uppercase tracking-tighter text-lg leading-tight">{{ $product->name }}</h3>
                            
                            @if($product->variations->count() > 1)
                                <div class="mt-2">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Specification:</label>
                                    <select x-model="selectedVariationId"
                                            @change="
                                                let selectedOpt = $event.target.options[$event.target.selectedIndex];
                                                selectedSize = selectedOpt.dataset.size || '';
                                                selectedThickness = selectedOpt.dataset.thickness || '';
                                                selectedColor = selectedOpt.dataset.color || '';
                                                unitPrice = parseFloat(selectedOpt.dataset.price) || 0;
                                                grossPrice = parseFloat(selectedOpt.dataset.gross) || 0;
                                                dealerDiscount = parseFloat(selectedOpt.dataset.discount) || 0;
                                                piecesPerUnit = parseInt(selectedOpt.dataset.pieces) || 1;
                                                gstPercentage = parseFloat(selectedOpt.dataset.gst) || 0;
                                            "
                                            class="w-full text-xs font-bold border border-slate-200 rounded-xl p-2 bg-slate-50 focus:ring-1 focus:ring-emerald-500 outline-none">
                                        @foreach($product->variations as $var)
                                            @php
                                                $varDisc = $var->getDealerDiscountForDealer($customer);
                                                $varPrice = $var->getB2BDiscountedPrice($customer);
                                                $varGross = $var->getEffectiveBasePriceForDealer($customer);
                                            @endphp
                                            <option value="{{ $var->id }}"
                                                    data-size="{{ $var->size }}"
                                                    data-thickness="{{ $var->thickness }}"
                                                    data-color="{{ $var->color }}"
                                                    data-price="{{ $varPrice }}"
                                                    data-gross="{{ $varGross }}"
                                                    data-discount="{{ $varDisc }}"
                                                    data-pieces="{{ $var->total_pieces }}"
                                                    data-gst="{{ $var->gst_percentage }}"
                                                    {{ $activeVariation && $activeVariation->id == $var->id ? 'selected' : '' }}>
                                                {{ $var->size ?? '' }} {{ $var->thickness ? '| Thk: ' . $var->thickness : '' }} {{ $var->color ? '| Color: ' . $var->color : '' }} ({{ $var->total_pieces }} pcs) - ₹{{ number_format($varPrice, 2) }} @if($varDisc > 0) [{{ $varDisc }}% OFF] @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($activeVariation)
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @if($activeVariation->size)
                                        <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">Size: {{ $activeVariation->size }}</span>
                                    @endif
                                    @if($activeVariation->thickness)
                                        <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">Thk: {{ $activeVariation->thickness }}</span>
                                    @endif
                                    @if($activeVariation->color)
                                        <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">Color: {{ $activeVariation->color }}</span>
                                    @endif
                                    <span class="text-[10px] font-bold bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded border border-emerald-200">{{ $activeVariation->total_pieces }} pcs / unit</span>
                                </div>
                            @endif

                            <div class="mt-4 space-y-2">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Rate (Per PC):</span>
                                    <div class="flex items-baseline gap-2">
                                        <template x-if="dealerDiscount > 0">
                                            <span class="text-xs text-slate-400 line-through">₹<span x-text="pieceGrossRate"></span></span>
                                        </template>
                                        <span class="text-xs font-black text-slate-900 tracking-tighter">₹<span x-text="pieceNetRate"></span></span>
                                    </div>
                                </div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Rate (Per Carton):</span>
                                    <div class="flex items-baseline gap-2">
                                        <template x-if="dealerDiscount > 0">
                                            <span class="text-sm text-slate-400 line-through">₹<span x-text="grossPrice.toFixed(2)"></span></span>
                                        </template>
                                        <span class="text-xl font-black text-emerald-600 tracking-tight">₹<span x-text="unitPrice.toFixed(2)"></span></span>
                                    </div>
                                </div>
                                <template x-if="dealerDiscount > 0">
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold">
                                        Special Disc: <span x-text="dealerDiscount"></span>% OFF
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 flex-1">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-3 bg-slate-50 rounded-2xl flex flex-col justify-center border border-slate-100/50 group-hover:border-emerald-100 transition-colors">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Per PC Rate</p>
                                <p class="text-sm font-black text-slate-900 tracking-tight">₹<span x-text="pieceNetRate"></span></p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-2xl flex flex-col justify-center border border-slate-100/50 group-hover:border-slate-200 transition-colors text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Order MOQ</p>
                                <p class="text-sm font-black text-slate-900 tracking-tight">{{ $minQty }} Case(s)</p>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Select Quantity</span>
                                <div class="flex items-center bg-white border border-slate-200 rounded-lg overflow-hidden h-8">
                                    <button type="button" @click="if(quantity > {{ $minQty }}) quantity--" class="px-2 hover:bg-slate-50 text-slate-600 font-bold transition-colors border-r border-slate-200">-</button>
                                    <input type="number" x-model.number="quantity" min="{{ $minQty }}" class="w-12 text-center text-xs font-bold border-none focus:ring-0 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button type="button" @click="quantity++" class="px-2 hover:bg-slate-50 text-slate-600 font-bold transition-colors border-l border-slate-200">+</button>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-200/50 space-y-2">
                                <div class="flex justify-between items-center">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Total Pieces</p>
                                    <p class="text-[10px] font-bold text-slate-600 px-2 py-0.5 bg-white rounded border border-slate-100"><span x-text="totalPieces"></span> pcs</p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Calculation (Excl. GST)</p>
                                    <p class="text-[10px] font-bold text-slate-600"><span x-text="quantity"></span> units x ₹<span x-text="unitPrice.toFixed(2)"></span></p>
                                </div>
                                <div class="flex justify-between items-center">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Net Subtotal</p>
                                    <p class="text-xs font-black text-slate-900">₹<span x-text="total"></span></p>
                                </div>
                                <div class="flex justify-between items-center text-slate-600">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Tax (GST <span x-text="gstPercentage"></span>%)</p>
                                    <p class="text-xs font-bold text-emerald-700">+ ₹<span x-text="gstAmount"></span></p>
                                </div>
                                <div class="flex justify-between items-end mt-2 pt-2 border-t border-slate-200/30">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total (Incl. GST)</p>
                                        <p class="text-lg font-black text-red-600 tracking-tighter">₹<span x-text="netTotalWithGst"></span></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Units</p>
                                        <p class="text-xs font-black text-slate-900 tracking-tight"><span x-text="quantity"></span> Case(s)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-6" @submit.prevent="
                        if (isAdding || justAdded) return;
                        isAdding = true;
                        let form = $event.target;
                        let prodDetails = {
                            name: '{{ addslashes($product->name) }}',
                            image: '{{ $product->images->first() ? asset("storage/" . $product->images->first()->image_path) : "" }}',
                            qty: quantity,
                            specs: (selectedSize ? 'Size: ' + selectedSize : '') + (selectedThickness ? ' | Thk: ' + selectedThickness : '') + (selectedColor ? ' | Color: ' + selectedColor : ''),
                            pieces: totalPieces,
                            price: netTotalWithGst
                        };
                        fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            isAdding = false;
                            if(data.success) {
                                justAdded = true;
                                isInCart = true;
                                let cartCountEls = document.querySelectorAll('.cart-count, .cart-counter, [data-cart-count]');
                                cartCountEls.forEach(el => {
                                    el.innerText = data.cart_count;
                                    el.style.display = data.cart_count > 0 ? 'flex' : 'none';
                                    el.classList.add('animate-bounce');
                                    setTimeout(() => el.classList.remove('animate-bounce'), 1000);
                                });
                                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                                
                                if(typeof showAddedToCartModal === 'function') {
                                    showAddedToCartModal(prodDetails);
                                } else if(typeof showCartToast === 'function') {
                                    showCartToast(data.message || 'Product added to cart successfully!');
                                }

                                setTimeout(() => {
                                    justAdded = false;
                                }, 1000);
                            } else {
                                if(typeof showCartToast === 'function') {
                                    showCartToast(data.message || 'Error adding to cart', 'error');
                                } else if(typeof notyf !== 'undefined') {
                                    notyf.error(data.message || 'Error adding to cart');
                                } else {
                                    alert(data.message || 'Error adding to cart');
                                }
                            }
                        })
                        .catch(error => {
                            isAdding = false;
                            justAdded = false;
                            form.submit();
                        });
                    ">
                        @csrf
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="variation_id" :value="selectedVariationId">
                        <input type="hidden" name="size" :value="selectedSize">
                        <input type="hidden" name="thickness" :value="selectedThickness">
                        <input type="hidden" name="color" :value="selectedColor">
                        <button type="submit" 
                                :disabled="isAdding" 
                                :class="justAdded 
                                    ? 'bg-emerald-600 text-white scale-[1.03] ring-4 ring-emerald-300/60 shadow-xl shadow-emerald-200' 
                                    : (isInCart 
                                        ? 'bg-emerald-700 text-white hover:bg-emerald-800 shadow-xl shadow-emerald-200/50' 
                                        : 'bg-slate-900 text-white hover:bg-emerald-600 shadow-xl shadow-slate-200')"
                                class="w-full flex items-center justify-center gap-2 py-4 rounded-xl font-black text-xs uppercase tracking-widest active:scale-95 transition-all duration-300">
                            
                            <!-- Loading Spinner State -->
                            <template x-if="isAdding">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Adding to Cart...</span>
                                </div>
                            </template>

                            <!-- 1-Second Success Animation State -->
                            <template x-if="!isAdding && justAdded">
                                <div class="flex items-center gap-2 animate-pulse">
                                    <svg class="w-5 h-5 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="tracking-wider">✓ Added Successfully!</span>
                                </div>
                            </template>

                            <!-- In Cart State -->
                            <template x-if="!isAdding && !justAdded && isInCart">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>✓ In Cart - Add More</span>
                                </div>
                            </template>

                            <!-- Default State -->
                            <template x-if="!isAdding && !justAdded && !isInCart">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    <span>Add To Cart</span>
                                </div>
                            </template>
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <div class="py-32 text-center bg-white border-2 border-dashed border-slate-200 rounded-[3rem]">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 7h.01M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path></svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">No Special Discounts</h3>
            <p class="text-slate-500 font-medium mt-2 max-w-xs mx-auto text-sm leading-relaxed">Currently there are no dynamic discounts available for your account. Standard trade rates apply.</p>
        </div>
    @endif
</div>

<!-- Animated Add to Cart Success Modal -->
<div id="cart-success-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300">
    <div id="cart-modal-card" class="relative w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 text-center transform transition-all duration-300">
        <!-- Close Button -->
        <button type="button" onclick="closeCartModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Animated SVG Checkmark Icon -->
        <div class="w-16 h-16 mx-auto mb-3.5 relative flex items-center justify-center">
            <svg id="cart-modal-svg" class="w-16 h-16 text-emerald-500" viewBox="0 0 52 52">
                <circle class="animated-check-circle" cx="26" cy="26" r="24" fill="none" stroke="currentColor" stroke-width="3"></circle>
                <path class="animated-check-path" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" d="M14 27l8 8 16-16"></path>
            </svg>
        </div>

        <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-black uppercase tracking-widest mb-1.5">
            Added to Cart
        </span>
        <h3 class="text-lg font-black text-slate-900 tracking-tight leading-tight mb-1">
            Your Product Added Successfully!
        </h3>
        <p class="text-xs text-slate-500 mb-4">
            Item has been safely added to your order cart.
        </p>

        <!-- Product Preview Box -->
        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100 text-left mb-4">
            <div id="modal-product-img-wrap" class="w-14 h-14 rounded-xl bg-white overflow-hidden flex-shrink-0 border border-slate-200/60 flex items-center justify-center">
                <img id="modal-product-img" src="" alt="Product" class="w-full h-full object-cover hidden">
                <div id="modal-product-img-placeholder" class="text-slate-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h4 id="modal-product-name" class="text-xs font-black text-slate-900 truncate uppercase">Product Name</h4>
                <p id="modal-product-specs" class="text-[10px] text-slate-500 font-medium truncate mt-0.5">Size / Color</p>
                <div class="flex items-center justify-between mt-1">
                    <span id="modal-product-qty" class="text-[11px] font-bold text-slate-700">1 Case (5 pcs)</span>
                    <span id="modal-product-price" class="text-xs font-black text-emerald-600">₹0.00</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="closeCartModal()" class="w-full py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                Continue
            </button>
            <a href="{{ route('cart.index') }}" class="w-full py-2.5 px-3 bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-colors flex items-center justify-center gap-1 shadow-md shadow-red-200">
                View Cart →
            </a>
        </div>

        <!-- Auto-closing progress timer -->
        <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden mt-4">
            <div id="modal-progress-bar" class="bg-emerald-500 h-full w-full rounded-full"></div>
        </div>
    </div>
</div>

<style>
@keyframes checkCircleAnim {
    0% { stroke-dashoffset: 166; transform: scale(0.85); }
    100% { stroke-dashoffset: 0; transform: scale(1); }
}
@keyframes checkPathAnim {
    0% { stroke-dashoffset: 48; }
    100% { stroke-dashoffset: 0; }
}
.animated-check-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    animation: checkCircleAnim 0.45s ease-out forwards;
}
.animated-check-path {
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: checkPathAnim 0.35s ease-out 0.35s forwards;
}
@keyframes modalPopupIn {
    0% { opacity: 0; transform: scale(0.8) translateY(20px); }
    50% { transform: scale(1.02) translateY(-2px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes modalPopupOut {
    0% { opacity: 1; transform: scale(1); }
    100% { opacity: 0; transform: scale(0.85) translateY(10px); }
}
.anim-modal-in {
    animation: modalPopupIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.anim-modal-out {
    animation: modalPopupOut 0.25s ease-in forwards;
}
</style>

<script>
let cartModalTimer = null;

function showAddedToCartModal(details) {
    let modal = document.getElementById('cart-success-modal');
    let card = document.getElementById('cart-modal-card');
    let nameEl = document.getElementById('modal-product-name');
    let specsEl = document.getElementById('modal-product-specs');
    let qtyEl = document.getElementById('modal-product-qty');
    let priceEl = document.getElementById('modal-product-price');
    let imgEl = document.getElementById('modal-product-img');
    let imgPlaceholder = document.getElementById('modal-product-img-placeholder');
    let progressBar = document.getElementById('modal-progress-bar');
    let svgEl = document.getElementById('cart-modal-svg');

    if (!modal) return;

    // Set details
    if (nameEl) nameEl.innerText = details.name || 'Product';
    if (specsEl) {
        specsEl.innerText = details.specs || '';
        specsEl.style.display = details.specs ? 'block' : 'none';
    }
    if (qtyEl) {
        qtyEl.innerText = (details.qty || 1) + ' Case(s)' + (details.pieces ? ' (' + details.pieces + ' pcs)' : '');
    }
    if (priceEl) {
        let numPrice = parseFloat(details.price) || 0;
        priceEl.innerText = '₹' + numPrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    if (details.image && imgEl) {
        imgEl.src = details.image;
        imgEl.classList.remove('hidden');
        if (imgPlaceholder) imgPlaceholder.classList.add('hidden');
    } else if (imgEl && imgPlaceholder) {
        imgEl.classList.add('hidden');
        imgPlaceholder.classList.remove('hidden');
    }

    // Replay checkmark animation
    if (svgEl) {
        let clone = svgEl.cloneNode(true);
        svgEl.parentNode.replaceChild(clone, svgEl);
    }

    // Reset and start progress bar
    if (progressBar) {
        progressBar.style.transition = 'none';
        progressBar.style.width = '100%';
        setTimeout(() => {
            progressBar.style.transition = 'width 1.5s linear';
            progressBar.style.width = '0%';
        }, 50);
    }

    // Display modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (card) {
        card.classList.remove('anim-modal-out');
        card.classList.add('anim-modal-in');
    }

    // Auto-close after 1.6s
    if (cartModalTimer) clearTimeout(cartModalTimer);
    cartModalTimer = setTimeout(() => {
        closeCartModal();
    }, 1600);
}

function closeCartModal() {
    let modal = document.getElementById('cart-success-modal');
    let card = document.getElementById('cart-modal-card');
    if (!modal || modal.classList.contains('hidden')) return;

    if (card) {
        card.classList.remove('anim-modal-in');
        card.classList.add('anim-modal-out');
    }
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (card) card.classList.remove('anim-modal-out');
    }, 240);
}

// Close when clicking on backdrop
document.addEventListener('click', function(e) {
    let modal = document.getElementById('cart-success-modal');
    if (modal && e.target === modal) {
        closeCartModal();
    }
});
</script>
@endsection