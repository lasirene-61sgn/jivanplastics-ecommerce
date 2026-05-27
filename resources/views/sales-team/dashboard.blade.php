@extends('layouts.sales-team')

@section('title', 'Executive Dashboard - Sales Team')

@section('header', 'Performance Command Center')

@section('content')
<div class="space-y-8 pb-12">
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm group hover:border-indigo-300 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all">
                    <i class="fas fa-shopping-cart text-lg"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gross Volume</span>
            </div>
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic">{{ $totalOrders }}</h3>
            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-tight">Total Inquiries</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm group hover:border-emerald-300 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-600 group-hover:text-white transition-all">
                    <i class="fas fa-check-double text-lg"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Finalized</span>
            </div>
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic">{{ $completedOrders }}</h3>
            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-tight">Converted Orders</p>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm group hover:border-amber-300 transition-all duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-2xl group-hover:bg-amber-600 group-hover:text-white transition-all">
                    <i class="fas fa-clock text-lg"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Waitlist</span>
            </div>
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic">{{ $pendingOrders }}</h3>
            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-tight">Awaiting Action</p>
        </div>

        <div class="bg-slate-900 p-6 rounded-[2rem] shadow-xl shadow-slate-200 group transition-all duration-300 ring-4 ring-slate-800">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-white/10 text-white rounded-2xl group-hover:bg-indigo-500 transition-all">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic">Contribution</span>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tighter italic">₹{{ number_format($totalRevenue, 2) }}</h3>
            <p class="text-xs font-bold text-slate-500 mt-1 uppercase tracking-tight">Lifetime Value (LTV)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Dealer Portfolio</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">Assigned Client Assets</p>
                </div>
                <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-tighter shadow-lg shadow-indigo-100">
                    {{ $assignedDealers->count() }} Total
                </span>
            </div>
            
            <div class="p-4 flex-1">
                @if($assignedDealers->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                    <th class="px-4 py-3">Invoiced Name</th>
                                    <th class="px-4 py-3">Corporate Entity</th>
                                    <th class="px-4 py-3 text-right">Email Node</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($assignedDealers as $dealer)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-4 py-4 text-sm font-black text-slate-900">{{ $dealer->name }}</td>
                                        <td class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-tighter italic">
                                            {{ $dealer->company_name ?? 'Individual' }}
                                        </td>
                                        <td class="px-4 py-4 text-right text-[11px] font-medium text-indigo-500 underline decoration-indigo-100">
                                            {{ $dealer->email }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-12 text-center text-slate-400 italic text-sm">No dealer assignments detected.</div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Activity Stream</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter mt-0.5">Real-time Order Flow</p>
                </div>
                <div class="h-2 w-2 rounded-full bg-emerald-500 animate-ping"></div>
            </div>

            <div class="p-4 flex-1">
                @if($recentOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                                    <th class="px-4 py-3">Reference #</th>
                                    <th class="px-4 py-3 text-center">Value</th>
                                    <th class="px-4 py-3 text-right">Lifecycle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-black text-slate-900">#{{ $order->order_number }}</div>
                                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">
                                                {{ $order->customer->company_name ?? $order->customer->name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm font-black text-slate-900 italic tracking-tighter">₹{{ number_format($order->total, 2) }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-right whitespace-nowrap">
                                            @php 
                                                $sColor = match($order->status) {
                                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                                    'pending' => 'bg-amber-100 text-amber-700',
                                                    default => 'bg-slate-100 text-slate-500'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest {{ $sColor }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="py-12 text-center text-slate-400 italic text-sm">No transaction activity logged.</div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection