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
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase italic">Advanced Ordering Desk</p>
            </div>
        </div>
        <a href="{{ route('sales-team.dashboard') }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
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
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400 flex justify-between items-center">
                    Dealer Selection
                    <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-[9px]">{{ $dealers->count() }} total</span>
                </div>
                <div class="p-4 overflow-y-auto max-h-[600px] custom-scrollbar">
                    @if($dealers->count() > 0)
                        <div class="space-y-2">
                            @foreach($dealers as $dealer)
                                <a href="#" 
                                   class="dealer-item block p-4 rounded-2xl border border-slate-50 hover:border-indigo-200 hover:bg-indigo-50/50 transition-all group {{ session('selected_dealer_id') == $dealer->id ? 'border-indigo-500 bg-indigo-50' : '' }}"
                                   data-dealer-id="{{ $dealer->id }}"
                                   data-dealer-name="{{ $dealer->company_name ?? $dealer->name }}">
                                    <div class="flex justify-between items-start mb-1">
                                        <h6 class="text-sm font-black text-slate-900 group-hover:text-indigo-600 truncate max-w-[150px]">{{ $dealer->company_name ?? $dealer->name }}</h6>
                                        <div class="h-2 w-2 rounded-full {{ session('selected_dealer_id') == $dealer->id ? 'bg-indigo-500' : 'bg-slate-200 group-hover:bg-indigo-500' }} transition-colors"></div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-medium truncate mb-2">{{ $dealer->email }}</p>
                                    <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 bg-white px-2 py-1 rounded inline-block shadow-sm">
                                        {{ $dealer->phone ?? 'NO PHONE' }}
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
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 flex justify-between items-center">
                <div>
                    <strong class="text-blue-800">{{ $totalItems }} item(s) in cart</strong>
                    <span class="text-blue-600 ml-2">| Total: ₹{{ number_format($totalAmount, 2) }}</span>
                </div>
                <div class="flex gap-2">
                    <button id="clear-cart" class="text-xs font-black uppercase tracking-wider text-blue-600 hover:text-blue-800 px-3 py-1 bg-white rounded-lg border border-blue-200">
                        Clear Cart
                    </button>
                    <button id="place-order-btn" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-black uppercase tracking-widest shadow-lg">
                        Place Order
                    </button>
                </div>
            </div>
            @endif

            <!-- Product Selection Area -->
            <div class="bg-slate-900 rounded-[2.5rem] p-1 border-4 border-slate-800 shadow-2xl">
                <div class="bg-white rounded-[2.1rem] overflow-hidden">
                    <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Product Catalog</h3>
                        <span class="text-[10px] font-black bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full uppercase tracking-tighter">B2B-Style Selection</span>
                    </div>

                    <!-- Product Filters -->
                    <div class="p-6 bg-slate-50 border-b border-slate-100">
                        <form method="GET" action="{{ route('sales-team.dealer-support') }}" class="flex flex-wrap gap-4 items-center">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="flex-1 min-w-[200px] px-4 py-2 rounded-lg border border-slate-200 text-sm">
                            
                            <select name="category" onchange="this.form.submit()" class="border border-slate-200 rounded-lg p-2 text-sm min-w-[150px]">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold text-sm hover:bg-indigo-700">
                                Filter
                            </button>
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
                                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg transition-all">
                                        @if($product->images->first())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-slate-100 flex items-center justify-center">
                                                <span class="text-slate-400">No Image</span>
                                            </div>
                                        @endif
                                        
                                        <div class="p-4">
                                            <h4 class="font-bold text-slate-900 text-sm mb-2 truncate">{{ $product->name }}</h4>
                                            <p class="text-lg font-black text-indigo-600 mb-3">₹{{ number_format($product->price, 2) }}</p>
                                            
                                            <!-- MOQ Information -->
                                            @if($minQty != 1 || $maxQty)
                                                <div class="text-[9px] font-bold text-slate-500 mb-3 bg-slate-50 px-2 py-1 rounded">
                                                    @if($minQty && $maxQty)
                                                        Min: {{ $minQty }} | Max: {{ $maxQty }}
                                                    @elseif($minQty)
                                                        Min: {{ $minQty }}
                                                    @elseif($maxQty)
                                                        Max: {{ $maxQty }}
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <!-- Quantity Selector -->
                                            <div class="flex items-center gap-2 mb-3">
                                                <button type="button" class="decrease-qty w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-lg" 
                                                        data-product-id="{{ $product->id }}"
                                                        data-min-qty="{{ $minQty }}"
                                                        data-max-qty="{{ $maxQty }}">-</button>
                                                
                                                <input type="number" 
                                                       id="qty-{{ $product->id }}"
                                                       class="w-16 text-center border border-slate-200 rounded py-1 text-sm font-bold qty-input"
                                                       value="{{ $inCart > 0 ? $inCart : $minQty }}"
                                                       min="{{ $minQty }}"
                                                       max="{{ $maxQty }}"
                                                       data-product-id="{{ $product->id }}"
                                                       data-min-qty="{{ $minQty }}"
                                                       data-max-qty="{{ $maxQty }}"
                                                       {{ $inCart > 0 ? '' : 'disabled' }}>
                                                
                                                <button type="button" class="increase-qty w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-lg" 
                                                        data-product-id="{{ $product->id }}"
                                                        data-min-qty="{{ $minQty }}"
                                                        data-max-qty="{{ $maxQty }}">+</button>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div class="flex gap-2">
                                                @if($inCart > 0)
                                                    <button type="button" 
                                                            class="update-cart-btn flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-xs font-black uppercase tracking-widest"
                                                            data-product-id="{{ $product->id }}">
                                                        Update Cart
                                                    </button>
                                                    <button type="button" 
                                                            class="remove-cart-btn w-8 h-8 bg-rose-100 hover:bg-rose-200 text-rose-600 rounded-lg flex items-center justify-center"
                                                            data-product-id="{{ $product->id }}">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            class="add-to-cart-btn w-full bg-slate-900 hover:bg-slate-800 text-white py-2 rounded-lg text-xs font-black uppercase tracking-widest"
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
                                <p class="text-slate-400">Try adjusting your filters or search terms.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Cart Sidebar -->
<div id="floating-cart" class="fixed top-0 right-0 h-full w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50 border-l border-slate-200">
    <div class="h-full flex flex-col">
        <!-- Cart Header -->
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Your Cart</h3>
                <button id="close-cart" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-slate-500 mb-4">Review items before checkout</p>
            
            <!-- Selected Dealer Display -->
            <div id="selected-dealer-display" class="mb-4 p-3 bg-indigo-50 rounded-xl border border-indigo-200 hidden">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                    <span class="text-xs font-black text-indigo-600 uppercase tracking-widest">Selected Dealer:</span>
                </div>
                <div id="dealer-name-display" class="text-sm font-bold text-slate-900 truncate"></div>
            </div>
        </div>
        
        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-6" id="cart-items-container">
            @php
                $cartItems = session('sales_team_portal_cart', []);
                $totalItems = collect($cartItems)->sum('quantity');
                $totalAmount = collect($cartItems)->sum(function($item) {
                    return $item['quantity'] * $item['price'];
                });
            @endphp
            
            @if($totalItems > 0)
                <div class="space-y-4">
                    @foreach($cartItems as $productId => $item)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100" data-product-id="{{ $productId }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-900 text-sm">{{ $item['name'] }}</h4>
                                    <p class="text-indigo-600 font-black mt-1">₹{{ number_format($item['price'], 2) }}</p>
                                    <div class="flex items-center gap-2 mt-3">
                                        <button class="decrease-cart-qty w-6 h-6 rounded-full bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold text-sm" 
                                                data-product-id="{{ $productId }}"
                                                data-min-qty="{{ $item['min_qty'] }}">-</button>
                                        <span class="font-bold text-slate-900 min-w-[40px] text-center">{{ $item['quantity'] }}</span>
                                        <button class="increase-cart-qty w-6 h-6 rounded-full bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold text-sm" 
                                                data-product-id="{{ $productId }}"
                                                data-max-qty="{{ $item['max_qty'] }}">+</button>
                                    </div>
                                </div>
                                <button class="remove-from-cart-btn text-rose-500 hover:text-rose-700 p-2" data-product-id="{{ $productId }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="mt-3 pt-3 border-t border-slate-200">
                                <p class="text-sm font-bold text-slate-700">Total: <span class="text-indigo-600">₹{{ number_format($item['quantity'] * $item['price'], 2) }}</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-slate-300 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-bold text-slate-400 mb-2">Your cart is empty</h4>
                    <p class="text-slate-500 text-sm">Add products from the catalog to get started</p>
                </div>
            @endif
        </div>
        
        <!-- Cart Footer -->
        @if($totalItems > 0)
        <div class="p-6 border-t border-slate-200 bg-slate-50">
            <div class="space-y-4">
                <div class="flex justify-between text-sm font-bold">
                    <span class="text-slate-600">Subtotal ({{ $totalItems }} items)</span>
                    <span class="text-slate-900">₹{{ number_format($totalAmount, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-black">
                    <span class="text-slate-900">Total</span>
                    <span class="text-indigo-600">₹{{ number_format($totalAmount, 2) }}</span>
                </div>
                <button id="proceed-to-checkout" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black uppercase tracking-widest text-sm shadow-lg transition-all">
                    Proceed to Checkout
                </button>
                <button id="clear-full-cart" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl font-bold text-sm transition-all">
                    Clear Cart
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Cart Toggle Button -->
<button id="toggle-cart" class="fixed bottom-8 right-8 w-16 h-16 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-2xl flex items-center justify-center z-40 transition-all transform hover:scale-110">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
    </svg>
    @if($totalItems > 0)
    <span class="absolute -top-2 -right-2 bg-rose-500 text-white text-xs font-black rounded-full w-6 h-6 flex items-center justify-center">
        {{ $totalItems }}
    </span>
    @endif
</button>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Assigned Dealers</h5>
        </div>
        <div class="card-body">
            @if($dealers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dealers as $dealer)
                                <tr>
                                    <td>{{ $dealer->name }}</td>
                                    <td>{{ $dealer->company_name ?? 'N/A' }}</td>
                                    <td>{{ $dealer->email }}</td>
                                    <td>{{ $dealer->phone ?? 'N/A' }}</td>
                                    <td>
                                        @if($dealer->address)
                                            {{ $dealer->address }}, {{ $dealer->city }}, {{ $dealer->state }} {{ $dealer->zip }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>You don't have any assigned dealers yet.</p>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const toggleCartBtn = document.getElementById('toggle-cart');
    const closeCartBtn = document.getElementById('close-cart');
    const floatingCart = document.getElementById('floating-cart');
    const proceedToCheckoutBtn = document.getElementById('proceed-to-checkout');
    const clearFullCartBtn = document.getElementById('clear-full-cart');
    
    // Cart Toggle Functionality
    if (toggleCartBtn) {
        toggleCartBtn.addEventListener('click', function() {
            floatingCart.classList.toggle('translate-x-full');
        });
    }
    
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', function() {
            floatingCart.classList.add('translate-x-full');
        });
    }
    
    // Proceed to Checkout
    if (proceedToCheckoutBtn) {
        proceedToCheckoutBtn.addEventListener('click', function() {
            console.log('Proceed to checkout clicked');
            
            // Check if selected dealer ID element exists
            const selectedDealerInput = document.getElementById('selected-dealer-id');
            if (!selectedDealerInput) {
                console.error('Selected dealer ID input not found');
                alert('Dealer selection element is missing. Please refresh the page.');
                return;
            }
            
            const dealerId = selectedDealerInput.value;
            console.log('Selected dealer ID:', dealerId);
            
            if (!dealerId || dealerId.trim() === '') {
                alert('Please select a dealer first!');
                return;
            }
            
            // Check if checkout form exists
            const checkoutForm = document.getElementById('checkout-form');
            if (!checkoutForm) {
                console.error('Checkout form not found');
                alert('Checkout form is missing. Please refresh the page.');
                return;
            }
            
            console.log('Submitting checkout form');
            checkoutForm.submit();
        });
    }
    
    // Clear Full Cart
    if (clearFullCartBtn) {
        clearFullCartBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to clear the entire cart?')) {
                fetch('{{ route("sales-team.clear-cart") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(() => {
                    location.reload();
                });
            }
        });
    }
    
    // Select dealer from list
    document.querySelectorAll('.dealer-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const dealerId = this.getAttribute('data-dealer-id');
            const dealerName = this.getAttribute('data-dealer-name');
            
            console.log('Dealer selected:', { dealerId, dealerName });
            
            // Update hidden form field
            const selectedDealerInput = document.getElementById('selected-dealer-id');
            if (selectedDealerInput) {
                selectedDealerInput.value = dealerId;
            }
            
            // Update dealer display in cart
            const dealerDisplay = document.getElementById('selected-dealer-display');
            const dealerNameDisplay = document.getElementById('dealer-name-display');
            if (dealerDisplay && dealerNameDisplay) {
                dealerNameDisplay.textContent = dealerName;
                dealerDisplay.classList.remove('hidden');
            }
            
            // Visual feedback for selected item
            document.querySelectorAll('.dealer-item').forEach(i => {
                i.classList.remove('border-indigo-500', 'bg-indigo-50');
                const dot = i.querySelector('div.h-2');
                if (dot) {
                    dot.classList.remove('bg-indigo-500');
                    dot.classList.add('bg-slate-200');
                }
            });
            
            this.classList.add('border-indigo-500', 'bg-indigo-50');
            const dot = this.querySelector('div.h-2');
            if (dot) {
                dot.classList.remove('bg-slate-200');
                dot.classList.add('bg-indigo-500');
            }
            
            // Store in session via AJAX
            fetch('{{ route("sales-team.select-dealer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ dealer_id: dealerId })
            });
        });
    });
    
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Add to cart button clicked');
            console.log('Button element:', this);
                
            // Defensive checks
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productPrice = this.getAttribute('data-product-price');
            const minQty = this.getAttribute('data-min-qty');
            const maxQty = this.getAttribute('data-max-qty');
                
            console.log('Raw attributes:', { productId, productName, productPrice, minQty, maxQty });
                
            // Validate required attributes
            if (!productId) {
                console.error('Missing data-product-id attribute');
                alert('Product ID is missing. Please refresh the page.');
                return;
            }
            if (!productName) {
                console.error('Missing data-product-name attribute');
                alert('Product name is missing. Please refresh the page.');
                return;
            }
            if (!productPrice) {
                console.error('Missing data-product-price attribute');
                alert('Product price is missing. Please refresh the page.');
                return;
            }
            if (!minQty) {
                console.error('Missing data-min-qty attribute');
                alert('Minimum quantity is missing. Please refresh the page.');
                return;
            }
                  
            const qtyInput = document.getElementById(`qty-${productId}`);
            console.log('Quantity input element:', qtyInput);
                
            if (!qtyInput) {
                console.error(`Quantity input not found for product ${productId}`);
                alert('Unable to find quantity input. Please refresh the page.');
                return;
            }
                
            let quantity = parseInt(qtyInput.value);
            const minQtyNum = parseInt(minQty);
            const maxQtyNum = maxQty ? parseInt(maxQty) : null;
                
            console.log('Parsed values:', { quantity, minQtyNum, maxQtyNum });
                
            // Validate quantity
            if (isNaN(quantity) || quantity < 1) {
                alert('Please enter a valid quantity');
                return;
            }
                
            if (quantity < minQtyNum) {
                alert(`Minimum order quantity for ${productName} is ${minQtyNum}`);
                qtyInput.value = minQtyNum;
                return;
            }
                
            if (maxQtyNum && quantity > maxQtyNum) {
                alert(`Maximum order quantity for ${productName} is ${maxQtyNum}`);
                qtyInput.value = maxQtyNum;
                return;
            }
                
            // Add to cart via AJAX
            console.log('Calling addToCart with validated data');
            addToCart(productId, productName, productPrice, quantity, minQtyNum, maxQtyNum);
        });
    });
    
    // Update cart functionality
    document.querySelectorAll('.update-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            
            // Validate required attributes
            if (!productId) {
                console.error('Missing product ID on update-cart button');
                alert('Product data is incomplete. Please refresh the page.');
                return;
            }
            
            const qtyInput = document.getElementById(`qty-${productId}`);
            if (!qtyInput) {
                console.error(`Quantity input not found for product ${productId}`);
                alert('Unable to find quantity input. Please refresh the page.');
                return;
            }
            
            const quantity = parseInt(qtyInput.value);
            if (isNaN(quantity) || quantity < 1) {
                alert('Please enter a valid quantity');
                return;
            }
            
            // Update cart via AJAX
            updateCart(productId, quantity);
        });
    });
    
    // Remove from cart functionality
    document.querySelectorAll('.remove-cart-btn, .remove-from-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            
            // Validate required attributes
            if (!productId) {
                console.error('Missing product ID on remove button');
                alert('Product data is incomplete. Please refresh the page.');
                return;
            }
            
            if (confirm('Are you sure you want to remove this item from cart?')) {
                removeFromCart(productId);
            }
        });
    });
    
    // Quantity adjustment buttons (main product grid)
    document.querySelectorAll('.increase-qty, .decrease-qty').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const minQty = parseInt(this.getAttribute('data-min-qty'));
            const maxQty = this.getAttribute('data-max-qty') ? parseInt(this.getAttribute('data-max-qty')) : null;
            const qtyInput = document.getElementById(`qty-${productId}`);
            
            if (!qtyInput) return;
            
            let currentQty = parseInt(qtyInput.value);
            
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
    
    // Quantity adjustment buttons (floating cart)
    document.querySelectorAll('.increase-cart-qty, .decrease-cart-qty').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const minQty = this.getAttribute('data-min-qty') ? parseInt(this.getAttribute('data-min-qty')) : 1;
            const maxQty = this.getAttribute('data-max-qty') ? parseInt(this.getAttribute('data-max-qty')) : null;
            
            const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
            const qtySpan = cartItem ? cartItem.querySelector('span.font-bold.text-slate-900') : null;
            
            if (!qtySpan) return;
            
            let currentQty = parseInt(qtySpan.textContent);
            
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
    
    // Quantity input validation
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const minQty = parseInt(this.getAttribute('data-min-qty'));
            const maxQty = this.getAttribute('data-max-qty') ? parseInt(this.getAttribute('data-max-qty')) : null;
            let value = parseInt(this.value);
            
            if (value < minQty) {
                this.value = minQty;
            }
            
            if (maxQty && value > maxQty) {
                this.value = maxQty;
            }
        });
    });
    
    // Clear cart (top banner)
    document.getElementById('clear-cart').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear the cart?')) {
            fetch('{{ route("sales-team.clear-cart") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(() => {
                location.reload();
            });
        }
    });
    
    // Place order
    document.getElementById('place-order-btn').addEventListener('click', function() {
        const dealerId = document.getElementById('selected-dealer-id').value;
        if (!dealerId) {
            alert('Please select a dealer first!');
            return;
        }
        
        const cartItems = {!! json_encode($cartItems) !!};
        if (Object.keys(cartItems).length === 0) {
            alert('Please add products to cart first!');
            return;
        }
        
        document.getElementById('place-order-form').submit();
    });
});

// AJAX Functions
function addToCart(productId, productName, price, quantity, minQty, maxQty) {
    console.log('addToCart called with:', { productId, productName, price, quantity, minQty, maxQty });
    
    // Comprehensive defensive checks
    if (!productId) {
        console.error('addToCart: productId is null/undefined');
        alert('Product ID is missing');
        return;
    }
    if (!productName) {
        console.error('addToCart: productName is null/undefined');
        alert('Product name is missing');
        return;
    }
    if (!price) {
        console.error('addToCart: price is null/undefined');
        alert('Product price is missing');
        return;
    }
    if (!quantity || isNaN(quantity) || quantity < 1) {
        console.error('addToCart: invalid quantity', quantity);
        alert('Please enter a valid quantity');
        return;
    }
    if (!minQty || isNaN(minQty) || minQty < 1) {
        console.error('addToCart: invalid minQty', minQty);
        alert('Invalid minimum quantity');
        return;
    }
    
    // Check CSRF token exists
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('addToCart: CSRF token not found');
        alert('Security token missing. Please refresh the page.');
        return;
    }
    
    const csrfTokenValue = csrfToken.getAttribute('content');
    if (!csrfTokenValue) {
        console.error('addToCart: CSRF token content is empty');
        alert('Security token is invalid. Please refresh the page.');
        return;
    }
    
    console.log('Making AJAX request...');
    
    fetch('{{ route("sales-team.add-to-cart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfTokenValue
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
    .then(response => {
        console.log('Response received:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            console.log('Success! Reloading page...');
            location.reload();
        } else {
            alert(data.message || 'Error adding to cart');
        }
    })
    .catch(error => {
        console.error('Add to cart error:', error);
        alert('Network error occurred. Please try again.\n\nError details: ' + error.message);
    });
}

function updateCart(productId, quantity) {
    // Defensive checks
    if (!productId || !quantity) {
        console.error('Missing required parameters for updateCart');
        alert('Invalid update data');
        return;
    }
    
    fetch('{{ route("sales-team.update-cart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating cart');
        }
    })
    .catch(error => {
        console.error('Update cart error:', error);
        alert('Network error occurred. Please try again.');
    });
}

function removeFromCart(productId) {
    // Defensive checks
    if (!productId) {
        console.error('Missing product ID for removeFromCart');
        alert('Invalid product data');
        return;
    }
    
    fetch('{{ route("sales-team.remove-from-cart") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error removing from cart');
        }
    })
    .catch(error => {
        console.error('Remove from cart error:', error);
        alert('Network error occurred. Please try again.');
    });
}
</script>

<!-- Hidden Forms for AJAX Operations -->
<form id="checkout-form" action="{{ route('sales-team.checkout') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" id="selected-dealer-id" name="dealer_id" value="{{ session('selected_dealer_id', '') }}">
</form>

<form id="place-order-form" action="{{ route('sales-team.dealer-support.place-order') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="dealer_id" value="{{ session('selected_dealer_id', '') }}">
</form>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out forwards;
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #4f46e5;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endsection