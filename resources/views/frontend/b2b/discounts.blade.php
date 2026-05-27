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
                    $originalPrice = $product->price;
                    $discountedPrice = $product->getB2BDiscountedPrice($customer);
                    $discountAmount = $originalPrice - $discountedPrice;
                    $discountPercentage = $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100, 1) : 0;
                    $minQty = $product->min_order_qty_b2b ?? 1;
                    $piecesPerUnit = max(1, $product->per_quantity_pieces ?? 1);
                @endphp
                <div class="group bg-white border border-slate-100 rounded-3xl p-6 hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 flex flex-col"
                     x-data="{ 
                        quantity: {{ $minQty }}, 
                        unitPrice: {{ $discountedPrice }},
                        piecesPerUnit: {{ $piecesPerUnit }},
                        originalPrice: {{ $originalPrice }},
                        get pieceNetRate() { return (this.unitPrice / this.piecesPerUnit).toFixed(2) },
                        get total() { return (this.quantity * this.unitPrice).toFixed(2) },
                        get totalPieces() { return (this.quantity * this.piecesPerUnit) },
                        get totalSavings() { return (this.quantity * (this.originalPrice - this.unitPrice)).toFixed(2) },
                        get discountPerPiece() { return ((this.originalPrice - this.unitPrice) / this.piecesPerUnit).toFixed(2) }
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
                            <div class="absolute top-0 right-0 p-1">
                                <span class="bg-red-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded-bl-lg uppercase">{{ $discountPercentage }}%</span>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">{{ $product->category->name ?? 'General' }}</span>
                            <h3 class="font-black text-slate-900 truncate uppercase tracking-tighter text-lg leading-tight">{{ $product->name }}</h3>
                            
                            <div class="mt-4 space-y-2">
                                <div class="flex items-baseline gap-2">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Rate (Per PC):</span>
                                    <span class="text-xs font-black text-slate-900 tracking-tighter">₹<span x-text="pieceNetRate"></span></span>
                                </div>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Net Piece Rate (Unit):</span>
                                    <span class="text-xl font-black text-emerald-600 tracking-tight">₹{{ number_format($discountedPrice, 2) }}</span>
                                    <span class="text-xs line-through text-slate-300 font-bold">₹{{ number_format($originalPrice, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 flex-1">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="p-3 bg-slate-50 rounded-2xl flex flex-col justify-center border border-slate-100/50 group-hover:border-emerald-100 transition-colors">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Discount per piece</p>
                                <p class="text-sm font-black text-emerald-600 tracking-tight">Save ₹<span x-text="discountPerPiece"></span></p>
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
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Calculation</p>
                                    <p class="text-[10px] font-bold text-slate-600"><span x-text="quantity"></span> units x ₹{{ number_format($discountedPrice, 2) }}</p>
                                </div>
                                <div class="flex justify-between items-end mt-2 pt-2 border-t border-slate-200/30">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Cost</p>
                                        <p class="text-lg font-black text-red-600 tracking-tighter">₹<span x-text="total"></span></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Discount</p>
                                        <p class="text-xs font-black text-emerald-600 tracking-tight">₹<span x-text="totalSavings"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="quantity" :value="quantity">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-slate-900 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-emerald-600 active:scale-95 transition-all shadow-xl shadow-slate-200">
                            Add To Cart
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
@endsection