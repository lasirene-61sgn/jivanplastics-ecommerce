@extends('layouts.admin')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="max-w-7xl mx-auto pb-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Order <span class="text-indigo-600">#{{ $order->order_number }}</span></h2>
            <p class="text-sm text-slate-500 font-medium">Placed on {{ $order->created_at->timezone('Asia/Kolkata')->format('M d, Y h:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all active:scale-95">
            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Orders
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm text-sm font-bold italic">
        {{ session('success') }}
    </div>
    @endif
    @if(session('info'))
    <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-r-xl shadow-sm text-sm font-bold italic">
        {{ session('info') }}
    </div>
    @endif

    <div class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400">Customer Intelligence</div>
                <div class="p-8 space-y-4">
                    <div class="flex justify-between border-b border-slate-50 pb-3">
                        <span class="text-sm font-bold text-slate-500">Name:</span>
                        <span class="text-sm font-black text-slate-900">{{ $order->customer->name }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-3">
                        <span class="text-sm font-bold text-slate-500">Email:</span>
                        <span class="text-sm font-bold text-indigo-600 underline">{{ $order->customer->email }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-3">
                        <span class="text-sm font-bold text-slate-500">Phone:</span>
                        <span class="text-sm font-mono font-bold text-slate-900">{{ $order->customer->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-bold text-slate-500">Account:</span>
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-slate-100 text-slate-600 border border-slate-200">{{ $order->customer_type }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400">Order Lifecycle</div>
                <div class="p-8 space-y-4">
                    <div class="flex justify-between border-b border-slate-50 pb-3">
                        <span class="text-sm font-bold text-slate-500">Order Number:</span>
                        <span class="text-sm font-black text-slate-900">#{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-3 italic">
                        <span class="text-sm font-bold text-slate-500">Payment:</span>
                        <span class="text-sm font-black text-slate-900 uppercase tracking-tighter">{{ ($order->payment_method === 'cod' && $order->customer_type === 'dealer') ? 'Draft Payment' : str_replace('_', ' ', $order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-bold text-slate-500">Current Status:</span>
                        @php
                        $statusColor = $order->status == 'completed' ? 'bg-green-100 text-green-700' :
                        ($order->status == 'processing' ? 'bg-amber-100 text-amber-700' :
                        ($order->status == 'accepted' ? 'bg-sky-100 text-sky-700' :
                        ($order->status == 'under_process' ? 'bg-yellow-100 text-yellow-700' :
                        ($order->status == 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-500'))));
                        @endphp
                        <span class="px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusColor }}">
                            {{ $order->status === 'under_process' ? 'under process' : $order->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Billing Origin</h5>
                <address class="not-italic text-sm text-slate-600 leading-relaxed font-medium">
                    {{ $order->billing_address }}<br>
                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}<br>
                    <span class="font-black text-slate-900 uppercase text-[10px] tracking-widest italic">{{ $order->billing_country }}</span>
                </address>
            </div>
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
                <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Shipping Destination</h5>
                <address class="not-italic text-sm text-slate-600 leading-relaxed font-medium">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}<br>
                    <span class="font-black text-slate-900 uppercase text-[10px] tracking-widest italic">{{ $order->shipping_country }}</span>
                </address>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400">Order Items - Dispatch Status</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase">Product</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center">SKU</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center whitespace-nowrap">Ordered</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center whitespace-nowrap">Ready</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center whitespace-nowrap">Dispatched</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center whitespace-nowrap">Rejected</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center whitespace-nowrap">Pending</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-center whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <span class="text-sm font-bold text-slate-900 leading-tight block">{{ $item->product_name }}</span>
                                @if($order->customer_type === 'dealer')
                                <span class="text-[10px] font-bold text-indigo-500 italic uppercase">₹{{ number_format($item->price, 2) }} / unit ({{ $item->per_unit_pieces }} pcs @ ₹{{ number_format($item->piece_price, 2) }} / pc)</span>
                                @else
                                <span class="text-[10px] font-bold text-indigo-500 italic uppercase">₹{{ number_format($item->price, 2) }} / piece</span>
                                @endif
                                @if($item->size || $item->thickness || $item->color)
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @if($item->size)
                                    <span class="text-[9px] font-black bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 uppercase tracking-tighter">Size: {{ $item->size }}</span>
                                    @endif
                                    @if($item->thickness)
                                    <span class="text-[9px] font-black bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 uppercase tracking-tighter">Thk: {{ $item->thickness }}</span>
                                    @endif
                                    @if($item->color)
                                    <span class="text-[9px] font-black bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 uppercase tracking-tighter">Color: {{ $item->color }}</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center text-xs font-mono text-slate-400 tracking-tighter">{{ $item->product_sku ?? 'N/A' }}</td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col md:items-center mt-2 md:mt-0 gap-1">
                                    <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-600">{{ $item->total_pieces }} pcs</span>
                                    <span class="text-[10px] text-slate-400 italic font-medium">= {{ $item->quantity }} units</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-2.5 py-1 bg-green-50 rounded-lg text-xs font-black text-green-600">{{ round($item->dispatch_pending_quantity * $item->per_unit_pieces) }} pcs</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-2.5 py-1 bg-green-50 rounded-lg text-xs font-black text-green-600">{{ $item->dispatched_quantity * $item->per_unit_pieces }} pcs</span>
                                    <span class="text-[10px] text-slate-400 italic font-medium">= {{ $item->dispatched_quantity }} units</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($item->rejected_pieces > 0)
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-2.5 py-1 bg-rose-50 rounded-lg text-xs font-black text-rose-600" title="{{ $item->rejection_reason }}">{{ $item->rejected_pieces }} pcs</span>
                                    @if($item->per_unit_pieces > 0)
                                    <span class="text-[10px] text-slate-400 italic font-medium">= {{ number_format($item->rejected_pieces / $item->per_unit_pieces, 2) }} units</span>
                                    @endif
                                </div>
                                @else
                                <span class="text-slate-300 font-bold">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($item->manufacturing_pending_pieces > 0)
                                @php $pendingPcs = max(0, $item->total_pieces - $item->manufactured_pieces - $item->rejected_pieces); @endphp
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-2.5 py-1 bg-amber-50 rounded-lg text-xs font-black text-amber-600">{{ $pendingPcs }} pcs</span>
                                    @if($item->per_unit_pieces > 0)
                                    <span class="text-[10px] text-slate-400 italic font-medium">= {{ number_format($pendingPcs / $item->per_unit_pieces, 2) }} units</span>
                                    @endif
                                </div>
                                @else
                                <span class="text-slate-300 font-bold">0 pcs</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($item->is_fully_dispatched)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-green-100 text-green-700 tracking-widest"><i class="fas fa-check-circle mr-1"></i> Complete</span>
                                @elseif($item->dispatched_quantity > 0 && $item->rejected_quantity > 0 && $item->pending_quantity == 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-purple-100 text-purple-700 tracking-widest"><i class="fas fa-exclamation-circle mr-1"></i> Completed (Short)</span>
                                @elseif($item->rejected_quantity > 0 && $item->pending_quantity == 0 && $item->dispatched_quantity == 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-rose-100 text-rose-700 tracking-widest"><i class="fas fa-times-circle mr-1"></i> Rejected</span>
                                @elseif($item->dispatched_quantity > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-700 tracking-widest"><i class="fas fa-hourglass-half mr-1"></i> Partial</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase bg-slate-100 text-slate-400 tracking-widest">Awaiting</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                <div class="text-sm font-black text-slate-900 italic">₹{{ number_format($item->total + $item->tax, 2) }}</div>
                                @if($order->customer_type === 'dealer')
                                <div class="text-[9px] font-bold text-slate-400 mt-1 uppercase">
                                    (Incl. ₹{{ number_format($item->tax, 2) }} GST)
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end">
            <div class="w-full lg:w-96 bg-white rounded-3xl border border-slate-200 p-8 space-y-4 shadow-sm">
                <div class="flex justify-between text-sm font-bold text-slate-500">
                    <span>Subtotal (Gross):</span>
                    <span class="text-slate-900 italic">₹{{ number_format($order->original_subtotal ?? $order->subtotal, 2) }}</span>
                </div>
                @if($order->customer_type === 'dealer' && $order->discount_amount > 0)
                <div class="flex justify-between text-sm font-bold text-slate-500">
                    <span>Dealer Discount:</span>
                    <span class="text-rose-600 italic">-₹{{ number_format($order->discount_amount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-slate-500 border-t border-dashed border-slate-100 pt-2">
                    <span>Net Subtotal:</span>
                    <span class="text-slate-900 font-extrabold italic">₹{{ number_format(($order->original_subtotal ?? $order->subtotal) - $order->discount_amount, 2) }}</span>
                </div>
                @endif
               
                @if($order->b2b_discount_amount > 0)
                <div class="flex justify-between text-sm font-bold text-slate-500 border-b border-slate-50 pb-3">
                    <span>B2B Extra Discount (2%):</span>
                    <span class="text-indigo-600 italic">-₹{{ number_format($order->b2b_discount_amount ?? 0, 2) }}</span>
                </div>
                @endif
                @php
                $onlineDisc = $order->online_discount_amount > 0 ? $order->online_discount_amount : $order->bank_transfer_discount_amount;
                @endphp
                @if($onlineDisc > 0)
                <div class="flex justify-between text-sm font-bold text-slate-500 border-b border-slate-50 pb-3">
                    <span>{{ $order->customer_type === 'dealer' ? 'Online Discount:' : 'Bank Transfer Discount:' }}</span>
                    <span class="text-emerald-600 italic">-₹{{ number_format($onlineDisc, 2) }}</span>
                </div>
                @endif
                @if(isset($order->draft_discount_amount) && $order->draft_discount_amount > 0)
                <div class="flex justify-between text-sm font-bold text-slate-500 border-b border-slate-50 pb-3">
                    <span>Draft Payment Discount:</span>
                    <span class="text-amber-600 italic">-₹{{ number_format($order->draft_discount_amount, 2) }}</span>
                </div>
                @endif
                 <div class="flex justify-between text-sm font-bold text-slate-500">
                    <span>Tax Aggregation:</span>
                    <span class="text-slate-900 italic">₹{{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm font-bold text-slate-500 border-b border-slate-50 pb-3">
                    <span>Logistics:</span>
                    <span class="text-slate-900 italic">₹{{ number_format($order->shipping, 2) }}</span>
                </div>
                <div class="flex justify-between items-end pt-2">
                    <span class="text-xs font-black uppercase tracking-widest text-slate-400">Grand Total</span>
                    @php
                    $rejectedRefund = 0;
                    foreach($order->items as $item) {
                    if($item->rejected_quantity > 0) {
                    $p = $item->product;
                    $unitPrice = $item->original_price ?? ($p->price ?? $item->price);

                    if ($order->customer_type !== 'dealer' && $item->per_unit_pieces == 1 && $p && $p->per_quantity_pieces > 1) {
                    if ($unitPrice >= $item->price * $p->per_quantity_pieces * 0.9) {
                    $unitPrice = $unitPrice / $p->per_quantity_pieces;
                    }
                    }

                    $lineOriginalTotal = $unitPrice * $item->rejected_quantity;
                    $unitDiscount = ($order->customer_type !== 'dealer') ? 0 : (($item->discount_amount ?? 0) > 0 ? ($item->discount_amount / $item->quantity) : 0);
                    $lineDiscountTotal = $unitDiscount * $item->rejected_quantity;

                    $lineFinalTotal = $lineOriginalTotal - $lineDiscountTotal;
                    $gstRate = $p ? $p->gst_percentage : ($order->tax > 0 ? ($order->tax / $order->subtotal * 100) : 18);
                    $lineGst = $lineFinalTotal * $gstRate / 100;

                    $rejectedRefund += ($lineFinalTotal + $lineGst);
                    }
                    }
                    @endphp
                    <div class="text-right">
                        @if($rejectedRefund > 0)
                        <div class="mb-2 space-y-1">
                            @php
                            $rejBase = 0;
                            $rejTax = 0;
                            foreach($order->items as $item) {
                            if($item->rejected_quantity > 0) {
                            $p = $item->product;
                            $uPrice = $item->original_price ?? ($p->price ?? $item->price);
                            $uDisc = ($item->discount_amount ?? 0) > 0 ? ($item->discount_amount / $item->quantity) : 0;
                            $taxable = ($uPrice - $uDisc) * $item->rejected_quantity;
                            $gRate = $p ? $p->gst_percentage : ($order->tax > 0 ? ($order->tax / $order->subtotal * 100) : 18);
                            $rejBase += $taxable;
                            $rejTax += ($taxable * $gRate / 100);
                            }
                            }
                            @endphp
                            <div class="text-[9px] font-bold text-rose-400 uppercase">Rejected Base: -₹{{ number_format($rejBase, 2) }}</div>
                            <div class="text-[9px] font-bold text-rose-400 uppercase border-b border-rose-100 pb-1">Rejected GST: -₹{{ number_format($rejTax, 2) }}</div>
                            <span class="block text-xs font-bold text-slate-400 line-through italic">₹{{ number_format($order->total, 2) }}</span>
                        </div>
                        @endif
                        <span class="text-2xl font-black text-indigo-600 tracking-tighter italic">₹{{ number_format($order->total - $rejectedRefund, 2) }}</span>
                        @if($rejectedRefund > 0)
                        <div class="text-[8px] font-black text-rose-600 uppercase tracking-widest mt-1">(Refunding ₹{{ number_format($rejectedRefund, 2) }})</div>
                        @endif
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-4 gap-2 text-center uppercase tracking-tighter font-black text-[9px]">
                    <div class="p-2 bg-indigo-50 text-indigo-700 rounded-lg">Mfg: {{ $order->items->sum('manufactured_quantity') }}</div>
                    <div class="p-2 bg-rose-50 text-rose-700 rounded-lg">Rej: {{ $order->total_rejected_quantity }}</div>
                    <div class="p-2 bg-green-50 text-green-700 rounded-lg">Sent: {{ $order->total_dispatched_quantity }}</div>
                    <div class="p-2 bg-amber-50 text-amber-700 rounded-lg">Pend: {{ $order->total_pending_quantity }}</div>
                </div>
            </div>
        </div>


    </div>

    <div class="bg-slate-900 rounded-[2.5rem] shadow-2xl p-1 lg:p-2 border-4 border-slate-800">

        @if($order->tentative_dispatch_date)
        <div class="w-full lg:w-96 bg-white rounded-3xl border border-slate-200 p-8 space-y-4 shadow-sm mt-6">
            <div class="flex justify-between items-center text-sm font-bold text-slate-500">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Tentative Dispatch Date
                </span>
                <span class="text-indigo-600 font-black px-3 py-1 bg-indigo-50 rounded-lg">{{ \Carbon\Carbon::parse($order->tentative_dispatch_date)->format('M d, Y') }}</span>
            </div>
        </div>
        @endif
        <div class="bg-white rounded-[2rem] p-8 lg:p-12">
            <h5 class="text-xl font-black text-slate-900 tracking-tight mb-8 flex items-center">
                <span class="w-1.5 h-8 bg-indigo-600 mr-4 rounded-full"></span>
                Manufacturing Control Center
            </h5>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column: Tracking & Allocation -->
                <div class="bg-slate-50/50 p-6 lg:p-8 rounded-3xl border border-slate-100 space-y-8">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-500">
                            <i class="fas fa-industry"></i>
                        </div>
                        <h6 class="text-sm font-black text-slate-800 uppercase tracking-widest">Allocation & Tracking</h6>
                    </div>

                    {{-- Edit Permission Alert Panel --}}
                    @if($order->mfg_edit_permission_granted)
                    <div class="p-4 bg-sky-50 border border-sky-100 rounded-2xl flex items-start gap-3">
                        <svg class="w-5 h-5 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-black text-sky-700">Edit Permission Active</p>
                            <p class="text-[10px] font-bold text-sky-600 mt-1 uppercase tracking-widest">Awaiting manufacturer submission.</p>
                        </div>
                    </div>
                    @elseif($order->mfg_edit_request_note)
                    <div class="p-5 bg-amber-50 border border-amber-100 rounded-2xl shadow-sm">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-black text-amber-700">⚠️ Edit Request Pending</p>
                                <div class="mt-2 p-3 bg-white rounded-xl border border-amber-200 text-xs text-slate-700 italic font-medium">
                                    "{{ $order->mfg_edit_request_note }}"
                                </div>
                                <div class="mt-3">
                                    @if($order->mfg_edit_permission_count < 2)
                                        <form action="{{ route('admin.orders.grant-edit', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Grant edit permission?')"
                                            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black rounded-lg uppercase tracking-widest shadow-sm">
                                            ✅ Grant Access
                                        </button>
                                        </form>
                                        @else
                                        <span class="px-3 py-1.5 bg-rose-100 text-rose-700 text-[10px] font-black rounded-lg uppercase tracking-widest">⛔ Max Grants Reached</span>
                                        @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Edit permissions usage tracker --}}
                    @if($order->mfg_edit_permission_count > 0 || $order->manufacturing_status == 'processing')
                    <div class="flex items-center gap-2 p-3 bg-white rounded-xl border border-slate-100">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Corrections Used:</span>
                        @for($i = 1; $i <= 2; $i++)
                            <span class="w-5 h-5 rounded-full text-[9px] font-black flex items-center justify-center {{ $i <= $order->mfg_edit_permission_count ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-400' }}">{{ $i }}</span>
                            @endfor
                            <span class="text-[9px] font-medium text-slate-400 ml-auto">{{ $order->mfg_edit_permission_count }}/2 Limit</span>
                    </div>
                    @endif

                    <div class="space-y-6">
                        <div>
                            <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Current Status</h6>
                            <div class="flex items-center gap-4">
                                @php $manStatusColor = $order->manufacturing_status == 'completed' ? 'bg-green-100 text-green-700' : ($order->manufacturing_status == 'processing' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700'); @endphp
                                <span class="px-6 py-2 rounded-2xl text-xs font-black uppercase tracking-widest shadow-sm {{ $manStatusColor }}">
                                    {{ $order->manufacturing_status }}
                                </span>
                            </div>
                        </div>

                        @if($order->manufacturingTeam)
                        <div class="space-y-3 p-5 bg-white rounded-2xl border border-slate-100 shadow-sm">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase">Allocated Unit:</span>
                                <span class="text-xs font-black text-slate-900 underline decoration-indigo-200 decoration-2">{{ $order->manufacturingTeam->factory_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-400 uppercase">Assigned At:</span>
                                <span class="text-xs font-bold text-slate-600">{{ $order->allocated_at ? $order->allocated_at->timezone('Asia/Kolkata')->format('M d, Y h:i A') : 'N/A' }}</span>
                            </div>
                        </div>
                        @else
                        <div class="p-6 bg-white rounded-2xl border border-dashed border-slate-300 text-center italic text-slate-400 text-sm">
                            Not currently assigned to any unit.
                        </div>
                        @endif

                        <div class="pt-2">
                            <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">
                                Chronology Timeline
                            </h6>

                            @php
                            $timelineEvents = collect();

                            // 1. Order Placed by Customer
                            if ($order->created_at) {
                                $timelineEvents->push((object)[
                                    'label' => 'Order Placed by Customer',
                                    'date' => $order->created_at,
                                    'color' => 'bg-slate-500',
                                    'badge' => 'Customer'
                                ]);
                            }

                            // 2. Admin Accept / Reject
                            if ($order->status === 'rejected') {
                                $rejectedDate = $order->rejected_at ?? $order->updated_at ?? ($order->created_at ? $order->created_at->copy()->addMinute() : now());
                                $timelineEvents->push((object)[
                                    'label' => 'Admin Rejected Order' . ($order->rejection_reason ? ' (' . $order->rejection_reason . ')' : ''),
                                    'date' => $rejectedDate,
                                    'color' => 'bg-rose-500',
                                    'badge' => 'Admin'
                                ]);
                            } elseif ($order->status != 'pending') {
                                $acceptedDate = $order->accepted_at ?? ($order->created_at ? $order->created_at->copy()->addMinute() : ($order->updated_at ?? now()));
                                $timelineEvents->push((object)[
                                    'label' => 'Admin Accepted Order',
                                    'date' => $acceptedDate,
                                    'color' => 'bg-emerald-500',
                                    'badge' => 'Admin'
                                ]);
                            }

                            // 3. Admin Allocated to Manufacturing Unit
                            if ($order->allocated_at) {
                                $factoryName = $order->manufacturingTeam ? $order->manufacturingTeam->factory_name : 'Manufacturing Unit';
                                $timelineEvents->push((object)[
                                    'label' => 'Admin Allocated Order to ' . $factoryName,
                                    'date' => $order->allocated_at,
                                    'color' => 'bg-indigo-500',
                                    'badge' => 'Admin'
                                ]);
                            }

                            // 4. Manufacturer Accepted Order & Started Processing
                            $mfgAcceptedDate = $order->mfg_accepted_at ?? ($order->manufacturing_status && $order->manufacturing_status !== 'allocated' && $order->manufacturing_status !== 'pending' && $order->allocated_at ? $order->allocated_at->copy()->addMinute() : null);
                            if ($mfgAcceptedDate) {
                                $factoryName = $order->manufacturingTeam ? $order->manufacturingTeam->factory_name : 'Manufacturing Unit';
                                $timelineEvents->push((object)[
                                    'label' => 'Manufacturer (' . $factoryName . ') Accepted Order & Started Processing',
                                    'date' => $mfgAcceptedDate,
                                    'color' => 'bg-amber-500',
                                    'badge' => 'Manufacturer'
                                ]);
                            }

                            // 5. Tentative Dispatch Date Set by Manufacturer
                            if ($order->tentative_dispatch_date) {
                                $targetDateFormatted = \Carbon\Carbon::parse($order->tentative_dispatch_date)->timezone('Asia/Kolkata')->format('d M, Y');
                                $setDate = $order->mfg_accepted_at ?? ($order->allocated_at ? $order->allocated_at->copy()->addSeconds(30) : ($order->created_at ?? now()));
                                $timelineEvents->push((object)[
                                    'label' => 'Manufacturer Set Tentative Dispatch Date: ' . $targetDateFormatted,
                                    'date' => $setDate,
                                    'color' => 'bg-cyan-500',
                                    'badge' => 'Target Date'
                                ]);
                            }

                            // 6. Manufacturer Partial Completions (How many products completed)
                            if ($order->productionLogs && $order->productionLogs->count() > 0) {
                                foreach ($order->productionLogs as $pIndex => $pLog) {
                                    $prodItemName = $pLog->orderItem ? ($pLog->orderItem->product_name ?? 'Product') : 'Product';
                                    $prodPieces = number_format($pLog->pieces_completed);
                                    $rejInfo = $pLog->pieces_rejected > 0 ? " (" . number_format($pLog->pieces_rejected) . " pcs rejected)" : "";
                                    $timelineEvents->push((object)[
                                        'label' => "Manufacturer Completed: {$prodPieces} pcs of {$prodItemName}{$rejInfo}",
                                        'date' => $pLog->created_at,
                                        'color' => 'bg-teal-500',
                                        'badge' => 'Production'
                                    ]);
                                }
                            } elseif ($order->items->sum('manufactured_pieces') > 0 && !$order->mfg_completed_at && !$order->completed_at) {
                                // Fallback for legacy orders manufactured prior to logging table
                                $totalMfgPieces = number_format($order->items->sum('manufactured_pieces'));
                                $timelineEvents->push((object)[
                                    'label' => "Manufacturer Production In-Progress: {$totalMfgPieces} pcs manufactured",
                                    'date' => $order->updated_at ?? ($order->allocated_at ? $order->allocated_at->copy()->addHour() : now()),
                                    'color' => 'bg-teal-500',
                                    'badge' => 'Production'
                                ]);
                            }

                            // 7. Manufacturer Finished All Production (Last Completed Manufacturer Side)
                            $mfgCompletedDate = $order->mfg_completed_at ?? ($order->manufacturing_status === 'completed' && $order->completed_at ? $order->completed_at : null);
                            if ($mfgCompletedDate) {
                                $totalOrderPieces = number_format($order->items->sum('total_pieces') ?: $order->items->sum('manufactured_pieces'));
                                $timelineEvents->push((object)[
                                    'label' => "Manufacturer Finished All Production ({$totalOrderPieces} total pcs completed)",
                                    'date' => $mfgCompletedDate,
                                    'color' => 'bg-emerald-600',
                                    'badge' => 'Mfg Complete'
                                ]);
                            }

                            // 8. Admin Dispatched Batches (Partial Dispatches & Invoices)
                            if ($order->invoices && $order->invoices->count() > 0) {
                                foreach ($order->invoices as $invIndex => $invoice) {
                                    $invQty = number_format($invoice->items->sum('quantity'));
                                    $invPieces = $invoice->items->sum('pieces');
                                    $pieceStr = $invPieces > 0 ? " / " . number_format($invPieces) . " pcs" : "";
                                    $batchNum = $invIndex + 1;
                                    $timelineEvents->push((object)[
                                        'label' => "Admin Dispatched Batch {$batchNum} ({$invQty} units{$pieceStr}) - Invoice #{$invoice->invoice_number}",
                                        'date' => $invoice->dispatched_at ?? $invoice->created_at,
                                        'color' => 'bg-blue-500',
                                        'badge' => 'Dispatch Batch'
                                    ]);
                                }
                            }

                            // 9. Admin Final Dispatch Completed
                            if ($order->dispatched_at) {
                                $timelineEvents->push((object)[
                                    'label' => 'Admin Completed Final Dispatch (100% Fully Dispatched to Customer)',
                                    'date' => $order->dispatched_at,
                                    'color' => 'bg-blue-800',
                                    'badge' => 'Fully Dispatched'
                                ]);
                            }

                            $timelineEvents = $timelineEvents->sortBy('date')->values();
                            @endphp

                            <ul class="space-y-4 text-xs border-l-2 border-slate-200 pl-6 ml-2">
                                @foreach($timelineEvents as $event)
                                <li class="relative pl-2 leading-relaxed">
                                    <span class="absolute -left-[31px] top-1.5 w-3.5 h-3.5 {{ $event->color }} rounded-full ring-4 ring-slate-50"></span>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-black text-slate-800 break-words">{{ $event->label }}</span>
                                            @if(!empty($event->badge))
                                            <span class="px-1.5 py-0.5 text-[9px] font-black rounded uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                                {{ $event->badge }}
                                            </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-semibold text-slate-500 mt-0.5 flex items-center gap-1.5">
                                            <svg class="w-3 h-3 text-slate-400 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>{{ $event->date ? $event->date->timezone('Asia/Kolkata')->format('d M, Y • h:i A') . ' IST' : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-6">

                        {{-- 1. ORDER ACTION REQUIRED (PENDING STATE) --}}
                        @if($order->status === 'pending')
                        <div class="space-y-4">
                            <h6 class="text-xs font-black text-slate-700 uppercase mb-4 tracking-widest text-center">New Order Action Required</h6>
                            <div class="grid grid-cols-2 gap-4">
                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="accepted">
                                    <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm">
                                        ✅ Accept
                                    </button>
                                </form>
                                <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="w-full py-4 bg-rose-500 hover:bg-rose-600 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm">
                                        ❌ Reject
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- 2. ACCEPTED BADGE --}}
                        @elseif($order->status === 'accepted')
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                            <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Order Accepted</p>
                            <span class="text-[11px] text-emerald-600 font-medium">
                                {{ $order->accepted_at ? $order->accepted_at->timezone('Asia/Kolkata')->format('d M, Y h:i A') : 'N/A' }}
                            </span>
                        </div>

                        {{-- 3. REJECTED BADGE --}}
                        @elseif($order->status === 'rejected')
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-center">
                            <p class="text-xs font-bold text-rose-700 uppercase tracking-wider">Order Rejected</p>
                            <span class="text-[11px] text-rose-600 font-medium">
                                {{ $order->rejected_at ? $order->rejected_at->timezone('Asia/Kolkata')->format('d M, Y h:i A') : 'N/A' }}
                            </span>
                        </div>
                        @endif


                        {{-- 4. ALLOCATE / REALLOCATE SECTION (Independent condition) --}}
                        {{-- Visible when the order is NOT rejected/cancelled/pending, and either unallocated or currently in allocated status --}}
                        @if(!in_array($order->status, ['rejected', 'cancelled', 'pending']) && (!$order->manufacturing_team_id || $order->manufacturing_status === 'allocated'))
                        <form action="{{ route('admin.orders.allocate') }}" method="POST" class="space-y-4 pt-4 border-t border-slate-100">
                            @csrf
                            <input type="hidden" name="order_ids[]" value="{{ $order->id }}">
                            <div>
                                <label class="text-xs font-black text-slate-700 uppercase mb-2 block">
                                    {{ $order->manufacturingTeam ? 'Reallocate Partner' : 'Select Partner' }}
                                </label>
                                <select name="manufacturing_team_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:border-indigo-500 font-bold text-sm bg-slate-50" required>
                                    <option value="">Select a team</option>
                                    @foreach($manufacturingTeams as $team)
                                    <option value="{{ $team->id }}" {{ ($order->manufacturing_team_id == $team->id) ? 'selected' : '' }}>
                                        {{ $team->factory_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @if($order->manufacturingTeam)
                                <p class="text-[9px] text-amber-600 font-bold mt-2 uppercase tracking-widest">
                                    <i class="fas fa-info-circle mr-1"></i> Editable until accepted
                                </p>
                                @endif
                            </div>
                            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm">
                                {{ $order->manufacturingTeam ? 'Reallocate' : 'Allocate' }}
                            </button>
                        </form>
                        @endif


                        {{-- 5. INVOICES & REJECTED ITEMS --}}
                        <div class="pt-6 border-t border-slate-100">
                            @include('components.rejected-items-invoice', ['order' => $order])
                            <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 italic">Generated Invoices</h6>
                            @if($order->invoices->count() > 0)
                            <div class="space-y-3">
                                @foreach($order->invoices as $invoice)
                                <a href="{{ route('admin.orders.invoice', ['order' => $order->id, 'invoice' => $invoice->id]) }}" class="flex items-center justify-between p-3 bg-indigo-50 hover:bg-indigo-100 rounded-xl border border-indigo-100 transition-all group">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 flex items-center justify-center bg-indigo-200 text-indigo-700 rounded-lg mr-3">
                                            <i class="fas fa-file-invoice text-xs"></i>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-black text-indigo-900 block">{{ $invoice->invoice_number }}</span>
                                            <span class="text-[9px] font-bold text-indigo-500 block">{{ $invoice->created_at->timezone('Asia/Kolkata')->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                            @else
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-center italic text-slate-400 text-xs">
                                No invoices generated.
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- Right Column: Dispatch Operations -->
                <div class="bg-indigo-50/30 p-6 lg:p-8 rounded-3xl border border-indigo-50 space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-indigo-100">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-indigo-100 flex items-center justify-center text-indigo-600">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <h6 class="text-sm font-black text-indigo-900 uppercase tracking-widest">Dispatch Operations</h6>
                    </div>

                    @if($order->has_pending_items)
                    <div class="space-y-6">
                        <div class="p-4 bg-white rounded-2xl border border-indigo-100 shadow-sm">
                            <p class="text-sm font-black text-indigo-700">
                                @if($order->manufacturing_status == 'processing')
                                <i class="fas fa-cogs mr-2"></i> Work In Progress
                                @else
                                <i class="fas fa-exclamation-triangle mr-2"></i> Pending Release
                                @endif
                            </p>
                            <div class="flex items-center gap-3 mt-3">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-black rounded-lg uppercase tracking-widest border border-indigo-100 shadow-sm">
                                    Pieces waiting to ship: {{ $order->items->sum(function($i){ return $i->dispatch_pending_quantity * $i->per_unit_pieces; }) }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('admin.orders.partial-dispatch', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf @method('PUT')

                            @php
                            $hasDispatchableItems = $order->items->sum('dispatch_pending_quantity') > 0;
                            @endphp

                            @if($hasDispatchableItems)
                            <div class="space-y-3 max-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($order->items as $item)
                                @if($item->dispatch_pending_quantity > 0)
                                <div class="bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between p-3 relative overflow-hidden group hover:border-indigo-200 hover:bg-white transition-all shadow-sm">
                                    <div class="absolute left-0 top-0 w-1 h-full bg-indigo-500 group-hover:bg-indigo-600"></div>

                                    <div class="pl-3 flex-1 overflow-hidden pr-3">
                                        <div class="text-xs font-black text-slate-800 truncate" title="{{ $item->product_name }}">{{ $item->product_name }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ord: {{ $item->total_pieces }}</span>
                                            <span class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100 shadow-sm">Ready: {{ round($item->dispatch_pending_quantity * $item->per_unit_pieces) }}</span>
                                        </div>
                                    </div>

                                    <div class="w-24 shrink-0 flex flex-col items-center">
                                        <input type="number" name="dispatched_pieces[{{ $item->id }}]" class="w-full px-2 py-1.5 rounded-lg border-slate-200 text-sm font-black text-indigo-700 focus:ring-2 focus:ring-indigo-500 text-center shadow-inner" min="0" max="{{ round($item->dispatch_pending_quantity * $item->per_unit_pieces) }}" value="{{ round($item->dispatch_pending_quantity * $item->per_unit_pieces) }}" required>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                            @else
                            <div class="p-10 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 text-center space-y-4">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto text-slate-300 shadow-sm">
                                    <i class="fas fa-industry text-2xl"></i>
                                </div>
                                <h6 class="text-sm font-black text-slate-600 uppercase tracking-widest">Awaiting Production</h6>
                                <p class="text-xs text-slate-400 font-bold max-w-sm mx-auto leading-relaxed">No items are currently manufactured and ready to be dispatched. Please wait for the manufacturing team to process this order.</p>
                            </div>
                            @endif

                            <!-- Batch Level Proof -->
                            <div class="p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100 space-y-4">
                                <h6 class="text-[10px] font-black text-indigo-700 uppercase tracking-widest italic mb-2">Batch Shipment Proof (Shared)</h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">Upload Dispatch Photo</label>
                                        <input type="file" name="order_dispatch_image" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-indigo-100 file:text-indigo-700 cursor-pointer">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">Logistics Note (LR Number, Driver, etc.)</label>
                                        <input type="text" name="order_dispatch_description" class="w-full px-4 py-2 rounded-xl border-slate-200 text-sm italic" placeholder="Enter tracking or batch details...">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">Logistics Fee (₹)</label>
                                        <input type="number" name="shipping_fee" step="0.01" class="w-full px-4 py-2 rounded-xl border-slate-200 text-sm font-bold" value="0.00">
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 uppercase block mb-1">Other Charges / Dear (₹)</label>
                                        <input type="number" name="other_charges" step="0.01" class="w-full px-4 py-2 rounded-xl border-slate-200 text-sm font-bold" value="0.00">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-100 italic">Dispatch Batch</button>
                        </form>
                    </div>
                    @elseif($order->manufacturing_status == 'completed' && !$order->dispatched_at)
                    <div class="mt-8 p-6 bg-green-50 rounded-3xl border border-green-100">
                        <h6 class="text-xs font-black text-green-700 uppercase tracking-widest mb-4 italic">Final Logistics Clearance</h6>
                        <form action="{{ route('admin.orders.dispatch', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="file" name="dispatch_image" class="w-full text-xs text-slate-500 file:bg-green-100 file:text-green-700 file:rounded-full file:border-0 file:px-4 file:py-2 file:font-bold">
                            <textarea name="dispatch_description" rows="2" class="w-full p-4 rounded-2xl border-slate-200 text-xs italic outline-none focus:border-green-500" placeholder="Final handover notes..."></textarea>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1 tracking-widest">Logistics Fee (₹)</label>
                                    <input type="number" name="shipping_fee" step="0.01" class="w-full px-4 py-3 rounded-2xl border-slate-200 text-sm font-black bg-slate-50" value="0.00">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase block mb-1 tracking-widest">Other Charges / Dear (₹)</label>
                                    <input type="number" name="other_charges" step="0.01" class="w-full px-4 py-3 rounded-2xl border-slate-200 text-sm font-black bg-slate-50" value="0.00">
                                </div>
                            </div>
                            <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-green-100" onclick="return confirm('Seal and finalize logistics?')">Final Order Release</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
</style>
@endsection