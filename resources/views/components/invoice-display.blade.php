@php
    $displayOrder = $order ?? (isset($invoice) && $invoice->order_id ? $invoice->order : null);
    $customer = $displayOrder ? $displayOrder->customer : (isset($invoice) && $invoice->rewardClaim ? $invoice->rewardClaim->customer : null);
@endphp

<div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden mb-12">
    <div class="p-8 sm:p-10 border-b border-slate-100 bg-slate-50/30">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-2">
                    @if(isset($invoice))
                        @if(str_contains($invoice->invoice_number, '-RET-'))
                            <span class="text-rose-600">Return Invoice</span> <span class="text-rose-400">#{{ $invoice->invoice_number }}</span>
                        @elseif(str_contains($invoice->invoice_number, 'REW-'))
                            <span class="text-amber-600">Reward Invoice</span> <span class="text-amber-400">#{{ $invoice->invoice_number }}</span>
                        @else
                            Invoice <span class="text-indigo-600">#{{ $invoice->invoice_number }}</span>
                        @endif
                    @else
                        Invoice <span class="text-indigo-600">#{{ $displayOrder->order_number ?? 'N/A' }}</span>
                    @endif
                </h2>
                <div class="flex flex-wrap items-center gap-3 text-sm font-medium text-slate-500">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @if(isset($invoice))
                            {{ $invoice->created_at->format('M d, Y H:i') }}
                        @elseif($displayOrder)
                            {{ $displayOrder->created_at->format('M d, Y H:i') }}
                        @endif
                    </span>
                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-white border border-slate-200 text-slate-600 shadow-sm">
                        {{ $displayOrder->status ?? 'FULFILLED' }}
                    </span>
                </div>
                
                @if($displayOrder && $displayOrder->tentative_dispatch_date)
                <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 border border-indigo-100 rounded-lg">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest">Tentative Dispatch: {{ \Carbon\Carbon::parse($displayOrder->tentative_dispatch_date)->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
            <div class="text-left md:text-right">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter uppercase">
                    {{ config('app.name', 'V4 Kitchen Partner') }}
                </h3>
                <p class="text-sm font-bold text-indigo-500 mt-1">
                    {{ str_replace(['https://', 'http://'], '', config('app.url', 'v4kitchen.com')) }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 p-8 sm:p-10 bg-white border-b border-slate-100">
        <div class="space-y-4">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Billing Details</h4>
            <address class="not-italic text-sm leading-relaxed text-slate-600 font-medium">
                <span class="block text-slate-900 font-bold mb-1 italic">Recipient Address:</span>
                @if($displayOrder)
                    {{ $displayOrder->billing_address }}<br>
                    {{ $displayOrder->billing_city }}, {{ $displayOrder->billing_state }} {{ $displayOrder->billing_zip }}<br>
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-widest">{{ $displayOrder->billing_country }}</span>
                @elseif($customer)
                    {{ $customer->address }}<br>
                    {{ $customer->city }}, {{ $customer->state }} {{ $customer->zip_code }}<br>
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-widest">{{ $customer->country }}</span>
                @else
                    N/A
                @endif
            </address>
        </div>

        <div class="space-y-4">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Shipping Logistics</h4>
            <address class="not-italic text-sm leading-relaxed text-slate-600 font-medium">
                <span class="block text-slate-900 font-bold mb-1 italic">Delivery Destination:</span>
                @if($displayOrder)
                    {{ $displayOrder->shipping_address }}<br>
                    {{ $displayOrder->shipping_city }}, {{ $displayOrder->shipping_state }} {{ $displayOrder->shipping_zip }}<br>
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-widest">{{ $displayOrder->shipping_country }}</span>
                @elseif($customer)
                    {{ $customer->address }}<br>
                    {{ $customer->city }}, {{ $customer->state }} {{ $customer->zip_code }}<br>
                    <span class="font-bold text-slate-400 uppercase text-[10px] tracking-widest">{{ $customer->country }}</span>
                @else
                    N/A
                @endif
            </address>
        </div>

        <div class="space-y-4 p-6 bg-slate-50 rounded-2xl border border-slate-100">
            <h4 class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-4">Partner Profile</h4>
            <div class="space-y-3">
                @if($customer)
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Customer Details</span>
                    <h4 class="text-xl font-black text-slate-900 leading-none">{{ $customer->name }}</h4>
                    <div class="mt-2 space-y-1">
                        @if($customer->company_name)
                            <p class="text-sm font-bold text-slate-600">{{ $customer->company_name }}</p>
                        @endif
                        @if($customer->gst_number)
                            <div class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-[10px] font-black uppercase tracking-tighter border border-indigo-100 italic">
                                GST: {{ $customer->gst_number }}
                            </div>
                        @endif
                        <p class="text-[11px] text-slate-500 font-medium">+91 {{ $customer->phone }}</p>
                    </div>
                </div>
                @else
                    <p class="text-sm text-slate-400 italic">No customer profile linked.</p>
                @endif
            </div>

            <!-- Total pieces / summary -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 p-6 bg-slate-50/50 border-t border-slate-100">
                <div>
                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Manifest Status</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-black uppercase tracking-widest">
                        @if(isset($invoice))
                            Invoice Generated
                        @elseif($displayOrder)
                            {{ $displayOrder->is_fully_dispatched ? 'Fully Dispatched' : 'Partial Dispatch' }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div>
                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pieces</span>
                    <span class="text-xs font-black text-slate-900">
                        @if(isset($invoice))
                            {{ $invoice->items->count() }}
                        @elseif($displayOrder)
                            @if($displayOrder->customer_type === 'dealer')
                                {{ $displayOrder->items->sum(function($i){ return $i->dispatched_quantity * $i->per_unit_pieces; }) }} / {{ $displayOrder->items->sum('total_pieces') }} pcs
                            @else
                                {{ $displayOrder->items->sum('dispatched_quantity') }} / {{ $displayOrder->items->sum('quantity') }} pcs
                            @endif
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div>
                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Date</span>
                    <span class="text-xs font-black text-slate-900">
                        @if(isset($invoice))
                            {{ $invoice->created_at->format('M d, Y') }}
                        @elseif($displayOrder)
                            {{ $displayOrder->created_at->format('M d, Y') }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div>
                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Method</span>
                    <span class="text-xs font-black text-slate-900 uppercase">
                        @if($displayOrder)
                            {{ str_replace('_', ' ', $displayOrder->payment_method) }}
                        @else
                            REWARD
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-8 sm:p-10">
        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Manifest Items</h4>
        <div class="overflow-x-auto rounded-2xl border border-slate-100 shadow-sm">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Product Description</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center whitespace-nowrap">Price / Rate</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center whitespace-nowrap">@if($displayOrder && $displayOrder->customer_type === 'dealer') Units / Pcs @else Pieces @endif</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center whitespace-nowrap">GST / Tax</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right whitespace-nowrap">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php 
                        $dispatchedSubtotal = 0; 
                        $dispatchedDiscountTotal = 0; 
                        $dispatchedTaxTotal = 0; 
                        $dispatchedTotalPieces = 0;
                        
                        $itemsToDisplay = [];
                        
                        if (isset($invoice)) {
                            $itemsToDisplay = $invoice->items;
                        } elseif ($displayOrder) {
                            // Filter items that have been dispatched
                            $itemsToDisplay = $displayOrder->items->filter(function($item) {
                                return $item->dispatched_quantity > 0;
                            });
                        }
                    @endphp
                    
                    @foreach($itemsToDisplay as $item)
                        @php
                            // Handle both InvoiceItem and OrderItem
                            if (isset($invoice)) {
                                $orderItem = $item->orderItem;
                                $isDealer = $displayOrder && $displayOrder->customer_type === 'dealer';
                                
                                if ($isDealer && $orderItem && $orderItem->per_unit_pieces > 0) {
                                    $displayQuantity = round($item->quantity * $orderItem->per_unit_pieces);
                                    $unitPrice = (!empty($orderItem->piece_price) && $orderItem->piece_price > 0) ? $orderItem->piece_price : ($orderItem->original_price ? ($orderItem->original_price / $orderItem->per_unit_pieces) : ($item->unit_price / $orderItem->per_unit_pieces));
                                    $unitDiscount = ($displayQuantity > 0) ? ($item->discount_amount / $displayQuantity) : 0;
                                } else {
                                    $displayQuantity = round($item->quantity, 2);
                                    
                                    $fallbackPrice = $product ? ($product->per_quantity_pieces > 1 ? ($product->price / $product->per_quantity_pieces) : $product->price) : $item->unit_price;
                                    $unitPrice = $orderItem ? ($orderItem->original_price ?? $fallbackPrice) : $item->unit_price;
                                    
                                    if ($orderItem && $orderItem->per_unit_pieces == 1 && $product && $product->per_quantity_pieces > 1) {
                                        if ($unitPrice >= $orderItem->price * $product->per_quantity_pieces * 0.9) {
                                            $unitPrice = $unitPrice / $product->per_quantity_pieces;
                                        }
                                    }
                                    
                                    $unitDiscount = ($displayQuantity > 0) ? ($item->discount_amount / $displayQuantity) : 0;
                                }
                                $lineTotal = $item->total; // This includes tax in InvoiceItem? No, InvoiceItem total usually includes tax? 
                                // Migration: unit_price, tax_amount, discount_amount, total.
                                // We need to verify how total is calculated in CreateInvoice.
                                // In CreateInvoice: total = (unit * qty) + (unitTax * qty) - discount.
                                // So $item->total is the final line total.
                                
                                // For display consistency with existing table:
                                $lineOriginalTotal = $unitPrice * $displayQuantity;
                                $lineFinalTotal = $lineOriginalTotal - $item->discount_amount;
                                
                                if ($orderItem) {
                                    $product = $orderItem->product;
                                    $productName = $orderItem->product_name;
                                    $productId = $orderItem->product_id;
                                } else {
                                    $rewardClaim = $invoice->rewardClaim;
                                    $product = ($rewardClaim && $rewardClaim->reward) ? $rewardClaim->reward->product : null;
                                    $productName = ($rewardClaim && $rewardClaim->reward) ? $rewardClaim->reward->name : 'Reward Item';
                                    $productId = $product ? $product->id : 'N/A';
                                }
                             } else {
                                $orderItem = $item;
                                $product = $orderItem->product;
                                $productName = $orderItem->product_name;
                                $productId = $orderItem->product_id;
                                $isDealer = $displayOrder && $displayOrder->customer_type === 'dealer';

                                if ($isDealer) {
                                    $displayQuantity = round($item->dispatched_quantity * $item->per_unit_pieces);
                                    $unitPrice = (!empty($item->piece_price) && $item->piece_price > 0) ? $item->piece_price : ($item->per_unit_pieces > 0 ? (($item->original_price ?? ($product ? $product->price : $item->price)) / $item->per_unit_pieces) : $item->price);
                                    $unitDiscount = ($item->total_pieces > 0) ? (($item->discount_amount ?? 0) / $item->total_pieces) : 0;
                                } else {
                                    $displayQuantity = round($item->dispatched_quantity, 2);
                                    
                                    // For B2C, the price should be piece price
                                    $fallbackPrice = $product ? ($product->per_quantity_pieces > 1 ? ($product->price / $product->per_quantity_pieces) : $product->price) : $item->price;
                                    $unitPrice = $item->original_price ?? $fallbackPrice;
                                    
                                    // Fix old B2C orders where original_price was incorrectly stored as the full unit price
                                    if ($item->per_unit_pieces == 1 && $product && $product->per_quantity_pieces > 1) {
                                        if ($unitPrice >= $item->price * $product->per_quantity_pieces * 0.9) {
                                            $unitPrice = $unitPrice / $product->per_quantity_pieces;
                                        }
                                    }
                                    
                                    $unitDiscount = 0; // B2C orders do not have discounts
                                }

                                $lineOriginalTotal = $unitPrice * $displayQuantity;
                                $lineDiscountTotal = $unitDiscount * $displayQuantity;
                                $lineFinalTotal = $lineOriginalTotal - $lineDiscountTotal;

                                if ($item->tax > 0 && $item->quantity > 0) {
                                    $lineGst = ($item->tax / $item->quantity) * $item->dispatched_quantity;
                                    $gstRate = ($item->price > 0 && $item->quantity > 0) ? ($item->tax / ($item->price * $item->quantity)) * 100 : 0;
                                } else {
                                    $gstRate = $product ? $product->gst_percentage : ($displayOrder && $displayOrder->tax > 0 ? ($displayOrder->tax / $displayOrder->subtotal * 100) : 0);
                                    $lineGst = $lineFinalTotal * $gstRate / 100;
                                }

                                $lineTotal = $lineFinalTotal + $lineGst;
                            }
                            
                            if ($displayOrder && $displayOrder->customer_type === 'dealer') {
                                $dispatchedTotalPieces += round($displayQuantity); // already in pieces
                            } else {
                                $dispatchedTotalPieces += round($displayQuantity); // B2C orders are already piece-wise
                            }
                            
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
                                        <span class="text-[10px] text-slate-400 font-mono mt-1">PID-{{ $productId ?? 'N/A' }}</span>
                                        
                                        @if($orderItem && ($orderItem->size || $orderItem->thickness || $orderItem->color))
                                            <div class="flex flex-wrap gap-1 mt-1.5">
                                                @if($orderItem->size)
                                                    <span class="text-[8px] font-black bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 uppercase">Size: {{ $orderItem->size }}</span>
                                                @endif
                                                @if($orderItem->thickness)
                                                    <span class="text-[8px] font-black bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 uppercase">Thk: {{ $orderItem->thickness }}</span>
                                                @endif
                                                @if($orderItem->color)
                                                    <span class="text-[8px] font-black bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 uppercase">Color: {{ $orderItem->color }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($displayOrder && $displayOrder->customer_type === 'dealer')
                                    <div class="text-sm font-bold text-slate-700 whitespace-nowrap italic">₹{{ number_format($unitPrice, 2) }} / piece</div>
                                    @if($unitDiscount > 0)
                                        <div class="text-[10px] font-black text-emerald-600 uppercase tracking-tighter mt-1">
                                            B2B Discount: -₹{{ number_format($unitDiscount, 2) }}/pc
                                        </div>
                                    @endif
                                @else
                                    <div class="text-sm font-bold text-slate-700 whitespace-nowrap italic">₹{{ number_format($unitPrice, 2) }} / piece</div>
                                    @if($unitDiscount > 0 && $displayOrder && $displayOrder->customer_type === 'dealer')
                                        <div class="text-[10px] font-black text-emerald-600 uppercase tracking-tighter mt-1">
                                            Saved ₹{{ number_format($unitDiscount, 2) }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @if($displayOrder && $displayOrder->customer_type === 'dealer')
                                    <div class="text-xs font-black text-slate-900">{{ round($displayQuantity) }} Pieces</div>
                                @else
                                    <div class="text-xs font-black text-slate-900">{{ $displayQuantity }} Pieces</div>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-center">
                                @php
                                    $itemGst = 0;
                                    $displayGstRate = $gstRate ?? 0;
                                    
                                    if (isset($invoice)) {
                                        $itemGst = $item->tax_amount;
                                        if ($itemGst <= 0 && $orderItem && $orderItem->product && $orderItem->product->gst_percentage > 0) {
                                            $itemGst = ($item->unit_price * $item->quantity) * $orderItem->product->gst_percentage / 100;
                                            $displayGstRate = $orderItem->product->gst_percentage;
                                        }
                                    } else {
                                        $itemGst = $lineGst;
                                    }
                                @endphp
                                <span class="text-xs font-bold text-slate-500 italic">₹{{ number_format($itemGst, 2) }}</span>
                                <div class="text-[8px] text-slate-400 uppercase font-black tracking-tighter mt-1">
                                    ({{ number_format($displayGstRate, 1) }}%)
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right whitespace-nowrap">
                                <div class="text-sm font-black text-slate-900 italic">₹{{ number_format($lineTotal, 2) }}</div>
                                @if($displayOrder && $displayOrder->customer_type === 'dealer')
                                <div class="text-[9px] font-bold text-slate-400 mt-1 uppercase">
                                    (₹{{ number_format($lineFinalTotal, 2) }} + ₹{{ number_format($itemGst, 2) }})
                                </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    
                    @if($itemsToDisplay->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic text-sm">
                                No items found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="px-8 sm:p-10 bg-slate-50/50 border-t border-slate-100 flex flex-col items-end">
        <div class="w-full sm:w-80 space-y-4 py-4">
            
            @php
                if (isset($invoice)) {
                    $finalSubtotal = $invoice->subtotal;
                    $finalDiscount = $invoice->discount_amount;
                    $finalTax = $invoice->tax;
                    $finalShipping = $invoice->shipping;
                    $finalOtherCharges = $invoice->other_charges ?? 0;
                    $finalTotal = $invoice->total;
                } else {
                     // Recalculate for cumulative view
                    $finalSubtotal = $itemsToDisplay->sum(function($item) {
                        return ($item->original_price ?? ($item->product->price ?? $item->price)) * $item->dispatched_quantity;
                    });
                    $finalDiscount = $itemsToDisplay->sum(function($item) use ($displayOrder) {
                        if ($displayOrder && $displayOrder->customer_type !== 'dealer') return 0;
                        return ($item->discount_amount ?? 0) * $item->dispatched_quantity;
                    });
                    $finalTax = $itemsToDisplay->sum(function($item) use ($displayOrder) {
                        if (isset($item->tax_amount)) {
                            return $item->tax_amount;
                        }
                        
                        if ($item->tax > 0 && $item->quantity > 0) {
                            return ($item->tax / $item->quantity) * $item->dispatched_quantity;
                        }
                        
                        $p = $item->product;
                        $fallbackPrice = $p ? ($displayOrder->customer_type === 'dealer' ? $p->price : ($p->per_quantity_pieces > 1 ? $p->price / $p->per_quantity_pieces : $p->price)) : $item->price;
                        $origPrice = $item->original_price ?? $fallbackPrice;
                        
                        if ($displayOrder->customer_type !== 'dealer' && $item->per_unit_pieces == 1 && $p && $p->per_quantity_pieces > 1) {
                            if ($origPrice >= $item->price * $p->per_quantity_pieces * 0.9) {
                                $origPrice = $origPrice / $p->per_quantity_pieces;
                            }
                        }
                        
                        $lineOriginalTotal = $origPrice * $item->dispatched_quantity;
                        $lineDiscountTotal = ($displayOrder && $displayOrder->customer_type !== 'dealer') ? 0 : ($item->discount_amount ?? 0) * $item->dispatched_quantity;
                        $lineFinalTotal = $lineOriginalTotal - $lineDiscountTotal;
                        $gstRate = $p ? $p->gst_percentage : ($displayOrder && $displayOrder->tax > 0 ? ($displayOrder->tax / $displayOrder->subtotal * 100) : 0);
                        return $lineFinalTotal * $gstRate / 100;
                    });
                    $finalShipping = $displayOrder ? $displayOrder->shipping : 0;
                    $finalOtherCharges = $displayOrder ? $displayOrder->other_charges : 0;
                    $finalBTDiscount = $displayOrder ? $displayOrder->bank_transfer_discount_amount : 0;
                    
                    $finalTotal = ($finalSubtotal - $finalDiscount) + $finalTax + $finalShipping + $finalOtherCharges - $finalBTDiscount;
                }
                
                if (isset($invoice)) {
                    $finalBTDiscount = $invoice->bank_transfer_discount_amount;
                }
            @endphp
        
            <div class="flex justify-between items-center text-sm">
                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Total Pieces</span>
                <span class="font-bold text-slate-700 italic">{{ $dispatchedTotalPieces }} pcs</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Subtotal (Gross)</span>
                <span class="font-bold text-slate-700 italic">₹{{ number_format($finalSubtotal, 2) }}</span>
            </div>

            @if($finalDiscount > 0 && $displayOrder && $displayOrder->customer_type === 'dealer')
            <div class="flex justify-between items-center text-sm">
                <span class="font-black text-emerald-600 uppercase tracking-widest text-[10px]">B2B Dealer Discount</span>
                <span class="font-black text-emerald-600 italic">-₹{{ number_format($finalDiscount, 2) }}</span>
            </div>
            @endif

            @if(isset($finalBTDiscount) && $finalBTDiscount > 0)
            <div class="flex justify-between items-center text-sm">
                <span class="font-black text-emerald-600 uppercase tracking-widest text-[10px]">Bank Transfer Discount</span>
                <span class="font-black text-emerald-600 italic">-₹{{ number_format($finalBTDiscount, 2) }}</span>
            </div>
            @endif

            <div class="flex justify-between items-center text-sm">
                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">GST Aggregation</span>
                <span class="font-bold text-slate-700 italic">+₹{{ number_format($finalTax, 2) }}</span>
            </div>
            
            <div class="flex justify-between items-center text-sm">
                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Logistics Fee</span>
                <span class="font-bold text-slate-700 italic">+₹{{ number_format($finalShipping, 2) }}</span>
            </div>

            @if(isset($finalOtherCharges) && $finalOtherCharges > 0)
            <div class="flex justify-between items-center text-sm">
                <span class="font-bold text-slate-400 uppercase tracking-widest text-[10px]">Other Charges (Dear)</span>
                <span class="font-bold text-slate-700 italic">+₹{{ number_format($finalOtherCharges, 2) }}</span>
            </div>
            @endif
            
            <div class="pt-6 mt-4 border-t-2 border-slate-200 border-dashed flex justify-between items-end">
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Total Payable</span>
                    <span class="text-xs font-bold text-indigo-500 uppercase tracking-tighter">Verified Amount</span>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-black text-rose-800 tracking-tighter italic">
                        ₹{{ number_format($finalTotal, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    
    @if($displayOrder)
    <div class="bg-white border-t border-slate-200">
        @include('components.dispatch-info', ['order' => $displayOrder])
    </div>
    @endif
</div>