@if($order->items->where('rejected_quantity', '>', 0)->count() > 0)
<div class="bg-white rounded-3xl shadow-xl shadow-rose-100/50 border border-rose-100 overflow-hidden mb-12">
    <!-- Header -->
    <div class="p-8 sm:p-10 border-b border-rose-100 bg-rose-50/30">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
            <div>
                <h2 class="text-3xl font-black text-rose-900 tracking-tight mb-2">
                    Rejection Memo <span class="text-rose-600">#{{ $order->order_number }}-REJ</span>
                </h2>
                <div class="flex flex-wrap items-center gap-3 text-sm font-medium text-slate-500">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        {{ now()->format('M d, Y') }}
                    </span>
                    <span class="w-1 h-1 bg-rose-300 rounded-full"></span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-100 text-rose-700 border border-rose-200 shadow-sm">
                        Rejected
                    </span>
                </div>
            </div>
            <div class="text-left md:text-right">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter uppercase">
                    {{ config('app.name', 'V4 Kitchen Partner') }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="p-8 sm:p-10 bg-white">
        <h4 class="text-[10px] font-black text-rose-400 uppercase tracking-[0.2em] mb-6">Rejected Items Details</h4>
        <div class="overflow-x-auto rounded-2xl border border-rose-100 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-rose-50/50 border-b border-rose-200">
                        <th class="px-6 py-4 text-[10px] font-black text-rose-900 uppercase tracking-widest">Product</th>
                        <th class="px-6 py-4 text-[10px] font-black text-rose-900 uppercase tracking-widest text-center whitespace-nowrap">Price</th>
                        <th class="px-6 py-4 text-[10px] font-black text-rose-900 uppercase tracking-widest text-center whitespace-nowrap">Pieces Rejected</th>
                        <th class="px-6 py-4 text-[10px] font-black text-rose-900 uppercase tracking-widest text-right whitespace-nowrap">Refund Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-50">
                    @php
                        $totalRefund = 0;
                    @endphp
                    @foreach($order->items as $item)
                        @if($item->rejected_quantity > 0)
                            @php
                                $product = $item->product;
                                // Calculations
                                $unitPrice = $item->original_price ?? ($product->price ?? $item->price);
                                $totalPrice = $unitPrice * $item->rejected_quantity;
                                
                                // Discount
                                $unitDiscount = ($item->discount_amount ?? 0) > 0 ? ($item->discount_amount / $item->quantity) : 0;
                                $totalDiscount = $unitDiscount * $item->rejected_quantity;
                                
                                // GST
                                $taxableAmount = $totalPrice - $totalDiscount;
                                $gstRate = $product ? $product->gst_percentage : ($order->tax > 0 ? ($order->tax / $order->subtotal * 100) : 18);
                                $gstAmount = $taxableAmount * $gstRate / 100;
                                
                                $lineRefund = $taxableAmount + $gstAmount;
                                $totalRefund += $lineRefund;
                                
                                $imagePath = $product && $product->images->first() ? $product->images->first()->image_path : null;
                            @endphp
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 flex-shrink-0 bg-slate-100 rounded-lg border border-slate-200 overflow-hidden mr-4">
                                            @if($imagePath)
                                                <img src="{{ asset('storage/' . $imagePath) }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center text-[8px] text-slate-400 font-black uppercase">No Img</div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="text-sm font-bold text-slate-900 leading-tight block">{{ $item->product_name }}</span>
                                            @if($item->rejection_reason)
                                                <span class="text-[10px] text-rose-500 italic mt-1 block">Reason: {{ $item->rejection_reason }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="text-xs font-bold text-slate-700">₹{{ number_format($unitPrice, 2) }}</div>
                                    @if($unitDiscount > 0)
                                        <div class="text-[9px] text-emerald-600 font-bold">Disc: -₹{{ number_format($unitDiscount, 2) }}</div>
                                    @endif
                                    <div class="text-[9px] text-slate-500">GST: {{ $gstRate }}%</div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-2 py-1 bg-rose-100 text-rose-700 rounded-md text-xs font-black">{{ $item->rejected_pieces }}</span>
                                </td>
                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase leading-none mb-1">Base: ₹{{ number_format($taxableAmount, 2) }}</div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase leading-none">GST: ₹{{ number_format($gstAmount, 2) }}</div>
                                    <div class="mt-2 font-black text-rose-700 text-sm italic">
                                        ₹{{ number_format($lineRefund, 2) }}
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary & Footer -->
    <div class="px-8 sm:px-10 py-6 bg-rose-50/50 border-t border-rose-100 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-start gap-3 bg-white p-4 rounded-2xl border border-rose-100 max-w-md shadow-sm">
            <div class="p-2 bg-rose-100 text-rose-600 rounded-full flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-xs font-bold text-rose-800 uppercase tracking-wide mb-1">Refund Information</p>
                <p class="text-[11px] text-slate-600 font-medium leading-relaxed">
                    The total refund amount of <span class="font-black text-rose-700">₹{{ number_format($totalRefund, 2) }}</span> for these rejected items will be credited to your linked bank account within <span class="underline decoration-rose-300 decoration-2">3 working days</span>.
                </p>
            </div>
        </div>
        
        <div class="text-right space-y-2">
            @php
                $totalBase = 0;
                $totalTax = 0;
                foreach($order->items as $item) {
                    if($item->rejected_quantity > 0) {
                        $p = $item->product;
                        $uPrice = $item->original_price ?? ($p->price ?? $item->price);
                        $uDisc = ($item->discount_amount ?? 0) > 0 ? ($item->discount_amount / $item->quantity) : 0;
                        $taxable = ($uPrice - $uDisc) * $item->rejected_quantity;
                        $gRate = $p ? $p->gst_percentage : ($order->tax > 0 ? ($order->tax / $order->subtotal * 100) : 18);
                        
                        $totalBase += $taxable;
                        $totalTax += ($taxable * $gRate / 100);
                    }
                }
            @endphp
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Rejected Subtotal: ₹{{ number_format($totalBase, 2) }}
            </div>
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Rejected GST Aggregation: ₹{{ number_format($totalTax, 2) }}
            </div>
            <div class="pt-2 border-t border-rose-200">
                <span class="block text-[10px] font-black text-rose-400 uppercase tracking-[0.3em] mb-1">Total Refund (Incl. GST)</span>
                <span class="text-3xl font-black text-rose-700 tracking-tighter italic">₹{{ number_format($totalRefund, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endif
