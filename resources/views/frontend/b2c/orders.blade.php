@extends('frontend.b2c.layouts.app')

@section('title', 'My Orders - V4 Kitchen Partner')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">My Orders</h1>
            <p class="text-slate-400 text-sm mt-1">Track and manage your order history</p>
        </div>
        <a href="{{ route('b2c.products') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-700 text-white text-sm font-bold uppercase tracking-wide hover:shadow-lg hover:shadow-rose-200 transition-all duration-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            Shop Now
        </a>
    </div>

    @if($orders->isEmpty())
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-16 text-center">
        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-black text-slate-800 mb-2">No orders yet</h3>
        <p class="text-slate-400 text-sm mb-6">You haven't placed any orders yet. Start shopping!</p>
        <a href="{{ route('b2c.products') }}"
            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-red-700 text-white text-sm font-bold uppercase tracking-wide hover:shadow-lg hover:shadow-rose-200 transition-all duration-300">
            Start Shopping
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Order</p>
                        <p class="text-base font-black text-slate-900">#{{ $order->order_number }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @php
                    $statusColors = [
                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'processing' => 'bg-blue-50 text-blue-700 border-blue-200',
                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'cancelled' => 'bg-slate-100 text-slate-500 border-slate-200',
                    ];
                    $statusColor = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600 border-slate-200';
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColor }} capitalize">
                        {{ $order->status }}
                    </span>
                    <a href="{{ route('b2c.orders.show', $order) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold uppercase tracking-wide hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all duration-300">
                        View
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-6 text-sm text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $order->created_at->format('d M Y') }}
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        {{ $order->items->count() ?? 0 }} items
                    </span>
                </div>
                <p class="text-lg font-black text-rose-600">₹{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>
        @endforeach

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
    @endif

</div>
@endsection