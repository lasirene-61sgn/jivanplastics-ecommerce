@extends('layouts.admin')

@section('title', 'Customer Portfolio - Admin Panel')

@section('header', 'Relationship Intelligence')

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">{{ $customer->name }}</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">Assigned to: {{ $salesTeam->name }}</p>
            </div>
        </div>
        <a href="{{ route('admin.sales-team.customer-relationships', $salesTeam) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Back to Portfolio
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-8">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400">Core Profile</div>
                <div class="p-8 space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest mb-1">Business Entity</p>
                        <p class="text-lg font-black text-slate-900 leading-tight">{{ $customer->company_name ?? 'Individual' }}</p>
                        @if($customer->gst_number)
                            <p class="text-[10px] font-mono text-slate-400 mt-1 uppercase">GST: {{ $customer->gst_number }}</p>
                        @endif
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm font-medium text-slate-600">
                            <i class="fas fa-envelope text-slate-300 w-4"></i> {{ $customer->email }}
                        </div>
                        <div class="flex items-center gap-3 text-sm font-medium text-slate-600">
                            <i class="fas fa-phone text-slate-300 w-4"></i> {{ $customer->phone ?? 'Not Linked' }}
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-50">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic text-indigo-500">Logistics Node</p>
                        <address class="not-italic text-xs font-bold text-slate-500 leading-relaxed uppercase tracking-tighter">
                            {{ $customer->address ?? 'No Address' }}<br>
                            {{ $customer->city }}, {{ $customer->state }} {{ $customer->zip }}<br>
                            {{ $customer->country }}
                        </address>
                    </div>

                    <div class="flex gap-2">
                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase bg-indigo-50 text-indigo-700 border border-indigo-100 italic">{{ $customer->customer_type }}</span>
                        @if($customer->is_active)
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase bg-green-100 text-green-700 border border-green-200 italic tracking-widest">Active Member</span>
                        @else
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase bg-rose-100 text-rose-700 border border-rose-200 italic tracking-widest">Inactive</span>
                        @endif
                    </div>

                    <a href="{{ route('admin.customers.show', $customer) }}" class="block w-full py-3 bg-slate-900 hover:bg-black text-white rounded-2xl text-center text-[10px] font-black uppercase tracking-[0.2em] transition-all">
                        Edit Full Profile
                    </a>
                </div>
            </div>

            <div class="bg-indigo-900 rounded-[2rem] p-8 text-white shadow-2xl relative overflow-hidden group">
                <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-indigo-800/50 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5V2a1 1 0 112 0v5h-5zM11 13a1 1 0 102 0v-5h5a1 1 0 100-2h-5V1a1 1 0 10-2 0v5H4a1 1 0 100 2h5v5z" clip-rule="evenodd"></path></svg>
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-6 italic relative z-10">Sales Metrics</h3>
                <div class="space-y-6 relative z-10">
                    <div class="flex justify-between items-end border-b border-indigo-800 pb-4">
                        <div>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Lifetime Value</p>
                            <p class="text-2xl font-black italic">₹{{ number_format($totalSpent, 2) }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Orders</p>
                            <p class="text-xl font-black italic">{{ $totalOrders }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Last Node</p>
                            <p class="text-xs font-black italic uppercase text-indigo-200 mt-1">{{ $lastOrderDate ? $lastOrderDate->format('d M, y') : 'None' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Order History Manifest</h3>
                    <span class="text-[10px] font-black bg-slate-100 text-slate-500 px-3 py-1 rounded-lg uppercase tracking-tighter">Total {{ $orders->count() }} Records</span>
                </div>
                
                @if($orders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                    <th class="px-8 py-4">Transaction #</th>
                                    <th class="px-8 py-4 text-center">Items</th>
                                    <th class="px-8 py-4 text-center whitespace-nowrap">Order Value</th>
                                    <th class="px-8 py-4 text-center">Status</th>
                                    <th class="px-8 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-slate-50 transition-colors group">
                                        <td class="px-8 py-5">
                                            <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">#{{ $order->order_number }}</div>
                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $order->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="text-xs font-black text-slate-600 bg-slate-100 px-2 py-1 rounded-lg italic">{{ $order->items->sum('quantity') }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="text-sm font-black text-slate-900 italic tracking-tighter">₹{{ number_format($order->total, 2) }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            @php 
                                                $sColor = match($order->status) {
                                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                                    'pending' => 'bg-amber-100 text-amber-700',
                                                    default => 'bg-slate-100 text-slate-500'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $sColor }}">
                                                {{ $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('admin.orders.show', $order) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="View Order">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                                <a href="{{ route('admin.orders.invoice', $order) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-all" title="Invoice">
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
                    <div class="p-20 text-center text-slate-400 font-medium italic">
                        No transactions recorded for this customer account.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection