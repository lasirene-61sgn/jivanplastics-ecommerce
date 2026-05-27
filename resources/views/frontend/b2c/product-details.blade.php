@extends('frontend.b2c.layouts.app')

@section('title', $product->name . ' - Product Details')

@php
$customer = auth()->user()->customer ?? null;
$b2cMin = $product->min_order_qty_b2c ?? 1;
$piecesPerUnit = max(1, $product->per_quantity_pieces ?? 1);
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb / Back -->
    <div class="mb-8">
        <a href="{{ route('b2c.products') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-rose-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Catalog
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
            <!-- Product Image Section -->
            <div class="relative bg-slate-50 p-8 lg:p-12 flex items-center justify-center min-h-[400px] lg:min-h-[600px] border-b lg:border-b-0 lg:border-r border-slate-100 shadow-inner">
                @php $imagePath = $product->image_url ?? ($product->images->first() ? $product->images->first()->image_path : null); @endphp
                <img id="mainImage" src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder.jpg') }}"
                    alt="{{ $product->name }}"
                    class="max-w-full max-h-[500px] object-contain drop-shadow-2xl transition-transform hover:scale-105 duration-500">

                <!-- Badges -->
                <div class="absolute top-8 left-8 flex flex-col gap-3">
                    @if($product->gst_percentage > 0)
                    <div class="bg-rose-600 text-white text-[10px] font-black px-3 py-1.5 rounded-full uppercase tracking-widest shadow-lg">GST: {{ $product->gst_percentage }}%</div>
                    @endif
                </div>

                <!-- Thumbnails if available -->
                @if($product->images->count() > 1)
                <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-2 px-8 overflow-x-auto pb-2">
                    @foreach($product->images as $img)
                    <div class="w-16 h-16 border-2 border-white rounded-lg overflow-hidden shadow-lg cursor-pointer hover:border-rose-600 transition-all flex-shrink-0" onclick="document.getElementById('mainImage').src = this.querySelector('img').src">
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
                unitPrice: {{ $product->price }},
                gstPercentage: {{ $product->gst_percentage }},
                basePieces: {{ $product->per_quantity_pieces ?? 1 }},
                thicknessPieces: @js($product->thickness_pieces ?? []),
                selectedSize: '{{ trim($productSizes[0] ?? '') }}',
                selectedThickness: '{{ trim($productThicknesses[0] ?? '') }}',
                selectedColor: '{{ trim($productColors[0] ?? '') }}',
                get currentPieces() {
                    return parseInt(this.thicknessPieces[this.selectedThickness] || this.basePieces);
                },
                get piecePrice() { return (this.unitPrice / this.currentPieces) },
                quantity: 1,
                get subtotal() { return (this.quantity * this.piecePrice) },
                get gstAmount() { return (this.subtotal * this.gstPercentage / 100) },
                get total() { return (this.subtotal + this.gstAmount).toFixed(2) },
                get canAddToCart() {
                    if ({{ $product->size ? 'true' : 'false' }} && !this.selectedSize) return false;
                    if ({{ $product->thickness ? 'true' : 'false' }} && !this.selectedThickness) return false;
                    if ({{ $product->color ? 'true' : 'false' }} && !this.selectedColor) return false;
                    return true;
                }
            }">
                <div class="mb-auto">
                    <!-- Category & Meta -->
                    <div class="flex items-center gap-3 mb-6">
                        <nav class="flex text-[10px] font-black uppercase tracking-[0.2em]" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li><span class="text-slate-400">{{ $product->category->name ?? 'General' }}</span></li>
                                @if($product->subcategory)
                                <li><svg class="w-2.5 h-2.5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path>
                                    </svg></li>
                                <li><span class="text-rose-600">{{ $product->subcategory->name }}</span></li>
                                @endif
                            </ol>
                        </nav>
                    </div>

                    <!-- Title -->
                    <h1 class="text-4xl font-black text-slate-900 mb-8 leading-tight uppercase tracking-tight">{{ $product->name }}</h1>

                    <!-- Attributes Selection -->
                    @if($product->size || $product->thickness || $product->color)
                    <div class="space-y-6 mb-8 py-8 border-y border-slate-100">
                        @if($product->size)
                        <div class="space-y-3">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest block">Available Sizes</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productSizes as $sz)
                                @php $sz = trim($sz); @endphp
                                <button type="button" @click="selectedSize = '{{ $sz }}'"
                                    :class="selectedSize == '{{ $sz }}' ? 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-white text-slate-700 border-slate-200 hover:border-rose-600'"
                                    class="px-4 py-2 rounded-xl border text-xs font-black uppercase transition-all duration-300">
                                    {{ $sz }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($product->thickness)
                        <div class="space-y-3">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest block">Thickness</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productThicknesses as $tk)
                                @php $tk = trim($tk); @endphp
                                <button type="button" @click="selectedThickness = '{{ $tk }}'"
                                    :class="selectedThickness == '{{ $tk }}' ? 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-white text-slate-700 border-slate-200 hover:border-rose-600'"
                                    class="px-4 py-2 rounded-xl border text-xs font-black uppercase transition-all duration-300">
                                    {{ $tk }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($product->color)
                        <div class="space-y-3">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest block">Color / Finish</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productColors as $cl)
                                @php $cl = trim($cl); @endphp
                                <button type="button" @click="selectedColor = '{{ $cl }}'"
                                    :class="selectedColor == '{{ $cl }}' ? 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-white text-slate-700 border-slate-200 hover:border-rose-600'"
                                    class="px-4 py-2 rounded-xl border text-xs font-black uppercase transition-all duration-300">
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
                    <div class="mb-10">
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Description</h3>
                        <div class="text-slate-500 text-sm leading-relaxed prose prose-rose max-w-none">
                            <p>{{ $product->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Pricing & Cart Logic -->
                <div class="mt-8 bg-slate-50 rounded-[2rem] p-8 border border-slate-100 shadow-sm">
                    <!-- Price Breakdown -->
                    <div class="flex justify-between items-end mb-8 pb-8 border-b border-slate-200/60">
                        <div>
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Price Per Piece</span>
                            <span class="text-4xl font-black text-slate-900 tracking-tighter">₹<span x-text="piecePrice.toFixed(2)"></span></span>
                        </div>
                        <div class="text-right">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Total Payable</span>
                            <span class="text-4xl font-black text-rose-600 tracking-tighter">₹<span x-text="total"></span></span>
                        </div>
                    </div>

                    <!-- Quantity and Pieces -->
                    <div class="grid grid-cols-1 mb-8">
                        <div class="space-y-3">
                            <span class="block text-[10px] font-black text-slate-500 uppercase tracking-widest">Quantity (Pieces)</span>
                            <div class="flex items-center bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm h-14 w-full max-w-sm">
                                <button type="button" @click="if(quantity > 1) quantity--" class="flex-1 h-full hover:bg-slate-50 text-slate-600 font-bold text-xl transition-colors border-r border-slate-200 flex items-center justify-center">-</button>
                                <input type="number" x-model.number="quantity" min="1" class="w-24 text-center text-xl font-bold border-none focus:ring-0 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none text-slate-900">
                                <button type="button" @click="quantity++" class="flex-1 h-full hover:bg-slate-50 text-slate-600 font-bold text-xl transition-colors border-l border-slate-200 flex items-center justify-center">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Add to Cart Form -->
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="size" :value="selectedSize">
                        <input type="hidden" name="thickness" :value="selectedThickness">
                        <input type="hidden" name="color" :value="selectedColor">
                        <input type="hidden" name="is_pieces" value="1">

                        <button type="submit"
                            :disabled="!canAddToCart"
                            :class="!canAddToCart ? 'bg-slate-200 cursor-not-allowed text-slate-400' : 'bg-slate-900 text-white hover:bg-rose-600 shadow-2xl shadow-slate-200 active:scale-[0.98]'"
                            class="w-full flex items-center justify-center gap-3 py-6 rounded-2xl font-black text-sm uppercase tracking-[0.2em] transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span x-text="canAddToCart ? 'Add To Cart' : 'Please Select Options' "></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-24">
        <div class="flex items-center justify-between mb-12">
            <h2 class="text-3xl font-black text-slate-900 uppercase tracking-tight">You Might Also <span class="text-rose-600">Like</span></h2>
            <div class="w-24 h-1 bg-rose-200 rounded-full"></div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($relatedProducts as $related)
            <a href="{{ route('b2c.products.show', $related) }}" class="group block bg-white border border-slate-100 rounded-[2rem] overflow-hidden hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-500">
                <div class="aspect-square bg-slate-50 overflow-hidden relative">
                    @php $relImg = $related->image_url ?? ($related->images->first() ? $related->images->first()->image_path : null); @endphp
                    <img src="{{ $relImg ? asset('storage/' . $relImg) : asset('images/placeholder.jpg') }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/10 transition-colors"></div>
                </div>
                <div class="p-6">
                    <h3 class="font-black text-slate-900 uppercase text-sm truncate tracking-tight mb-2">{{ $related->name }}</h3>
                    <div class="flex items-center justify-between">
                        <p class="text-rose-600 font-black text-lg">₹{{ number_format($related->price, 2) }}</p>
                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection