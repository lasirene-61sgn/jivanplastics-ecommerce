@extends('frontend.b2b.layouts.app')

@section('title', $product->name . ' - Product Details')

@php
$customer = auth()->user()->customer ?? null;
$discountedPrice = $product->getB2BDiscountedPrice($customer);
$b2bMin = $product->min_order_qty_b2b ?? 1;
$piecesPerUnit = max(1, $product->per_quantity_pieces ?? 1);
$discountAmount = $product->price - $discountedPrice;
$pieceDiscount = $discountAmount / $piecesPerUnit;
$pieceNetRate = $discountedPrice / $piecesPerUnit;
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb / Back -->
    <div class="mb-8">
        <a href="{{ route('b2b.products') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-red-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Marketplace
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
            <!-- Product Image Section -->
            <div class="relative bg-slate-50 p-8 lg:p-12 flex items-center justify-center min-h-[400px] lg:min-h-[600px] border-b lg:border-b-0 lg:border-r border-slate-100">
                @php $imagePath = $product->image_url ?? ($product->images->first() ? $product->images->first()->image_path : null); @endphp
                <img src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder.jpg') }}"
                    alt="{{ $product->name }}"
                    class="max-w-full max-h-[500px] object-contain drop-shadow-2xl transition-transform hover:scale-105 duration-500">

                <!-- Badges -->
                <div class="absolute top-8 left-8 flex flex-col gap-3">
                    @if($product->min_order_qty_b2b)
                    <div class="bg-slate-900/90 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg border border-white/10">
                        MOQ: {{ $product->min_order_qty_b2b }}
                    </div>
                    @endif
                </div>

                <!-- Thumbnails if available -->
                @if($product->images->count() > 1)
                <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-2 px-8 overflow-x-auto">
                    @foreach($product->images as $img)
                    <div class="w-16 h-16 border-2 border-white rounded-lg overflow-hidden shadow-lg cursor-pointer hover:border-red-600 transition-colors">
                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Product Details Section -->
            @php
            $productSizes = $product->size ? explode(',', $product->size) : $product->variations->pluck('size')->unique()->filter()->map(fn($s) => trim($s))->toArray();
            $productThicknesses = $product->thickness ? explode(',', $product->thickness) : $product->variations->pluck('thickness')->unique()->filter()->map(fn($t) => trim($t))->toArray();
            $productColors = $product->color ? explode(',', $product->color) : $product->variations->pluck('color')->unique()->filter()->map(fn($c) => trim($c))->toArray();
            $cart = session('cart', []);
            $cartProductIds = collect($cart)->pluck('product_id')->map(fn($id) => (int)$id)->unique()->values()->toArray();
            $isProductInCart = in_array((int)$product->id, $cartProductIds);
            @endphp
            <div class="p-8 lg:p-12 flex flex-col h-full bg-white" x-data="{ 
                quantity: {{ $b2bMin }}, 
                dealerDiscountPercentage: {{ $product->getB2BDiscountPercentage($customer) }},
                basePrice: {{ $product->price > 0 ? $product->price : ($product->variations->first()->total_price ?? 0) }},
                basePieces: {{ $product->per_quantity_pieces ?? 1 }},
                thicknessPieces: @js($product->thickness_pieces ?? []),
                variations: @js($product->variations),
                selectedSize: '{{ trim($productSizes[0] ?? '') }}',
                selectedThickness: '{{ trim($productThicknesses[0] ?? '') }}',
                selectedColor: '{{ trim($productColors[0] ?? '') }}',
                isInCart: {{ $isProductInCart ? 'true' : 'false' }},
                isAdding: false,
                get activeVariation() {
                    const trim = (s) => (s || '').toString().trim();
                    return this.variations.find(v => 
                        (!v.size || trim(v.size) == trim(this.selectedSize)) && 
                        (!v.thickness || trim(v.thickness) == trim(this.selectedThickness)) && 
                        (!v.color || trim(v.color) == trim(this.selectedColor))
                    );
                },
                customerId: {{ $customer ? $customer->id : 'null' }},
                get currentPieces() {
                    if (this.activeVariation) return parseInt(this.activeVariation.total_pieces);
                    return parseInt(this.thicknessPieces[this.selectedThickness] || this.basePieces);
                },
                get dealerDiscountPercentage() {
                    if (this.activeVariation && this.activeVariation.dealer_discounts && this.customerId) {
                        let dDiscounts = this.activeVariation.dealer_discounts;
                        if (typeof dDiscounts === 'string') {
                            try { dDiscounts = JSON.parse(dDiscounts); } catch(e) {}
                        }
                        if (dDiscounts && dDiscounts[this.customerId] !== undefined && dDiscounts[this.customerId] !== null && dDiscounts[this.customerId] !== '') {
                            return parseFloat(dDiscounts[this.customerId]) || 0;
                        }
                    }
                    return parseFloat(this.baseDealerDiscountPercentage ?? this.discountPercentage ?? 0) || 0;
                },
                get dealerSpecialDiscountPercentage() {
                    if (this.activeVariation && this.activeVariation.dealer_special_discounts && this.customerId) {
                        let discounts = this.activeVariation.dealer_special_discounts;
                        if (typeof discounts === 'string') {
                            try { discounts = JSON.parse(discounts); } catch(e) {}
                        }
                        if (discounts && discounts[this.customerId] !== undefined && discounts[this.customerId] !== null && discounts[this.customerId] !== '') {
                            return parseFloat(discounts[this.customerId]) || 0;
                        }
                    }
                    return null;
                },
                get grossCartonPrice() {
                    if (this.activeVariation) {
                        return parseFloat(this.activeVariation.piece_price * this.activeVariation.total_pieces);
                    }
                    return parseFloat(this.basePrice);
                },
                get basePriceAfterSpecial() {
                    let base = this.grossCartonPrice;
                    let spec = this.dealerSpecialDiscountPercentage;
                    if (spec !== null && spec > 0) {
                        return base - (base * spec / 100);
                    }
                    return base;
                },
                get unitPrice() {
                    let base = this.basePriceAfterSpecial;
                    let dDisc = this.dealerDiscountPercentage;
                    if (dDisc > 0) {
                        return base - (base * dDisc / 100);
                    }
                    return base;
                },
                get strikethroughPrice() {
                    return this.basePriceAfterSpecial;
                },
                get pieceNetRate() { return (this.unitPrice / this.currentPieces).toFixed(2) },
                get pieceGrossRate() { return (this.strikethroughPrice / this.currentPieces).toFixed(2) },
                get total() { return (this.quantity * this.unitPrice).toFixed(2) },
                get totalPieces() { return (this.quantity * this.currentPieces) },
                get canAddToCart() {
                    if ({{ count($productSizes) > 0 ? 'true' : 'false' }} && !this.selectedSize) return false;
                    if ({{ count($productThicknesses) > 0 ? 'true' : 'false' }} && !this.selectedThickness) return false;
                    if ({{ count($productColors) > 0 ? 'true' : 'false' }} && !this.selectedColor) return false;
                    return true;
                }
            }">
                <div class="mb-auto">
                    <!-- Category & Meta -->
                    <div class="flex items-center gap-3 mb-4">
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-black uppercase tracking-widest">
                            {{ $product->category->name ?? 'General' }}
                        </span>
                        @if($product->subcategory)
                        <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $product->subcategory->name }}</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl lg:text-4xl font-black text-slate-900 mb-6 leading-tight uppercase tracking-tight">{{ $product->name }}</h1>

                    <!-- Attributes -->
                    @if($product->size || $product->thickness || $product->color)
                    <div class="space-y-4 mb-8 pb-8 border-b border-slate-100">
                        @if($product->size)
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest min-w-[100px]">Sizes</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productSizes as $sz)
                                @php $sz = trim($sz); @endphp
                                <button type="button" @click="selectedSize = '{{ $sz }}'"
                                    :class="selectedSize == '{{ $sz }}' ? 'bg-red-600 border-red-600 text-white' : 'bg-white text-slate-700 border-slate-100 hover:border-red-600'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-black uppercase tracking-tight transition-all">
                                    {{ $sz }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($product->thickness)
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest min-w-[100px]">Thickness</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productThicknesses as $tk)
                                @php $tk = trim($tk); @endphp
                                <button type="button" @click="selectedThickness = '{{ $tk }}'"
                                    :class="selectedThickness == '{{ $tk }}' ? 'bg-red-600 border-red-600 text-white' : 'bg-white text-slate-700 border-slate-100 hover:border-red-600'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-black uppercase tracking-tight transition-all">
                                    {{ $tk }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($product->color)
                        <div class="flex flex-wrap gap-2 items-center">
                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest min-w-[100px]">Colors</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productColors as $cl)
                                @php $cl = trim($cl); @endphp
                                <button type="button" @click="selectedColor = '{{ $cl }}'"
                                    :class="selectedColor == '{{ $cl }}' ? 'bg-red-600 border-red-600 text-white' : 'bg-white text-slate-700 border-slate-100 hover:border-red-600'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-black uppercase tracking-tight transition-all">
                                    {{ $cl }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Description -->
                    @if($product->description)
                    <div class="mb-8 border-t border-slate-100 pt-6">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4 text-center lg:text-left">Product Information</h3>
                        <div class="prose prose-sm prose-slate max-w-none text-slate-600 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
                            {!! $product->description !!}
                        </div>
                    </div>
                    <style>
                        .custom-scrollbar::-webkit-scrollbar {
                            width: 6px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-track {
                            background: #f1f5f9; 
                            border-radius: 4px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-thumb {
                            background: #cbd5e1; 
                            border-radius: 4px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                            background: #94a3b8; 
                        }
                    </style>
                    @endif
                </div>

                <!-- Pricing & Cart Logic -->

                <div class="mt-8 bg-slate-50 rounded-2xl p-6 lg:p-8 border border-slate-100">
                    <!-- Price Breakdown -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-slate-200/50">
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Net Rate (Per PC)</span>
                            <div class="flex items-baseline gap-2">
                                <template x-if="dealerDiscountPercentage > 0">
                                    <span class="text-base text-slate-400 line-through font-normal">₹<span x-text="pieceGrossRate"></span></span>
                                </template>
                                <span class="text-2xl font-black text-slate-900 tracking-tighter">₹<span x-text="pieceNetRate"></span></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Unit Rate (Per Carton)</span>
                            <div class="flex flex-col items-end">
                                <div class="flex items-baseline gap-2">
                                    <template x-if="dealerDiscountPercentage > 0">
                                        <span class="text-base text-slate-400 line-through font-normal">₹<span x-text="strikethroughPrice.toFixed(2)"></span></span>
                                    </template>
                                    <span class="text-2xl font-black text-red-600 tracking-tighter">₹<span x-text="unitPrice.toFixed(2)"></span></span>
                                </div>
                                <template x-if="dealerDiscountPercentage > 0">
                                    <span class="mt-1 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[11px] font-bold">
                                        Special Disc: <span x-text="dealerDiscountPercentage"></span>% OFF
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Quantity (Units)</span>
                            <span class="text-[10px] text-slate-400 font-medium">Min Order: {{ $b2bMin }}</span>
                        </div>
                        <div class="flex items-center bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm h-12">
                            <button type="button" @click="if(quantity > {{ $b2bMin }}) quantity--" class="w-12 h-full hover:bg-slate-50 text-slate-600 font-bold text-lg transition-colors border-r border-slate-200 flex items-center justify-center">-</button>
                            <input type="number" x-model.number="quantity" min="{{ $b2bMin }}" class="w-20 text-center text-lg font-bold border-none focus:ring-0 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none text-slate-900">
                            <button type="button" @click="quantity++" class="w-12 h-full hover:bg-slate-50 text-slate-600 font-bold text-lg transition-colors border-l border-slate-200 flex items-center justify-center">+</button>
                        </div>
                    </div>

                    <!-- Final Calculation -->
                    <div class="bg-white rounded-xl p-4 border border-slate-200 mb-6 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Total Pieces</span>
                            <span class="font-black text-slate-800"><span x-text="totalPieces"></span> pcs</span>
                        </div>
                        <div class="flex justify-between items-center text-lg">
                            <span class="font-black text-slate-900 uppercase tracking-tight">Total Payable</span>
                            <span class="font-black text-red-600 tracking-tighter">₹<span x-text="total"></span></span>
                        </div>
                    </div>

                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" @submit.prevent="
                        if (!canAddToCart || isAdding) return;
                        
                        if (isInCart) {
                            if (!confirm('This product is already in your cart. Do you want to add more to cart?')) {
                                return;
                            }
                        }
                        
                        let form = $event.target;
                        isAdding = true;
                        
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
                                isInCart = true;
                                
                                // Update global cart counts if they exist
                                let cartCountEls = document.querySelectorAll('.cart-count, .cart-counter, [data-cart-count]');
                                cartCountEls.forEach(el => {
                                    el.innerText = data.cart_count;
                                    el.style.display = data.cart_count > 0 ? 'flex' : 'none';
                                });
                                
                                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                                
                                if(typeof notyf !== 'undefined') notyf.success(data.message);
                                else if(typeof toastr !== 'undefined') toastr.success(data.message);
                            } else {
                                if(typeof notyf !== 'undefined') notyf.error(data.message || 'Error adding to cart');
                                else if(typeof toastr !== 'undefined') toastr.error(data.message || 'Error adding to cart');
                                else alert(data.message || 'Error adding to cart');
                            }
                        })
                        .catch(error => {
                            isAdding = false;
                            if(typeof notyf !== 'undefined') notyf.error('Error adding to cart');
                            else if(typeof toastr !== 'undefined') toastr.error('Error adding to cart');
                            else alert('Error adding to cart');
                        });
                    ">
                        @csrf
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="size" :value="selectedSize">
                        <input type="hidden" name="thickness" :value="selectedThickness">
                        <input type="hidden" name="color" :value="selectedColor">

                        <button type="submit"
                            :disabled="!canAddToCart || isAdding"
                            :class="!canAddToCart ? 'bg-slate-200 cursor-not-allowed text-slate-400' : (isInCart ? 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-xl shadow-emerald-200' : 'bg-slate-900 text-white hover:bg-red-600 shadow-xl shadow-slate-200 hover:shadow-red-200')"
                            class="w-full flex items-center justify-center gap-3 py-5 rounded-xl font-black text-sm uppercase tracking-widest active:scale-95 transition-all">
                            <template x-if="isAdding">
                                <svg class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <template x-if="!isAdding && isInCart">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </template>
                            <template x-if="!isAdding && !isInCart">
                                <svg class="w-5 h-5 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </template>
                            <span x-text="!canAddToCart ? 'Please Select Options' : (isAdding ? (isInCart ? 'Adding More...' : 'Adding...') : (isInCart ? 'Already in Cart ✓' : 'Add To Cart'))"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection