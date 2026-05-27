@extends('layouts.sales-team')

@section('title', 'Orders Manifest - Sales Portal')

@section('header', 'Transaction Portfolio')

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Orders Manifest</h2>
            <p class="text-sm text-slate-500 font-medium italic">Comprehensive log of all dealer transactions assigned to your desk.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 shadow-sm">
                Total Logs: {{ $orders->total() }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Transaction Stream</h3>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-tighter">Live Updates Enabled</span>
            </div>
        </div>

        <div class="p-4">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 italic">
                                <th class="px-6 py-4">Node Ref</th>
                                <th class="px-6 py-4">Invoiced Dealer</th>
                                <th class="px-6 py-4 text-center">Items</th>
                                <th class="px-6 py-4 text-center">Value</th>
                                <th class="px-6 py-4 text-center">Workflow Status</th>
                                <th class="px-6 py-4 text-right">Intel</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($orders as $order)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">#{{ $order->order_number }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tight italic">{{ $order->created_at->format('M d, Y') }}</div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="text-sm font-bold text-slate-700 leading-tight">
                                            {{ $order->customer->company_name ?? $order->customer->name }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-medium truncate max-w-[180px] lowercase">{{ $order->customer->email }}</div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-900 border border-slate-200">
                                            {{ $order->items->sum('quantity') }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-black text-slate-900 tracking-tighter italic">₹{{ number_format($order->total, 2) }}</span>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        @php 
                                            $sColor = match($order->status) {
                                                'completed' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                default => 'bg-slate-100 text-slate-500 border-slate-200'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $sColor }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <a href="{{ route('sales-team.orders.show', $order) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all active:scale-95 group/btn">
                                            View Intel 
                                            <svg class="w-3 h-3 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="py-24 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-200 mb-4 italic">
                        <i class="fas fa-shopping-bag fa-2x"></i>
                    </div>
                    <p class="text-slate-400 font-bold tracking-tight italic">No transactions detected in your manifest queue.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection