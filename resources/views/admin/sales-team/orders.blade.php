@extends('layouts.admin')

@section('title', 'Sales Team Orders - Admin Panel')

@section('header', 'Portfolio Transactions')

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">{{ $salesTeam->name }}'s Orders</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">Assigned Dealer Revenue Stream</p>
            </div>
        </div>
        <a href="{{ route('admin.sales-team.show', $salesTeam) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Back to Profile
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Order Manifest</h3>
            <span class="text-[10px] font-black bg-indigo-100 text-indigo-600 px-3 py-1 rounded-lg uppercase tracking-tighter shadow-sm border border-indigo-200">
                Total Portfolio Activity
            </span>
        </div>

        <div class="p-4">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                <th class="px-6 py-4 italic">Transaction #</th>
                                <th class="px-6 py-4">Dealer Asset</th>
                                <th class="px-6 py-4 text-center">Items</th>
                                <th class="px-6 py-4 text-center">Financial Value</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Node Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($orders as $order)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">#{{ $order->order_number }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ $order->created_at->format('d M, Y') }}</div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="text-sm font-bold text-slate-700 leading-tight">{{ $order->customer->company_name ?? $order->customer->name }}</div>
                                        <div class="text-[10px] text-slate-400 uppercase font-medium italic tracking-tighter truncate max-w-[150px]">{{ $order->customer->email }}</div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-black text-slate-900">{{ $order->items->sum('quantity') }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Units</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-black text-slate-900 italic tracking-tighter italic">₹{{ number_format($order->total, 2) }}</span>
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
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="p-2.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all shadow-sm border border-transparent hover:border-indigo-100" 
                                               title="View Full Intel">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            <a href="{{ route('admin.orders.invoice', $order) }}" 
                                               class="p-2.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all shadow-sm border border-transparent hover:border-amber-100" 
                                               title="Generate Invoice">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                        </div>
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
                <div class="p-20 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-200 mb-4">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <p class="text-slate-400 italic font-medium">No order transactions found for this representative's portfolio.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection