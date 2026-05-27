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

                    @if($discountedPrice < $product->price)
                        <div class="bg-emerald-500 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg">Partner Deal</div>
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
            $productSizes = $product->size ? explode(',', $product->size) : [];
            $productThicknesses = $product->thickness ? explode(',', $product->thickness) : [];
            $productColors = $product->color ? explode(',', $product->color) : [];
            @endphp
            <div class="p-8 lg:p-12 flex flex-col h-full bg-white" x-data="{ 
                quantity: {{ $b2bMin }}, 
                unitPrice: {{ $discountedPrice }},
                basePieces: {{ $product->per_quantity_pieces ?? 1 }},
                thicknessPieces: @js($product->thickness_pieces ?? []),
                originalUnitPrice: {{ $product->price }},
                selectedSize: '{{ trim($productSizes[0] ?? '') }}',
                selectedThickness: '{{ trim($productThicknesses[0] ?? '') }}',
                selectedColor: '{{ trim($productColors[0] ?? '') }}',
                get currentPieces() {
                    return parseInt(this.thicknessPieces[this.selectedThickness] || this.basePieces);
                },
                get pieceNetRate() { return (this.unitPrice / this.currentPieces).toFixed(2) },
                get total() { return (this.quantity * this.unitPrice).toFixed(2) },
                get totalPieces() { return (this.quantity * this.currentPieces) },
                get totalSavings() { return (this.quantity * (this.originalUnitPrice - this.unitPrice)).toFixed(2) },
                get discountPerPiece() { return ((this.originalUnitPrice - this.unitPrice) / this.currentPieces).toFixed(2) },
                get canAddToCart() {
                    if ({{ $product->size ? 'true' : 'false' }} && !this.selectedSize) return false;
                    if ({{ $product->thickness ? 'true' : 'false' }} && !this.selectedThickness) return false;
                    if ({{ $product->color ? 'true' : 'false' }} && !this.selectedColor) return false;
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
                    <div class="mb-8 prose prose-slate prose-sm text-slate-500">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-2">Description</h3>
                        <p>{{ $product->description }}</p>
                    </div>
                    @endif
                </div>

                <!-- Pricing & Cart Logic -->

                <div class="mt-8 bg-slate-50 rounded-2xl p-6 lg:p-8 border border-slate-100">
                    <!-- Price Breakdown -->
                    <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-slate-200/50">
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Net Rate (Per PC)</span>
                            <span class="text-2xl font-black text-slate-900 tracking-tighter">₹<span x-text="pieceNetRate"></span></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Unit Rate</span>
                            <div class="flex flex-col items-end">
                                <span class="text-2xl font-black text-red-600 tracking-tighter">₹{{ number_format($discountedPrice, 2) }}</span>
                                @if($discountedPrice < $product->price)
                                    <span class="text-xs line-through text-slate-400 font-bold">₹{{ number_format($product->price, 2) }}</span>
                                    @endif
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
                        <template x-if="totalSavings > 0">
                            <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">Your Savings</span>
                                <span class="text-sm font-black text-emerald-600">₹<span x-text="totalSavings"></span></span>
                            </div>
                        </template>
                    </div>

                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="size" :value="selectedSize">
                        <input type="hidden" name="thickness" :value="selectedThickness">
                        <input type="hidden" name="color" :value="selectedColor">

                        <button type="submit"
                            :disabled="!canAddToCart"
                            :class="!canAddToCart ? 'bg-slate-200 cursor-not-allowed text-slate-400' : 'bg-slate-900 text-white hover:bg-red-600 shadow-xl shadow-slate-200 hover:shadow-red-200'"
                            class="w-full flex items-center justify-center gap-3 py-5 rounded-xl font-black text-sm uppercase tracking-widest active:scale-95 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            <span x-text="canAddToCart ? 'Add To Cart' : 'Please Select Options' "></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection