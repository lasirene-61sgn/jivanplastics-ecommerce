@extends('layouts.admin')

@section('title', 'Enhanced Dealer Support - Admin Panel')

@section('header', 'Enhanced Dealer Ordering System')

@section('content')
<div class="max-w-7xl mx-auto pb-20 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.297A8.609 8.609 0 0112 19c2.107 0 4.213.197 6.321.588L18 5.882A8.614 8.614 0 0012 6c-2.107 0-4.213.197-6.321.588L6 19.297A8.609 8.609 0 017 19c2.107 0 4.213.197 6.321.588z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Enhanced Dealer Support</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase italic">{{ $salesTeam->name }}'s Advanced Ordering Desk</p>
            </div>
        </div>
        <a href="{{ route('admin.sales-team.show', $salesTeam) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Exit Desk
        </a>
    </div>

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
                        <div class="p-8 text-center text-slate-400 italic text-sm">No dealers assigned to your desk.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Ordering Panel -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Cart Summary Banner -->
            @php
                $cartItems = session('sales_team_cart_' . $salesTeam->id, []);
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
                        <form method="GET" action="{{ route('admin.sales-team.dealer-support', $salesTeam) }}" class="flex flex-wrap gap-4 items-center">
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

<!-- Hidden Form for Placing Order -->
<form id="place-order-form" action="{{ route('admin.sales-team.place-order-cart', $salesTeam) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="dealer_id" id="selected-dealer-id" value="{{ session('selected_dealer_id', '') }}">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select dealer from list
    document.querySelectorAll('.dealer-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const dealerId = this.getAttribute('data-dealer-id');
            const dealerName = this.getAttribute('data-dealer-name');
            
            // Update hidden form field
            document.getElementById('selected-dealer-id').value = dealerId;
            
            // Visual feedback for selected item
            document.querySelectorAll('.dealer-item').forEach(i => {
                i.classList.remove('border-indigo-500', 'bg-indigo-50');
                i.querySelector('div.h-2').classList.remove('bg-indigo-500');
                i.querySelector('div.h-2').classList.add('bg-slate-200');
            });
            
            this.classList.add('border-indigo-500', 'bg-indigo-50');
            this.querySelector('div.h-2').classList.remove('bg-slate-200');
            this.querySelector('div.h-2').classList.add('bg-indigo-500');
            
            // Store in session via AJAX
            fetch('{{ route("admin.sales-team.select-dealer", $salesTeam) }}', {
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
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productPrice = this.getAttribute('data-product-price');
            const minQty = parseInt(this.getAttribute('data-min-qty'));
            const maxQty = this.getAttribute('data-max-qty') ? parseInt(this.getAttribute('data-max-qty')) : null;
            
            const qtyInput = document.getElementById(`qty-${productId}`);
            let quantity = parseInt(qtyInput.value);
            
            // Validate quantity
            if (quantity < minQty) {
                alert(`Minimum order quantity for ${productName} is ${minQty}`);
                qtyInput.value = minQty;
                return;
            }
            
            if (maxQty && quantity > maxQty) {
                alert(`Maximum order quantity for ${productName} is ${maxQty}`);
                qtyInput.value = maxQty;
                return;
            }
            
            // Add to cart via AJAX
            addToCart(productId, productName, productPrice, quantity, minQty, maxQty);
        });
    });
    
    // Update cart functionality
    document.querySelectorAll('.update-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const qtyInput = document.getElementById(`qty-${productId}`);
            const quantity = parseInt(qtyInput.value);
            
            // Update cart via AJAX
            updateCart(productId, quantity);
        });
    });
    
    // Remove from cart functionality
    document.querySelectorAll('.remove-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            removeFromCart(productId);
        });
    });
    
    // Quantity adjustment buttons
    document.querySelectorAll('.increase-qty, .decrease-qty').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const minQty = parseInt(this.getAttribute('data-min-qty'));
            const maxQty = this.getAttribute('data-max-qty') ? parseInt(this.getAttribute('data-max-qty')) : null;
            const qtyInput = document.getElementById(`qty-${productId}`);
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
    
    // Clear cart
    document.getElementById('clear-cart').addEventListener('click', function() {
        if (confirm('Are you sure you want to clear the cart?')) {
            fetch('{{ route("admin.sales-team.clear-cart", $salesTeam) }}', {
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
    fetch('{{ route("admin.sales-team.add-to-cart", $salesTeam) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: quantity,
            min_qty: minQty,
            max_qty: maxQty
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error adding to cart');
        }
    });
}

function updateCart(productId, quantity) {
    fetch('{{ route("admin.sales-team.update-cart", $salesTeam) }}', {
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating cart');
        }
    });
}

function removeFromCart(productId) {
    fetch('{{ route("admin.sales-team.remove-from-cart", $salesTeam) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error removing from cart');
        }
    });
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection