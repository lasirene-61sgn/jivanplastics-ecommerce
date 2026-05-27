@extends('frontend.b2c.layouts.app')

@section('title', 'Invoice #{{ $order->order_number }} - V4 Kitchen Partner')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Invoice</p>
            <h1 class="text-2xl font-black text-slate-900">#{{ $order->order_number }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('b2c.orders.show', $order) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-bold uppercase tracking-wide hover:bg-slate-200 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Order
            </a>
            <button onclick="window.print()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-700 text-white text-sm font-bold uppercase tracking-wide hover:shadow-lg hover:shadow-rose-200 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Invoice Content --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8">
        @include('components.invoice-viewer', ['order' => $order])
    </div>

</div>
@endsection