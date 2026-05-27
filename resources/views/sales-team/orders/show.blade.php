@extends('layouts.sales-team')

@section('title', 'Order Details - Sales Team Portal')

@section('header', 'Transaction Intelligence')

@section('content')
<div class="max-w-7xl mx-auto pb-20 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Order #{{ $order->order_number }}</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">Verified Transaction Record</p>
            </div>
        </div>
        <a href="{{ route('sales-team.orders.index') }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Back to Manifest
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400">Order Information</div>
            <div class="p-8 flex-1 space-y-4">
                <div class="flex justify-between border-b border-slate-50 pb-3">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-tighter">Reference Node</span>
                    <span class="text-sm font-black text-slate-900">#{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-3">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-tighter">Timestamp</span>
                    <span class="text-sm font-bold text-slate-700 italic">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-3">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-tighter">Workflow State</span>
                    @php 
                        $sColor = match($order->status) {
                            'completed' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            default => 'bg-slate-100 text-slate-500'
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $sColor }}">{{ $order->status }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-50 pb-3">
                    <span class="text-sm font-bold text-slate-400 uppercase tracking-tighter">Settlement</span>
                    <span class="text-sm font-black text-indigo-600 uppercase">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                </div>
                
                <div class="pt-4 space-y-2">
                    <div class="flex justify-between text-xs font-bold text-slate-500">
                        <span>Net Subtotal:</span>
                        <span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-slate-500">
                        <span>GST Aggregation:</span>
                        <span>₹{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-slate-500">
                        <span>Logistics Cost:</span>
                        <span>₹{{ number_format($order->shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-end pt-4 border-t border-slate-100">
                        <span class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Grand Total Value</span>
                        <span class="text-2xl font-black text-slate-900 tracking-tighter italic">₹{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400">Customer Portfolio Info</div>
            <div class="p-8 flex-1 space-y-6">
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Liaison Name</p>
                    <p class="text-lg font-black text-slate-900 leading-tight">{{ $order->customer->name }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Corporate Entity</p>
                        <p class="text-sm font-bold text-slate-700 italic">{{ $order->customer->company_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Communication</p>
                        <p class="text-xs font-bold text-slate-600 truncate">{{ $order->customer->email }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50">
                    <div class="flex gap-8">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Billing Destination</p>
                            <address class="not-italic text-xs font-bold text-slate-500 leading-relaxed uppercase tracking-tighter">
                                {{ $order->billing_address }}<br>
                                {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}<br>
                                {{ $order->billing_country }}
                            </address>
                        </div>
                        @if($order->billing_address !== $order->shipping_address)
                        <div>
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-2 italic">Shipping Destination</p>
                            <address class="not-italic text-xs font-bold text-slate-500 leading-relaxed uppercase tracking-tighter">
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                                {{ $order->shipping_country }}
                            </address>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Itemized manifest</h3>
            <span class="text-[10px] font-black bg-indigo-600 text-white px-3 py-1 rounded-lg uppercase tracking-tighter shadow-lg shadow-indigo-100">
                {{ $order->items->count() }} Line Items
            </span>
        </div>

        <div class="p-4">
            @if($order->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                <th class="px-6 py-4">Product Assets</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Global SKU</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Unit Price</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Quantity</th>
                                <th class="px-6 py-4 text-center whitespace-nowrap">Tax (GST)</th>
                                <th class="px-6 py-4 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $item->product_name }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-tighter">{{ $item->product_sku ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-bold text-slate-600 italic">₹{{ number_format($item->price, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-900 border border-slate-200">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-400">₹{{ number_format($item->gst_amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <span class="text-sm font-black text-slate-900 italic tracking-tighter">₹{{ number_format($item->total, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-900 text-white rounded-b-3xl">
                            <tr class="font-black text-[10px] uppercase tracking-[0.2em]">
                                <td colspan="3" class="px-6 py-6 text-indigo-400 italic">Consolidated Totals</td>
                                <td class="px-6 py-6 text-center border-l border-white/5">{{ $order->items->sum('quantity') }} Units</td>
                                <td class="px-6 py-6 text-center border-l border-white/5 whitespace-nowrap">₹{{ number_format($order->tax, 2) }} Tax</td>
                                <td class="px-6 py-6 text-right border-l border-white/5 text-sm italic tracking-tighter text-indigo-400 whitespace-nowrap">₹{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="p-12 text-center text-slate-400 italic text-sm">No item data found for this reference.</p>
            @endif
        </div>
    </div>
</div>
@endsection