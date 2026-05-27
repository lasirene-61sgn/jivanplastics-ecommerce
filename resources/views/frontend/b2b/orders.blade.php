@extends('frontend.b2b.layouts.app')

@section('title', 'Order History - V4 Kitchen Partner')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight text-uppercase">Purchase <span class="text-red-600">History</span></h1>
            <p class="text-slate-500 font-medium mt-1">Track and manage your dealership fulfillment.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-full">Total: {{ $orders->total() }} Orders</span>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="py-32 text-center bg-white border-2 border-dashed border-slate-200 rounded-[3rem]">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">No Orders Yet</h3>
            <p class="text-slate-500 font-medium mt-2 max-w-xs mx-auto text-sm leading-relaxed">You haven't placed any orders through the dealer portal yet.</p>
            <a href="{{ route('b2b.products') }}" class="mt-8 inline-block px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-600 transition-all shadow-xl shadow-slate-200">Start Sourcing</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
                <!-- Wrap the component in a premium shell if needed, but the component itself is quite integrated -->
                <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
                    @include('components.order-display', ['order' => $order, 'showViewButton' => true])
                </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="pt-10 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>

<style>
    /* Override component styles for B2B theme consistency */
    .order-header { border-bottom: 1px solid #f1f5f9 !important; padding: 1.5rem 2rem !important; }
    .order-item-card { border: none !important; border-bottom: 1px solid #f8fafc !important; }
    .status-badge-pending { background-color: #fef3c7 !important; color: #92400e !important; }
    .status-badge-completed { background-color: #d1fae5 !important; color: #065f46 !important; }
</style>
@endsection