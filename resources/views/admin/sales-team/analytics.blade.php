@extends('layouts.admin')

@section('title', 'Sales Analytics - Admin Panel')

@section('header', 'Performance Intelligence')

@section('content')
<div class="space-y-8 pb-12">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Analytics: {{ $salesTeam->name }}</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase italic">{{ $salesTeam->department }} Division</p>
            </div>
        </div>
        <a href="{{ route('admin.sales-team.show', $salesTeam) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Profile View
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm group hover:border-indigo-300 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gross Inquiries</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $totalOrders }}</h3>
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm group hover:border-emerald-300 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Conversion Success</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $completedOrders }}</h3>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm group hover:border-amber-300 transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pipeline Waitlist</p>
            <div class="flex items-end justify-between">
                <h3 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $pendingOrders }}</h3>
                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-600 group-hover:text-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl shadow-slate-200 group transition-all">
            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1 italic">Contribution Value</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-white tracking-tighter italic">₹{{ number_format($totalRevenue, 2) }}</h3>
                <div class="p-2 bg-white/10 text-white rounded-lg group-hover:bg-indigo-500 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Highest Velocity Assets</h3>
                <span class="text-[9px] font-black bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded uppercase">Volume Leaderboard</span>
            </div>
            <div class="p-4">
                @if($topProducts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-4 py-3">Product Nomenclature</th>
                                    <th class="px-4 py-3 text-right">Qty Liquidated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($topProducts as $product)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-4">
                                            <span class="text-sm font-bold text-slate-700 block">{{ $product->name }}</span>
                                            <span class="text-[9px] text-slate-400 uppercase font-bold tracking-tighter italic">Validated SKU SKU-{{ $product->id }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-right">
                                            <span class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-900">{{ $product->total_quantity }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-8 text-center text-slate-400 italic text-sm">No transaction data available yet.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Recent Activity Log</h3>
                <span class="text-[9px] font-black bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded uppercase">Live Feed</span>
            </div>
            <div class="p-4">
                @if($recentOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                    <th class="px-4 py-3">Invoiced Dealer</th>
                                    <th class="px-4 py-3 text-center">Value</th>
                                    <th class="px-4 py-3 text-right whitespace-nowrap">Order Node</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-bold text-slate-700 leading-tight">{{ $order->customer->company_name ?? $order->customer->name }}</div>
                                            <div class="text-[9px] text-slate-400 uppercase font-medium">{{ $order->created_at->format('d M, Y') }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm font-black text-slate-900 italic">₹{{ number_format($order->total, 2) }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-right whitespace-nowrap">
                                            @php 
                                                $sColor = $order->status === 'completed' ? 'bg-emerald-500' : ($order->status === 'pending' ? 'bg-amber-500' : 'bg-slate-400');
                                            @endphp
                                            <div class="flex flex-col items-end">
                                                <span class="text-[10px] font-black text-slate-900">#{{ $order->order_number }}</span>
                                                <span class="inline-flex items-center text-[8px] font-black uppercase text-white px-1.5 rounded mt-1 {{ $sColor }}">{{ $order->status }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-8 text-center text-slate-400 italic text-sm">No recent orders recorded.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection