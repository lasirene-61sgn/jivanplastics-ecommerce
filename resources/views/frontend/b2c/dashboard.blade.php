@extends('frontend.b2c.layouts.app')

@section('title', 'Dashboard - V4 Kitchen Partner')

@section('content')
<div class="space-y-8">

    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 p-8 text-white">
        <div class="absolute inset-0 opacity-10" style="background: radial-gradient(circle at 80% 20%, #E31E24 0%, transparent 60%);"></div>
        <div class="relative z-10">
            <p class="text-rose-400 text-xs font-bold uppercase tracking-widest mb-2">Welcome back</p>
            <h1 class="text-3xl font-black tracking-tight mb-1">{{ auth()->user()->name }}</h1>
            <p class="text-slate-400 text-sm">Here's what's happening with your account today.</p>
        </div>
        <div class="absolute bottom-0 right-0 w-64 h-64 opacity-5" style="background: radial-gradient(circle, #E31E24 0%, transparent 70%);"></div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Orders</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-900">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Pending</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-900">{{ $pendingOrders }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Completed</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-slate-900">{{ $completedOrders }}</p>
        </div>
    </div>

    {{-- Recent Products --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-black text-slate-900">Recent Products</h2>
                <p class="text-slate-400 text-sm mt-1">Latest additions to our catalog</p>
            </div>
            <a href="{{ route('b2c.products') }}" class="text-xs font-bold uppercase tracking-widest text-rose-600 hover:text-rose-700 flex items-center gap-1 transition-colors">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        @if($recentProducts->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            @foreach($recentProducts as $product)
            @php
            $gstAmount = $product->price * $product->gst_percentage / 100;
            $priceWithGst = $product->price + $gstAmount;
            @endphp
            <div class="group border border-slate-100 rounded-2xl overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                @if($product->images->count() > 0)
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                <div class="w-full h-40 bg-slate-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                @endif
                <div class="p-4">
                    <h4 class="font-bold text-slate-800 text-sm mb-2 line-clamp-2">{{ $product->name }}</h4>
                    <p class="text-rose-600 font-black text-base">₹{{ number_format($product->price, 2) }}</p>
                    @if($product->gst_percentage > 0)
                    <p class="text-slate-400 text-xs mt-0.5">+ {{ number_format($product->gst_percentage, 0) }}% GST</p>
                    @endif
                    <a href="{{ route('products.show', $product) }}"
                        class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-700 text-white text-xs font-bold uppercase tracking-wide hover:shadow-lg hover:shadow-rose-200 transition-all duration-300">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-slate-50 rounded-2xl">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <p class="text-slate-400 font-medium">No products available at the moment.</p>
        </div>
        @endif
    </div>

</div>
@endsection