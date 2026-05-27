@extends('frontend.b2c.layouts.app')

@section('title', 'Products - V4 Kitchen Partner')

@section('content')
<div class="space-y-8" x-data="{ mobileFilters: false }">
    <!-- Page Header & Active Filters -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase">Product <span class="text-rose-600">Catalog</span></h1>
            <p class="text-slate-500 font-medium mt-1">Discover our premium range of kitchen solutions.</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Mobile Filter Toggle -->
            <button @click="mobileFilters = true" class="lg:hidden flex items-center gap-2 px-4 py-3 bg-white border border-slate-200 rounded-xl font-bold text-sm text-slate-600 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 4.5h18m-18 7.5h18m-18 7.5h18"></path>
                </svg>
                Filters
            </button>

            <form method="GET" action="{{ route('b2c.products') }}" class="flex-1 md:w-80 flex gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search products..."
                        class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 transition-all">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <!-- Preserve other filters when searching -->
                @foreach(request()->except(['search', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all">Search</button>
            </form>
        </div>
    </div>

    <!-- Main Layout Container -->
    <div class="flex flex-col lg:flex-row gap-8 items-start">

        <!-- Sidebar Filters (Desktop) -->
        <aside class="hidden lg:block w-72 flex-shrink-0 sticky top-28 space-y-8">
            <!-- Category Tree -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Categories</h3>
                <div class="space-y-4">
                    @foreach($categories as $category)
                    <div class="space-y-2">
                        <a href="{{ request()->fullUrlWithQuery(['category' => $category->id, 'subcategory' => null, 'sub_subcategory' => null]) }}"
                            class="flex items-center justify-between text-sm font-bold {{ request('category') == $category->id ? 'text-rose-600' : 'text-slate-700 hover:text-rose-600' }} transition-colors">
                            <span>{{ $category->name }}</span>
                            @if(request('category') == $category->id)
                            <div class="w-1.5 h-1.5 rounded-full bg-rose-600"></div>
                            @endif
                        </a>

                        @if(request('category') == $category->id && $category->subcategories->count() > 0)
                        <div class="ml-4 pl-4 border-l-2 border-slate-100 space-y-2 py-1">
                            @foreach($category->subcategories as $sub)
                            <div class="space-y-2">
                                <a href="{{ request()->fullUrlWithQuery(['subcategory' => $sub->id, 'sub_subcategory' => null]) }}"
                                    class="block text-[13px] font-semibold {{ request('subcategory') == $sub->id ? 'text-rose-600' : 'text-slate-500 hover:text-rose-600' }} transition-colors">
                                    {{ $sub->name }}
                                </a>

                                @if(request('subcategory') == $sub->id && $sub->subSubcategories->count() > 0)
                                <div class="ml-2 pl-4 border-l border-slate-100 space-y-2 py-1">
                                    @foreach($sub->subSubcategories as $deep)
                                    <a href="{{ request()->fullUrlWithQuery(['sub_subcategory' => $deep->id]) }}"
                                        class="block text-[12px] font-medium {{ request('sub_subcategory') == $deep->id ? 'text-rose-600 font-bold' : 'text-slate-400 hover:text-rose-600' }} transition-colors">
                                        {{ $deep->name }}
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Attribute Filters -->
            <div class="space-y-6">
                <!-- Size Filter -->
                <!--@if($availableSizes->count() > 0)-->
                <!--<div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">-->
                <!--    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Size</h3>-->
                <!--    <div class="flex flex-wrap gap-2">-->
                <!--        @foreach($availableSizes as $size)-->
                <!--        <a href="{{ request('size') == $size ? request()->fullUrlWithQuery(['size' => null]) : request()->fullUrlWithQuery(['size' => $size]) }}"-->
                <!--            class="px-3 py-1.5 border rounded-lg text-[11px] font-black uppercase tracking-tight transition-all {{ request('size') == $size ? 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-white border-slate-200 text-slate-600 hover:border-rose-600 hover:text-rose-600' }}">-->
                <!--            {{ $size }}-->
                <!--        </a>-->
                <!--        @endforeach-->
                <!--    </div>-->
                <!--</div>-->
                <!--@endif-->

                <!-- Thickness Filter -->
                <!--@if($availableThicknesses->count() > 0)-->
                <!--<div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">-->
                <!--    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Thickness</h3>-->
                <!--    <div class="flex flex-wrap gap-2">-->
                <!--        @foreach($availableThicknesses as $thick)-->
                <!--        <a href="{{ request('thickness') == $thick ? request()->fullUrlWithQuery(['thickness' => null]) : request()->fullUrlWithQuery(['thickness' => $thick]) }}"-->
                <!--            class="px-3 py-1.5 border rounded-lg text-[11px] font-black uppercase tracking-tight transition-all {{ request('thickness') == $thick ? 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-200' : 'bg-white border-slate-200 text-slate-600 hover:border-rose-600 hover:text-rose-600' }}">-->
                <!--            {{ $thick }}-->
                <!--        </a>-->
                <!--        @endforeach-->
                <!--    </div>-->
                <!--</div>-->
                <!--@endif-->

                <!-- Color Filter -->
                <!--@if($availableColors->count() > 0)-->
                <!--<div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">-->
                <!--    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Color/Finish</h3>-->
                <!--    <div class="space-y-2">-->
                <!--        @foreach($availableColors as $color)-->
                <!--        <a href="{{ request('color') == $color ? request()->fullUrlWithQuery(['color' => null]) : request()->fullUrlWithQuery(['color' => $color]) }}"-->
                <!--            class="flex items-center gap-3 group">-->
                <!--            <div class="w-4 h-4 rounded border-2 flex items-center justify-center transition-all {{ request('color') == $color ? 'bg-rose-600 border-rose-600 shadow-sm' : 'border-slate-200 group-hover:border-rose-600' }}">-->
                <!--                @if(request('color') == $color)-->
                <!--                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                <!--                    <path d="M5 13l4 4L19 7"></path>-->
                <!--                </svg>-->
                <!--                @endif-->
                <!--            </div>-->
                <!--            <span class="text-sm font-bold {{ request('color') == $color ? 'text-slate-900' : 'text-slate-500 group-hover:text-rose-600' }} transition-colors uppercase tracking-tight">{{ $color }}</span>-->
                <!--        </a>-->
                <!--        @endforeach-->
                <!--    </div>-->
                <!--</div>-->
                <!--@endif-->
            </div>
        </aside>

        <!-- Product/Category Grid Content -->
        <div class="flex-1 space-y-8">
            <!-- Active Filter Breadcrumbs -->
            @if(request('category') || request('subcategory') || request('sub_subcategory') || request('search') || request('size') || request('thickness') || request('color'))
            <div class="flex items-center gap-2 flex-wrap bg-white border border-slate-100 p-4 rounded-2xl shadow-sm">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2">Location:</span>

                <a href="{{ route('b2c.products') }}" class="inline-flex items-center gap-1 px-3 py-1 bg-slate-100 rounded-full text-[11px] font-bold text-slate-600 hover:bg-slate-200 transition-colors">Catalog</a>

                @php
                $cat = \App\Models\Category::find(request('category'));
                $sub = \App\Models\Subcategory::find(request('subcategory'));
                $deep = \App\Models\SubSubcategory::find(request('sub_subcategory'));
                @endphp

                @if($cat)
                <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-50 rounded-full text-[11px] font-bold text-rose-700">
                    {{ $cat->name }}
                    <a href="{{ request()->fullUrlWithQuery(['category' => null, 'subcategory' => null, 'sub_subcategory' => null]) }}" class="hover:text-rose-900 ml-1 font-black">&times;</a>
                </span>
                @endif
                @if($sub)
                <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-50 rounded-full text-[11px] font-bold text-rose-700">
                    {{ $sub->name }}
                    <a href="{{ request()->fullUrlWithQuery(['subcategory' => null, 'sub_subcategory' => null]) }}" class="hover:text-rose-900 ml-1 font-black">&times;</a>
                </span>
                @endif
                @if($deep)
                <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-rose-50 rounded-full text-[11px] font-bold text-rose-700">
                    {{ $deep->name }}
                    <a href="{{ request()->fullUrlWithQuery(['sub_subcategory' => null]) }}" class="hover:text-rose-900 ml-1 font-black">&times;</a>
                </span>
                @endif

                <!-- Other Filters -->
                @if(request('search') || request('size') || request('thickness') || request('color'))
                <div class="h-4 w-px bg-slate-200 mx-2"></div>
                @if(request('search'))
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-slate-900 text-white rounded-full text-[11px] font-bold">Search: "{{ request('search') }}" <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="hover:text-rose-300 ml-1 font-black">&times;</a></span>
                @endif
                @if(request('size'))
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 rounded-full text-[11px] font-bold text-indigo-700">Size: {{ request('size') }} <a href="{{ request()->fullUrlWithQuery(['size' => null]) }}" class="hover:text-indigo-900 ml-1 font-black">&times;</a></span>
                @endif
                @if(request('thickness'))
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 rounded-full text-[11px] font-bold text-indigo-700">Thk: {{ request('thickness') }} <a href="{{ request()->fullUrlWithQuery(['thickness' => null]) }}" class="hover:text-indigo-900 ml-1 font-black">&times;</a></span>
                @endif
                @if(request('color'))
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 rounded-full text-[11px] font-bold text-indigo-700">Color: {{ request('color') }} <a href="{{ request()->fullUrlWithQuery(['color' => null]) }}" class="hover:text-indigo-900 ml-1 font-black">&times;</a></span>
                @endif
                <a href="{{ route('b2c.products') }}" class="text-[10px] font-bold text-slate-400 hover:text-rose-600 underline ml-2">Clear All</a>
                @endif
            </div>
            @endif

            @if($displayMode !== 'products')
            <!-- Category Drill-down Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($childCategories as $child)
                @php
                $queryKey = match($displayMode) {
                'categories' => 'category',
                'subcategories' => 'subcategory',
                'sub_subcategories' => 'sub_subcategory',
                default => 'category'
                };
                $link = request()->fullUrlWithQuery([$queryKey => $child->id]);
                @endphp
                <a href="{{ $link }}" class="group block relative bg-white border border-slate-100 rounded-3xl overflow-hidden hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-500">
                    <!-- Image -->
                    <div class="aspect-square bg-slate-50 relative overflow-hidden">
                        <img src="{{ $child->image ? asset('storage/' . $child->image) : asset('images/placeholder-category.jpg') }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                    </div>
                    <!-- Title -->
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <h3 class="text-white font-black text-lg leading-tight uppercase tracking-tight">{{ $child->name }}</h3>
                        <p class="text-white/80 text-[10px] font-bold uppercase tracking-widest mt-1 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                            Explore More
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M9 5l7 7-7 7"></path>
                            </svg>
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <!-- Product Grid -->
            @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($products as $product)
                @php
                $gstAmount = $product->price * $product->gst_percentage / 100;
                $priceWithGst = $product->price + $gstAmount;
                @endphp
                <div class="group bg-white border border-slate-100 rounded-3xl overflow-hidden hover:shadow-2xl hover:shadow-slate-200/60 transition-all duration-500">
                    <!-- Image Container -->
                    <a href="{{ route('b2c.products.show', $product) }}" class="block relative aspect-[4/5] bg-slate-50 overflow-hidden">
                        @php $imagePath = $product->image_url ?? ($product->images->first() ? $product->images->first()->image_path : null); @endphp
                        <img src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder.jpg') }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                            loading="lazy">

                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                        </div>
                    </a>

                    <!-- Details -->
                    @php
                    $b2cMin = $product->min_order_qty_b2c ?? 1;
                    $productSizes = $product->size ? explode(',', $product->size) : [];
                    $productThicknesses = $product->thickness ? explode(',', $product->thickness) : [];
                    $productColors = $product->color ? explode(',', $product->color) : [];
                    @endphp
                    <div class="p-6" x-data="{ 
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
                        <div class="flex flex-col mb-4">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ $product->category->name ?? 'General' }}</span>
                            <a href="{{ route('b2c.products.show', $product) }}" class="block">
                                <h3 class="font-black text-slate-900 leading-tight group-hover:text-rose-600 transition-colors uppercase truncate tracking-tight">{{ $product->name }}</h3>
                            </a>

                            @if($product->size || $product->thickness || $product->color)
                            <div class="flex flex-col gap-2 mt-2">
                                @if($product->size)
                                <div class="flex flex-wrap gap-1">
                                    <span class="text-[12px] font-black text-slate-400 uppercase tracking-widest w-full mb-0.5">Size</span>
                                    @foreach($productSizes as $sz)
                                    @php $sz = trim($sz); @endphp
                                    <button type="button" @click="selectedSize = '{{ $sz }}'"
                                        :class="selectedSize == '{{ $sz }}' ? 'bg-rose-600 border-rose-600 text-white' : 'bg-slate-50 text-slate-500 border-slate-100 hover:border-rose-600'"
                                        class="text-[11px] font-black px-1.5 py-0.5 rounded border uppercase tracking-tighter transition-all">
                                        {{ $sz }}
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                                @if($product->thickness)
                                <div class="flex flex-wrap gap-1">
                                    <span class="text-[12px] font-black text-slate-400 uppercase tracking-widest w-full mb-0.5">Thk</span>
                                    @foreach($productThicknesses as $tk)
                                    @php $tk = trim($tk); @endphp
                                    <button type="button" @click="selectedThickness = '{{ $tk }}'"
                                        :class="selectedThickness == '{{ $tk }}' ? 'bg-rose-600 border-rose-600 text-white' : 'bg-slate-50 text-slate-500 border-slate-100 hover:border-rose-600'"
                                        class="text-[11px] font-black px-1.5 py-0.5 rounded border uppercase tracking-tighter transition-all">
                                        {{ $tk }}
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                                @if($product->color)
                                <div class="flex flex-wrap gap-1">
                                    <span class="text-[12px] font-black text-slate-400 uppercase tracking-widest w-full mb-0.5">Color</span>
                                    @foreach($productColors as $cl)
                                    @php $cl = trim($cl); @endphp
                                    <button type="button" @click="selectedColor = '{{ $cl }}'"
                                        :class="selectedColor == '{{ $cl }}' ? 'bg-rose-600 border-rose-600 text-white' : 'bg-slate-50 text-slate-500 border-slate-100 hover:border-rose-600'"
                                        class="text-[11px] font-black px-1.5 py-0.5 rounded border uppercase tracking-tighter transition-all">
                                        {{ $cl }}
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endif

                            <div class="mt-4">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 block">Price Per Piece</span>
                                <span class="text-2xl font-black text-rose-600 tracking-tighter">₹<span x-text="piecePrice.toFixed(2)"></span></span>
                                @if($product->gst_percentage > 0)
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">+ {{ number_format($product->gst_percentage, 0) }}% GST</p>
                                @endif
                            </div>
                        </div>

                        <!-- Quantity and Total -->
                        <div class="bg-slate-50 rounded-2xl p-4 mb-4 border border-slate-100">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Quantity (Pieces)</span>
                                <div class="flex items-center bg-white border border-slate-200 rounded-lg overflow-hidden h-8">
                                    <button type="button" @click="if(quantity > 1) quantity--" class="px-2 hover:bg-slate-50 text-slate-600 font-bold transition-colors border-r border-slate-200">-</button>
                                    <input type="number" x-model.number="quantity" min="1" class="w-12 text-center text-xs font-bold border-none focus:ring-0 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                    <button type="button" @click="quantity++" class="px-2 hover:bg-slate-50 text-slate-600 font-bold transition-colors border-l border-slate-200">+</button>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-slate-200/50 space-y-2">
                                <div class="flex justify-between items-end mt-2 pt-2 border-t border-slate-200/30">
                                    <div>
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Payable</p>
                                        <p class="text-xl font-black text-rose-600 tracking-tighter">₹<span x-text="total"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" :value="quantity">
                            <input type="hidden" name="size" :value="selectedSize">
                            <input type="hidden" name="thickness" :value="selectedThickness">
                            <input type="hidden" name="color" :value="selectedColor">
                            <input type="hidden" name="is_pieces" value="1">

                            <button type="submit"
                                :disabled="!canAddToCart"
                                :class="!canAddToCart ? 'bg-slate-200 cursor-not-allowed text-slate-400' : 'bg-slate-900 text-white hover:bg-rose-600 shadow-xl shadow-slate-200'"
                                class="w-full flex items-center justify-center gap-2 py-4 rounded-xl font-black text-xs uppercase tracking-widest active:scale-95 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span x-text="canAddToCart ? 'Add To Cart' : 'Select Options' "></span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-16 py-8 border-t border-slate-100">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @else
            <div class="py-32 text-center bg-white border border-slate-100 rounded-[3rem] shadow-sm">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">No Products Found</h3>
                <p class="text-slate-500 font-medium mt-2 mb-8 max-w-xs mx-auto text-sm leading-relaxed">Try adjusting your filters or clear them to see all available inventory.</p>
                <a href="{{ route('b2c.products') }}" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-rose-600 transition-all shadow-xl">Clear All Filters</a>
            </div>
            @endif
            @endif
        </div>
    </div>

    <!-- Mobile Filters Slide-over (Uses Alpine.js) -->
    <div x-show="mobileFilters"
        class="fixed inset-0 z-[1000] lg:hidden"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileFilters = false"></div>

        <div class="absolute right-0 top-0 bottom-0 w-[85%] max-w-sm bg-white shadow-2xl overflow-y-auto"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full">

            <div class="p-6 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white z-10">
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">Filter Products</h2>
                <button @click="mobileFilters = false" class="p-2 text-slate-400 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-8 pb-32">
                <!-- Mobile Category Tree -->
                <div class="space-y-4">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Categories</h3>
                    @foreach($categories as $category)
                    <div class="space-y-2">
                        <a href="{{ request()->fullUrlWithQuery(['category' => $category->id]) }}" class="text-sm font-bold {{ request('category') == $category->id ? 'text-rose-600' : 'text-slate-700' }}">{{ $category->name }}</a>
                        @if(request('category') == $category->id)
                        @foreach($category->subcategories as $sub)
                        <a href="{{ request()->fullUrlWithQuery(['subcategory' => $sub->id]) }}" class="block ml-4 text-[13px] font-semibold {{ request('subcategory') == $sub->id ? 'text-rose-600' : 'text-slate-500' }}">{{ $sub->name }}</a>
                        @endforeach
                        @endif
                    </div>
                    @endforeach
                </div>

                <!-- Attributes -->
                <div class="space-y-8">
                    @if($availableSizes->count() > 0)
                    <div>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Size</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableSizes as $size)
                            <a href="{{ request()->fullUrlWithQuery(['size' => $size]) }}" class="px-3 py-1.5 border rounded-lg text-[10px] font-black uppercase {{ request('size') == $size ? 'bg-rose-600 text-white border-rose-600' : 'border-slate-200 text-slate-600' }}">{{ $size }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($availableColors->count() > 0)
                    <div>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Color</h3>
                        <div class="space-y-2">
                            @foreach($availableColors as $color)
                            <a href="{{ request()->fullUrlWithQuery(['color' => $color]) }}" class="flex items-center gap-3">
                                <div class="w-4 h-4 rounded border {{ request('color') == $color ? 'bg-rose-600 border-rose-600' : 'border-slate-200' }}"></div>
                                <span class="text-sm font-bold uppercase tracking-tight {{ request('color') == $color ? 'text-slate-900' : 'text-slate-500' }}">{{ $color }}</span>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 p-6 bg-white border-t border-slate-100 flex gap-4">
                <a href="{{ route('b2c.products') }}" class="flex-1 text-center py-4 bg-slate-100 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest">Reset</a>
                <button @click="mobileFilters = false" class="flex-1 py-4 bg-slate-900 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-slate-200">Show Results</button>
            </div>
        </div>
    </div>
</div>
@endsection