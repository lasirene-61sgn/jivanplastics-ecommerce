@extends('frontend.b2c.layouts.app')

@section('title', 'Order #{{ $order->order_number }} - V4 Kitchen Partner')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Order Details</p>
            <h1 class="text-2xl font-black text-slate-900">#{{ $order->order_number }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('b2c.orders.invoice', $order) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-rose-600 text-rose-600 text-sm font-bold uppercase tracking-wide hover:bg-rose-600 hover:text-white transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Invoice
            </a>
            <a href="{{ route('b2c.orders') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold uppercase tracking-wide hover:bg-slate-200 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back
            </a>
        </div>
    </div>

    {{-- Order Content --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
        @include('components.invoice-display', ['order' => $order])
    </div>

</div>
@endsection