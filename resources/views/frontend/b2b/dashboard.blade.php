@extends('frontend.b2b.layouts.app')

@section('title', 'Dealer Dashboard - V4 Kitchen Partner')

@section('content')
<div class="space-y-10">
    <!-- Header Hero -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Welcome back, <span class="text-red-600">{{ $customer->name }}</span></h1>
            <p class="text-slate-500 font-medium mt-1">Here's what's happening with your dealership today.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('b2b.products') }}" class="px-6 py-3 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">Browse Catalog</a>
            <a href="{{ route('b2b.orders') }}" class="px-6 py-3 bg-white text-slate-900 border border-slate-200 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">View Orders</a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stat-card group relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Orders</p>
                <h3 class="text-4xl font-black text-slate-900 italic">{{ sprintf('%02d', $totalOrders) }}</h3>
                <div class="mt-4 flex items-center text-xs font-bold text-slate-500">
                    <span class="bg-slate-100 px-2 py-1 rounded">Lifetime Orders</span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
        </div>

        <div class="stat-card group relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Active Pipeline</p>
                <h3 class="text-4xl font-black text-amber-500 italic">{{ sprintf('%02d', $pendingOrders) }}</h3>
                <div class="mt-4 flex items-center text-xs font-bold text-amber-600">
                    <span class="bg-amber-50 px-2 py-1 rounded">Pending Processing</span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-32 h-32 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="stat-card group relative overflow-hidden text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border: none;">
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Completed</p>
                <h3 class="text-4xl font-black italic">{{ sprintf('%02d', $completedOrders) }}</h3>
                <div class="mt-4 flex items-center text-xs font-bold text-emerald-400">
                    <span class="bg-emerald-500/10 px-2 py-1 rounded">Successfully Delivered</span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Recent Arrivals Section -->
    <div>
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">New Arrivals</h2>
                <p class="text-slate-500 font-medium text-sm">Explore our latest additions to the catalog.</p>
            </div>
            <a href="{{ route('b2b.products') }}" class="text-red-600 font-bold text-sm hover:underline">View All &rarr;</a>
        </div>

        @if($recentProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($recentProducts as $product)
            <div class="group bg-white border border-slate-100 rounded-2xl overflow-hidden hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
                <div class="relative aspect-[4/5] bg-slate-50 overflow-hidden">
                    @php $imageUrl = $product->images->first() ? asset('storage/' . $product->images->first()->image_path) : asset('images/placeholder.jpg'); @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                    <!-- Discount Badge -->
                    @php $finalPrice = $product->getB2BDiscountedPrice($customer); @endphp
                    @if($finalPrice < $product->price)
                        <div class="absolute top-4 left-4 bg-emerald-500 text-white text-[10px] font-black px-2 py-1 rounded-full uppercase tracking-widest shadow-lg">Partner Deal</div>
                        @endif
                </div>

                <div class="p-5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                    <h4 class="font-bold text-slate-900 leading-tight group-hover:text-red-600 transition-colors uppercase truncate">{{ $product->name }}</h4>

                    <div class="mt-4 flex items-center justify-between">
                        <!-- <div class="flex flex-col">
                            @if($finalPrice < $product->price)
                                <span class="text-[10px] text-slate-400 line-through font-bold">₹{{ number_format($product->price, 2) }}</span>
                                <span class="text-lg font-black text-slate-900">₹{{ number_format($finalPrice, 2) }}</span>
                                @else
                                <span class="text-lg font-black text-slate-900">₹{{ number_format($product->price, 2) }}</span>
                                @endif
                        </div> -->
                        <!-- <a href="{{ route('products.show', $product) }}" class="p-2 bg-slate-50 rounded-lg text-slate-400 group-hover:bg-red-50 group-hover:text-red-600 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a> -->
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-20 text-center bg-white border border-dashed border-slate-200 rounded-3xl">
            <div class="mb-4 text-slate-200">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <p class="text-slate-400 font-medium">Our new catalog is being prepared. Check back soon!</p>
        </div>
        @endif
    </div>
</div>
@endsection