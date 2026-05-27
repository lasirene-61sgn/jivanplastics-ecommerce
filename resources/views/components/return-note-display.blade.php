@php
    $displayOrder = $returnNote->order;
    $customer = $displayOrder->customer;
    $typeLabel = $returnNote->type == 'credit' ? 'Credit Note' : 'Debit Note';
    $typeColor = $returnNote->type == 'credit' ? 'emerald' : 'amber';
@endphp

<div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden mb-12">
    <div class="p-8 sm:p-10 border-b border-slate-100 bg-slate-50/30">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">
                    <span class="text-{{ $typeColor }}-600">{{ $typeLabel }}</span> <span class="text-{{ $typeColor }}-400">#{{ $returnNote->note_number }}</span>
                </h2>
                <div class="flex flex-wrap items-center gap-3 text-sm font-medium text-slate-500">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ $returnNote->created_at->format('M d, Y H:i') }}
                    </span>
                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-white border border-slate-200 text-slate-600 shadow-sm">
                        ORIGINAL ORDER: #{{ $displayOrder->order_number }}
                    </span>
                </div>
            </div>
            <div class="text-left md:text-right">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter uppercase">
                    {{ config('app.name', 'Jivan Plastics') }}
                </h3>
                <p class="text-sm font-bold text-{{ $typeColor }}-500 mt-1">
                    {{ str_replace(['https://', 'http://'], '', config('app.url', 'jivanplastics.com')) }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 p-8 sm:p-10 bg-white border-b border-slate-100">
        <div class="space-y-4">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Billing Details</h4>
            <address class="not-italic text-sm leading-relaxed text-slate-600 font-medium">
                <span class="block text-slate-900 font-bold mb-1 italic">Recipient Address:</span>
                {{ $displayOrder->billing_address }}<br>
                {{ $displayOrder->billing_city }}, {{ $displayOrder->billing_state }} {{ $displayOrder->billing_zip }}<br>
                <span class="font-bold text-slate-400 uppercase text-[10px] tracking-widest">{{ $displayOrder->billing_country }}</span>
            </address>
        </div>

        <div class="space-y-4">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Reference Info</h4>
            <div class="text-sm leading-relaxed text-slate-600 font-medium">
                <span class="block text-slate-900 font-bold mb-1 italic">Return Request:</span>
                <p class="mb-2">Request ID: #{{ $returnNote->return_request_id }}</p>
                <span class="block text-slate-900 font-bold mb-1 italic">Return Reason:</span>
                <p class="bg-{{ $typeColor }}-50 p-2 rounded text-xs border border-{{ $typeColor }}-100 italic">
                    {{ $returnNote->returnRequest->reason }}
                </p>
            </div>
        </div>

        <div class="space-y-4 p-6 bg-slate-50 rounded-2xl border border-slate-100">
            <h4 class="text-[10px] font-black text-{{ $typeColor }}-500 uppercase tracking-[0.2em] mb-4 text-center">Issued To</h4>
            <div class="space-y-3">
                @if($customer)
                <div class="text-center">
                    <h4 class="text-xl font-black text-slate-900 leading-none">{{ $customer->name }}</h4>
                    <div class="mt-2 space-y-1">
                        @if($customer->company_name)
                            <p class="text-sm font-bold text-slate-600">{{ $customer->company_name }}</p>
                        @endif
                        @if($customer->gst_number)
                            <div class="inline-flex items-center px-2 py-0.5 bg-{{ $typeColor }}-50 text-{{ $typeColor }}-700 rounded text-[10px] font-black uppercase tracking-tighter border border-{{ $typeColor }}-100 italic">
                                GST: {{ $customer->gst_number }}
                            </div>
                        @endif
                        <p class="text-[11px] text-slate-500 font-medium">+91 {{ $customer->phone }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="p-8 sm:p-10">
        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Note Items</h4>
        <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Product Description</th>
                        <!-- <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center whitespace-nowrap">Rate</th> -->
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center whitespace-nowrap">Returned Qty</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right whitespace-nowrap">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($returnNote->items as $item)
                        @php
                            $orderItem = $item->orderItem;
                            $product = $orderItem ? $orderItem->product : null;
                            $productName = $orderItem ? $orderItem->product_name : 'N/A';
                            $imagePath = $product && $product->images->first() ? $product->images->first()->image_path : null;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-5">
                                <div class="flex items-center">
                                    <div class="h-14 w-14 flex-shrink-0 bg-slate-100 rounded-xl border border-slate-200 overflow-hidden mr-4">
                                        @if($imagePath)
                                            <img src="{{ asset('storage/' . $imagePath) }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center text-[8px] text-slate-400 font-black uppercase tracking-tighter">No Image</div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 leading-tight">{{ $productName }}</span>
                                        @if($product)
                                        <span class="text-[10px] text-slate-400 font-mono mt-1">PID-{{ $product->id }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <!-- <td class="px-6 py-5 text-center">
                                <div class="text-sm font-bold text-slate-700 whitespace-nowrap italic">₹{{ number_format($item->unit_price, 2) }}</div>
                            </td> -->
                            <td class="px-6 py-5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 text-white rounded-lg shadow-sm">
                                        <span class="text-xs font-black">{{ $item->quantity }}</span>
                                        <span class="text-[9px] font-black uppercase text-indigo-400 ml-1">Units</span>
                                    </div>
                                    @if($item->pieces > 0)
                                    <span class="text-[9px] font-black text-slate-500 mt-1 uppercase tracking-widest italic bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                        + {{ $item->pieces }} pieces
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-sm font-black text-slate-900 whitespace-nowrap italic">₹{{ number_format($returnNote->subtotal, 2) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="px-8 sm:p-10 bg-slate-50/50 border-t border-slate-100 flex flex-col items-end">
        <div class="w-full sm:w-80 space-y-4 py-4">
            <div class="flex justify-between items-center text-sm">
                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Subtotal</span>
                <span class="font-bold text-slate-700 italic">₹{{ number_format($returnNote->subtotal, 2) }}</span>
            </div>

            @if($returnNote->discount_amount > 0)
            <div class="flex justify-between items-center text-sm">
                <span class="font-black text-emerald-600 uppercase tracking-widest text-[10px]">Pro-rated Discount</span>
                <span class="font-black text-emerald-600 italic">-₹{{ number_format($returnNote->discount_amount, 2) }}</span>
            </div>
            @endif

            <div class="flex justify-between items-center text-sm">
                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Tax Adjusted</span>
                <span class="font-bold text-slate-700 italic">+₹{{ number_format($returnNote->tax, 2) }}</span>
            </div>

            @if($returnNote->adjustment_amount != 0)
            <div class="flex justify-between items-center text-sm pt-2 border-t border-slate-100">
                <span class="font-black text-indigo-600 uppercase tracking-widest text-[10px]">Manual {{ $returnNote->adjustment_amount > 0 ? 'Charge' : 'Reduction' }}</span>
                <span class="font-black text-indigo-600 italic">{{ $returnNote->adjustment_amount > 0 ? '+' : '' }}₹{{ number_format($returnNote->adjustment_amount, 2) }}</span>
            </div>
            @endif
            
            <div class="pt-6 mt-4 border-t-2 border-slate-200 border-dashed flex justify-between items-end">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Total Adjustment</span>
                    <span class="text-xs font-bold text-{{ $typeColor }}-500 uppercase tracking-tighter italic">{{ $typeLabel }} Amount</span>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-black text-rose-800 tracking-tighter italic">
                        ₹{{ number_format($returnNote->total, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
