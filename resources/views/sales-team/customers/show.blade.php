@extends('layouts.sales-team')

@section('title', 'Customer Intelligence - Sales Portal')

@section('header', 'Client Relationship Profile')

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100 italic">
                <i class="fas fa-user-tie text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">{{ $customer->name }}</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">Verified Portfolio Asset</p>
            </div>
        </div>
        <a href="{{ route('sales-team.customers.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Portfolio Manifest
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400 italic">Account Intelligence</div>
                <div class="p-8 space-y-5">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Business Entity</p>
                        <p class="text-lg font-black text-slate-900 leading-tight">{{ $customer->company_name ?? 'Individual Liaison' }}</p>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm font-bold text-slate-600">
                            <i class="fas fa-envelope text-slate-300 w-4"></i> {{ $customer->email }}
                        </div>
                        <div class="flex items-center gap-3 text-sm font-bold text-slate-600">
                            <i class="fas fa-phone text-slate-300 w-4"></i> {{ $customer->phone ?? 'Unlisted' }}
                        </div>
                    </div>

                    <div class="pt-5 border-t border-slate-50 space-y-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Physical Node</p>
                            <address class="not-italic text-xs font-bold text-slate-500 leading-relaxed uppercase tracking-tighter">
                                @if($customer->address)
                                    {{ $customer->address }}<br>
                                    {{ $customer->city }}, {{ $customer->state }} {{ $customer->zip }}<br>
                                    <span class="text-indigo-400">{{ $customer->country }}</span>
                                @else
                                    <span class="text-slate-300">Address Data Missing</span>
                                @endif
                            </address>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase bg-indigo-50 text-indigo-700 border border-indigo-100 italic tracking-widest">
                                {{ $customer->customer_type }}
                            </span>
                            @if($customer->is_active)
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase tracking-widest">Active</span>
                            @else
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black bg-rose-50 text-rose-500 border border-rose-100 uppercase tracking-widest">Suspended</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-8 italic relative z-10">Portfolio Metrics</h3>
                <div class="space-y-6 relative z-10">
                    <div class="flex justify-between items-end border-b border-white/5 pb-4">
                        <div>
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Lifetime Value</p>
                            <p class="text-3xl font-black italic tracking-tighter text-white">₹{{ number_format($totalSpent, 2) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Total Invoices</p>
                            <p class="text-xl font-black italic text-indigo-400">{{ $totalOrders }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1 italic">Last Transaction Activity</p>
                        <p class="text-sm font-black italic uppercase text-slate-300">
                            {{ $lastOrderDate ? $lastOrderDate->format('d M, Y') : 'No History Detected' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Transaction Manifest</h3>
                    <span class="text-[10px] font-black bg-white border border-slate-200 text-slate-400 px-3 py-1 rounded-lg uppercase tracking-tighter italic shadow-sm">
                        Historical Log
                    </span>
                </div>

                <div class="p-4">
                    @if($orders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                        <th class="px-6 py-4">Node Ref</th>
                                        <th class="px-6 py-4 text-center whitespace-nowrap">Asset Count</th>
                                        <th class="px-6 py-4 text-center">Value</th>
                                        <th class="px-6 py-4 text-center">Workflow</th>
                                        <th class="px-6 py-4 text-right">Intel</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-slate-50/50 transition-colors group">
                                            <td class="px-6 py-5">
                                                <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600">#{{ $order->order_number }}</div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight italic">{{ $order->created_at->format('d M, Y') }}</div>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="text-xs font-black text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">{{ $order->items->sum('quantity') }}</span>
                                            </td>
                                            <td class="px-6 py-5 text-center whitespace-nowrap">
                                                <span class="text-sm font-black text-slate-900 tracking-tighter italic">₹{{ number_format($order->total, 2) }}</span>
                                            </td>
                                            <td class="px-6 py-5 text-center">
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
                                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                                <a href="{{ route('sales-team.orders.show', $order) }}" class="p-2 text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all inline-block border border-transparent hover:border-indigo-100">
                                                    <i class="fas fa-arrow-right text-xs"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-6 bg-slate-50 border-t border-slate-100">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="py-20 text-center">
                            <div class="text-slate-200 mb-4 italic">
                                <i class="fas fa-file-invoice fa-3x"></i>
                            </div>
                            <p class="text-slate-400 font-bold tracking-tight italic">No transactions found for this liaison node.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection