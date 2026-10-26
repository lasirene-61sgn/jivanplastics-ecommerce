@extends('layouts.sales-team')

@section('title', 'Enhanced Dealer Support - Sales Team Portal')

@section('header', 'Enhanced Dealer Support')

@section('content')
<div class="max-w-7xl mx-auto pb-20 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.297A8.609 8.609 0 0112 19c2.107 0 4.213.197 6.321.588L18 5.882A8.614 8.614 0 0012 6c-2.107 0-4.213.197-6.321.588L6 19.297A8.609 8.609 0 017 19c2.107 0 4.213.197 6.321.588z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Enhanced Dealer Support</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase italic">Advanced Catalog &amp; Cart Ordering Desk</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sales-team.dealer-support') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-indigo-100 transition-all">
                <i class="fas fa-headset mr-2"></i> Switch to Basic Desk
            </a>
            <a href="{{ route('sales-team.dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm text-sm font-bold flex justify-between items-center animate-fadeIn">
            <span><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="text-green-500 hover:text-green-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm text-sm font-bold flex justify-between items-center animate-fadeIn">
            <span><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Dealer Selection Panel -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400 flex justify-between items-center">
                    <span>Assigned Dealers</span>
                    <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-[9px] font-bold">{{ $dealers->count() }} total</span>
                </div>
                <div class="p-4 overflow-y-auto max-h-[600px] custom-scrollbar">
                    @if($dealers->count() > 0)
                        <div class="space-y-2">
                            @foreach($dealers as $dealer)
                                <a href="#" 
                                   class="dealer-item block p-4 rounded-2xl border transition-all group {{ session('selected_dealer_id') == $dealer->id ? 'border-indigo-500 bg-indigo-50/80 shadow-sm' : 'border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/40' }}"
                                   data-dealer-id="{{ $dealer->id }}"
                                   data-dealer-name="{{ $dealer->company_name ?? $dealer->name }}">
                                    <div class="flex justify-between items-start mb-1">
                                        <h6 class="text-sm font-black text-slate-900 group-hover:text-indigo-600 truncate max-w-[180px]">{{ $dealer->company_name ?? $dealer->name }}</h6>
                                        <div class="h-2.5 w-2.5 rounded-full {{ session('selected_dealer_id') == $dealer->id ? 'bg-indigo-600' : 'bg-slate-200 group-hover:bg-indigo-400' }} transition-colors"></div>
                                    </div>
                                    <p class="text-[11px] text-slate-400 font-medium truncate mb-2">{{ $dealer->email }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                            {{ $dealer->phone ?? 'NO PHONE' }}
                                        </span>
                                        @if($dealer->city)
                                            <span class="text-[9px] font-bold text-slate-400">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $dealer->city }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center text-slate-400 italic text-sm">No dealers assigned to your account.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Ordering Panel -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Cart Summary Banner -->
            @php
                $cartItems = session('sales_team_portal_cart', []);
                $totalItems = collect($cartItems)->sum('quantity');
                $totalAmount = collect($cartItems)->sum(function($item) {
                    return $item['quantity'] * $item['price'];
                });
            @endphp
            
            @if($totalItems > 0)
            <div class="bg-indigo-50/80 border border-indigo-200 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <strong class="text-indigo-950 font-black">{{ $totalItems }} item(s) in active cart</strong>
                        <span class="text-indigo-700 font-bold ml-2">Total: ₹{{ number_format($totalAmount, 2) }}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button id="clear-cart" class="text-xs font-black uppercase tracking-wider text-rose-600 hover:text-rose-800 px-3 py-2 bg-white rounded-xl border border-rose-200 hover:bg-rose-50 transition-all">
                        Clear Cart
                    </button>
                    <button id="place-order-btn" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-all active:scale-95">
                        Place Order
                    </button>
                </div>
            </div>
            @endif

            <!-- Product Selection Area -->
            <div class="bg-slate-900 rounded-[2.5rem] p-1 border-4 border-slate-800 shadow-2xl">
                <div class="bg-white rounded-[2.1rem] overflow-hidden">
                    <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Product Catalog</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Select items to build an order</p>
                        </div>
                        <span class="text-[10px] font-black bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full uppercase tracking-tighter">B2B Quick Order</span>
                    </div>

                    <!-- Product Filters -->
                    <div class="p-6 bg-slate-50 border-b border-slate-100">
                        <form method="GET" action="{{ route('sales-team.enhanced-dealer-support') }}" class="flex flex-wrap gap-4 items-center">
                            <div class="flex-1 min-w-[200px] relative">
                                <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-xs"></i>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products by name or SKU..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm font-medium focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                            </div>
                            
                            <select name="category" onchange="this.form.submit()" class="border border-slate-200 rounded-xl px-4 py-2 text-sm font-medium min-w-[160px] bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-black uppercase tracking-wider text-xs hover:bg-indigo-700 transition-all">
                                Filter
                            </button>
                            
                            @if(request('search') || request('category'))
                                <a href="{{ route('sales-team.enhanced-dealer-support') }}" class="text-slate-400 hover:text-slate-600 text-xs font-bold px-2 py-2">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- Product Grid -->
                    <div class="p-8">
                        @if($products->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($products as $product)
                                    @php
                                        // Get B2B min/max quantities
                                        $minQty = $product->min_order_qty_b2b ?? $product->min_order_qty ?? 1;
                                        $maxQty = $product->max_order_qty_b2b;
                                        
                                        // Check if already in cart
                                        $inCart = isset($cartItems[$product->id]) ? $cartItems[$product->id]['quantity'] : 0;
                                    @endphp
                                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg transition-all flex flex-col justify-between">
                                        <div>
                                            <div class="relative bg-slate-50 h-44 overflow-hidden flex items-center justify-center">
                                                @if($product->images->first())
                                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="text-slate-400 flex flex-col items-center">
                                                        <i class="fas fa-box-open text-3xl mb-1"></i>
                                                        <span class="text-[11px] font-bold">No Image</span>
                                                    </div>
                                                @endif
                                                @if($product->sku)
                                                    <span class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-sm text-white text-[9px] font-black uppercase px-2 py-0.5 rounded">
                                                        {{ $product->sku }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="p-4">
                                                <h4 class="font-bold text-slate-900 text-sm mb-1 truncate" title="{{ $product->name }}">{{ $product->name }}</h4>
                                                <p class="text-lg font-black text-indigo-600 mb-2">₹{{ number_format($product->price, 2) }}</p>
                                                
                                                <!-- MOQ Information -->
                                                @if($minQty != 1 || $maxQty)
                                                    <div class="text-[9px] font-bold text-slate-500 mb-3 bg-slate-50 px-2 py-1 rounded inline-block">
                                                        @if($minQty && $maxQty)
                                                            Min: {{ $minQty }} | Max: {{ $maxQty }}
                                                        @elseif($minQty)
                                                            Min: {{ $minQty }}
                                                        @elseif($maxQty)
                                                            Max: {{ $maxQty }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="p-4 pt-0">
                                            <!-- Quantity Selector -->
                                            <div class="flex items-center gap-2 mb-3">
                                                <button type="button" class="decrease-qty w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-lg transition-colors" 
                                                        data-product-id="{{ $product->id }}"
                                                        data-min-qty="{{ $minQty }}"
                                                        data-max-qty="{{ $maxQty }}">-</button>
                                                
                                                <input type="number" 
                                                       id="qty-{{ $product->id }}"
                                                       class="w-16 text-center border border-slate-200 rounded-lg py-1 text-sm font-black qty-input"
                                                       value="{{ $inCart > 0 ? $inCart : $minQty }}"
                                                       min="{{ $minQty }}"
                                                       max="{{ $maxQty }}"
                                                       data-product-id="{{ $product->id }}"
                                                       data-min-qty="{{ $minQty }}"
                                                       data-max-qty="{{ $maxQty }}">
                                                
                                                <button type="button" class="increase-qty w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-lg transition-colors" 
                                                        data-product-id="{{ $product->id }}"
                                                        data-min-qty="{{ $minQty }}"
                                                        data-max-qty="{{ $maxQty }}">+</button>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="flex gap-2">
                                                @if($inCart > 0)
                                                    <button type="button" 
                                                            class="update-cart-btn flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                                                            data-product-id="{{ $product->id }}">
                                                        Update
                                                    </button>
                                                    <button type="button" 
                                                            class="remove-cart-btn w-9 h-9 bg-rose-100 hover:bg-rose-200 text-rose-600 rounded-xl flex items-center justify-center transition-all"
                                                            data-product-id="{{ $product->id }}"
                                                            title="Remove item">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            class="add-to-cart-btn w-full bg-slate-900 hover:bg-indigo-600 text-white py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm"
                                                            data-product-id="{{ $product->id }}"
                                                            data-product-name="{{ $product->name }}"
                                                            data-product-price="{{ $product->price }}"
                                                            data-min-qty="{{ $minQty }}"
                                                            data-max-qty="{{ $maxQty }}">
                                                        Add to Cart
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-8">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="bg-white p-12 text-center rounded-2xl border-2 border-dashed border-slate-200">
                                <div class="text-slate-400 mb-4">
                                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-slate-600 mb-2">No products found</h3>
                                <p class="text-slate-400 text-sm">Try adjusting your filters or search terms.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Cart Sidebar Drawer -->
<div id="floating-cart" class="fixed top-0 right-0 h-full w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50 border-l border-slate-200">
    <div class="h-full flex flex-col">
        <!-- Cart Header -->
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <div class="flex justify-between items-center mb-3">
                <div class="flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-indigo-600"></i>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Active Cart</h3>
                </div>
                <button id="close-cart" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Selected Dealer Display -->
            <div id="selected-dealer-display" class="p-3 bg-indigo-50 rounded-xl border border-indigo-200 {{ session('selected_dealer_id') ? '' : 'hidden' }}">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-indigo-600"></div>
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Order On Behalf Of:</span>
                </div>
                <div id="dealer-name-display" class="text-sm font-bold text-slate-900 truncate">
                    @if(session('selected_dealer_id'))
                        {{ $dealers->firstWhere('id', session('selected_dealer_id'))?->company_name ?? $dealers->firstWhere('id', session('selected_dealer_id'))?->name ?? 'Selected Dealer' }}
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-6" id="cart-items-container">
            @if($totalItems > 0)
                <div class="space-y-4">
                    @foreach($cartItems as $productId => $item)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100" data-product-id="{{ $productId }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 pr-2">
                                    <h4 class="font-bold text-slate-900 text-sm truncate">{{ $item['name'] }}</h4>
                                    <p class="text-indigo-600 font-black mt-1 text-xs">₹{{ number_format($item['price'], 2) }} each</p>
                                    <div class="flex items-center gap-2 mt-3">
                                        <button class="decrease-cart-qty w-6 h-6 rounded-full bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold text-sm" 
                                                data-product-id="{{ $productId }}"
                                                data-min-qty="{{ $item['min_qty'] ?? 1 }}">-</button>
                                        <span class="font-black text-slate-900 min-w-[36px] text-center text-sm">{{ $item['quantity'] }}</span>
                                        <button class="increase-cart-qty w-6 h-6 rounded-full bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold text-sm" 
                                                data-product-id="{{ $productId }}"
                                                data-max-qty="{{ $item['max_qty'] ?? null }}">+</button>
                                    </div>
                                </div>
                                <button class="remove-from-cart-btn text-rose-500 hover:text-rose-700 p-1" data-product-id="{{ $productId }}" title="Remove">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-200/60 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-500">Subtotal</span>
                                <span class="text-xs font-black text-slate-900">₹{{ number_format($item['quantity'] * $item['price'], 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-600 mb-1">Your cart is empty</h4>
                    <p class="text-slate-400 text-xs">Select products from the catalog to add items</p>
                </div>
            @endif
        </div>
        
        <!-- Cart Footer -->
        @if($totalItems > 0)
        <div class="p-6 border-t border-slate-200 bg-slate-50 space-y-4">
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold text-slate-500">
                    <span>Items ({{ $totalItems }})</span>
                    <span>₹{{ number_format($totalAmount, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-black text-slate-900 pt-2 border-t border-slate-200">
                    <span>Total Amount</span>
                    <span class="text-indigo-600">₹{{ number_format($totalAmount, 2) }}</span>
                </div>
            </div>
            <button id="proceed-to-checkout" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-black uppercase tracking-widest text-xs shadow-lg transition-all active:scale-95">
                Place Order for Dealer
            </button>
            <button id="clear-full-cart" class="w-full py-2.5 bg-white hover:bg-slate-100 text-slate-600 rounded-xl font-bold text-xs border border-slate-200 transition-all">
                Clear Cart
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Cart Toggle Button -->
<button id="toggle-cart" class="fixed bottom-8 right-8 w-16 h-16 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-2xl flex items-center justify-center z-40 transition-all transform hover:scale-110">
    <i class="fas fa-shopping-cart text-xl"></i>
    @if($totalItems > 0)
    <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[11px] font-black rounded-full w-6 h-6 flex items-center justify-center border-2 border-white shadow-sm">
        {{ $totalItems }}
    </span>
    @endif
</button>

<!-- Hidden Forms for Order Operations -->
<form id="checkout-form" action="{{ route('sales-team.checkout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" id="selected-dealer-id" name="dealer_id" value="{{ session('selected_dealer_id', '') }}">
</form>

<form id="place-order-form" action="{{ route('sales-team.place-order-cart') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" id="place-order-dealer-id" name="dealer_id" value="{{ session('selected_dealer_id', '') }}">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const toggleCartBtn = document.getElementById('toggle-cart');
    const closeCartBtn = document.getElementById('close-cart');
    const floatingCart = document.getElementById('floating-cart');
    const proceedToCheckoutBtn = document.getElementById('proceed-to-checkout');
    const clearFullCartBtn = document.getElementById('clear-full-cart');
    const clearCartTopBtn = document.getElementById('clear-cart');
    const placeOrderBtn = document.getElementById('place-order-btn');
    
    // Cart Toggle
    if (toggleCartBtn && floatingCart) {
        toggleCartBtn.addEventListener('click', function() {
            floatingCart.classList.toggle('translate-x-full');
        });
    }
    
    if (closeCartBtn && floatingCart) {
        closeCartBtn.addEventListener('click', function() {
            floatingCart.classList.add('translate-x-full');
        });
    }
    
    // Dealer selection
    document.querySelectorAll('.dealer-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const dealerId = this.getAttribute('data-dealer-id');
            const dealerName = this.getAttribute('data-dealer-name');
            
            // Update hidden form inputs
            const selectedDealerInput = document.getElementById('selected-dealer-id');
            if (selectedDealerInput) selectedDealerInput.value = dealerId;
            const placeOrderDealerInput = document.getElementById('place-order-dealer-id');
            if (placeOrderDealerInput) placeOrderDealerInput.value = dealerId;
            
            // Update UI display in cart
            const dealerDisplay = document.getElementById('selected-dealer-display');
            const dealerNameDisplay = document.getElementById('dealer-name-display');
            if (dealerDisplay && dealerNameDisplay) {
                dealerNameDisplay.textContent = dealerName;
                dealerDisplay.classList.remove('hidden');
            }
            
            // Visual feedback
            document.querySelectorAll('.dealer-item').forEach(i => {
                i.classList.remove('border-indigo-500', 'bg-indigo-50/80', 'shadow-sm');
                i.classList.add('border-slate-100');
                const dot = i.querySelector('div.rounded-full');
                if (dot) {
                    dot.classList.remove('bg-indigo-600');
                    dot.classList.add('bg-slate-200');
                }
            });
            
            this.classList.remove('border-slate-100');
            this.classList.add('border-indigo-500', 'bg-indigo-50/80', 'shadow-sm');
            const dot = this.querySelector('div.rounded-full');
            if (dot) {
                dot.classList.remove('bg-slate-200');
                dot.classList.add('bg-indigo-600');
            }
            
            // Store selected dealer in session via AJAX
            fetch('{{ route("sales-team.select-dealer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ dealer_id: dealerId })
            }).catch(err => console.error('Dealer selection error:', err));
        });
    });

    // Add to cart click
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productPrice = this.getAttribute('data-product-price');
            const minQty = this.getAttribute('data-min-qty') || 1;
            const maxQty = this.getAttribute('data-max-qty');
            
            const qtyInput = document.getElementById(`qty-${productId}`);
            let quantity = qtyInput ? parseInt(qtyInput.value) : parseInt(minQty);
            
            if (isNaN(quantity) || quantity < 1) {
                alert('Please enter a valid quantity');
                return;
            }
            
            addToCart(productId, productName, productPrice, quantity, minQty, maxQty);
        });
    });
    
    // Update cart click
    document.querySelectorAll('.update-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const qtyInput = document.getElementById(`qty-${productId}`);
            if (!qtyInput) return;
            const quantity = parseInt(qtyInput.value);
            if (isNaN(quantity) || quantity < 1) {
                alert('Please enter a valid quantity');
                return;
            }
            updateCart(productId, quantity);
        });
    });
    
    // Remove from cart buttons
    document.querySelectorAll('.remove-cart-btn, .remove-from-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                removeFromCart(productId);
            }
        });
    });

    // Quantity selectors (+ / -) in catalog
    document.querySelectorAll('.increase-qty, .decrease-qty').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const minQty = parseInt(this.getAttribute('data-min-qty')) || 1;
            const maxQty = this.getAttribute('data-max-qty') ? parseInt(this.getAttribute('data-max-qty')) : null;
            const qtyInput = document.getElementById(`qty-${productId}`);
            if (!qtyInput) return;
            
            let currentQty = parseInt(qtyInput.value) || minQty;
            if (this.classList.contains('increase-qty')) {
                if (!maxQty || currentQty < maxQty) {
                    qtyInput.value = currentQty + 1;
                }
            } else {
                if (currentQty > minQty) {
                    qtyInput.value = currentQty - 1;
                }
            }
        });
    });

    // Quantity selectors (+ / -) in floating drawer
    document.querySelectorAll('.increase-cart-qty, .decrease-cart-qty').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const minQty = parseInt(this.getAttribute('data-min-qty')) || 1;
            const maxQty = this.getAttribute('data-max-qty') ? parseInt(this.getAttribute('data-max-qty')) : null;
            
            const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
            const qtySpan = cartItem ? cartItem.querySelector('span') : null;
            if (!qtySpan) return;
            
            let currentQty = parseInt(qtySpan.textContent) || minQty;
            if (this.classList.contains('increase-cart-qty')) {
                if (!maxQty || currentQty < maxQty) {
                    updateCart(productId, currentQty + 1);
                }
            } else {
                if (currentQty > minQty) {
                    updateCart(productId, currentQty - 1);
                }
            }
        });
    });

    // Clear cart actions
    function handleClearCart() {
        if (confirm('Are you sure you want to clear your current cart?')) {
            fetch('{{ route("sales-team.clear-cart") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => location.reload())
              .catch(err => {
                  console.error(err);
                  location.reload();
              });
        }
    }
    
    if (clearCartTopBtn) clearCartTopBtn.addEventListener('click', handleClearCart);
    if (clearFullCartBtn) clearFullCartBtn.addEventListener('click', handleClearCart);

    // Place order from top banner
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', function() {
            const dealerId = document.getElementById('selected-dealer-id')?.value;
            if (!dealerId) {
                alert('Please select a dealer from the left panel first!');
                return;
            }
            document.getElementById('place-order-form').submit();
        });
    }

    // Checkout from drawer
    if (proceedToCheckoutBtn) {
        proceedToCheckoutBtn.addEventListener('click', function() {
            const dealerId = document.getElementById('selected-dealer-id')?.value;
            if (!dealerId) {
                alert('Please select a dealer from the left panel first!');
                return;
            }
            document.getElementById('checkout-form').submit();
        });
    }
});

// AJAX operations
function addToCart(productId, productName, price, quantity, minQty, maxQty) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        alert('Security token missing. Please refresh the page.');
        return;
    }
    
    fetch('{{ route("sales-team.add-to-cart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            price: parseFloat(price),
            quantity: parseInt(quantity),
            min_qty: parseInt(minQty),
            max_qty: maxQty ? parseInt(maxQty) : null
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error adding product to cart');
        }
    })
    .catch(err => {
        console.error('Add to cart error:', err);
        alert('Network error. Please try again.');
    });
}

function updateCart(productId, quantity) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch('{{ route("sales-team.update-cart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: parseInt(quantity)
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating cart');
        }
    })
    .catch(err => {
        console.error('Update cart error:', err);
        alert('Network error. Please try again.');
    });
}

function removeFromCart(productId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch('{{ route("sales-team.remove-from-cart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error removing item from cart');
        }
    })
    .catch(err => {
        console.error('Remove from cart error:', err);
        alert('Network error. Please try again.');
    });
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-6px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>
@endsection
