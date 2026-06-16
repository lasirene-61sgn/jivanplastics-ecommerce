<div class="order-display-component" style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <span style="font-weight: bold; color: #1f2937;">Order #{{ $order->order_number }}</span>
            <span style="margin-left: 1rem; color: #6b7280;">{{ $order->created_at->format('M d, Y') }}</span>
        </div>
        <span class="order-status {{ $order->status }}" style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500;">
            @if($order->status === 'under_process')
                Under Process
            @else
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            @endif
        </span>
    </div>
    
    @if($order->tentative_dispatch_date)
    <div style="margin-bottom: 1rem; background-color: #f0fdfa; border: 1px solid #ccfbf1; padding: 0.75rem 1rem; border-radius: 0.375rem; display: flex; align-items: center; gap: 0.5rem;">
        <svg style="width: 1.25rem; height: 1.25rem; color: #0f766e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <span style="color: #0f766e; font-weight: 500; font-size: 0.875rem;">Tentative Dispatch:</span>
        <span style="color: #115e59; font-weight: 700; font-size: 0.875rem;">{{ \Carbon\Carbon::parse($order->tentative_dispatch_date)->format('M d, Y') }}</span>
    </div>
    @endif
    
    <!-- Order Items Preview -->
    <div style="margin-bottom: 1rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; padding: 1rem;">
        <h4 style="font-size: 1rem; font-weight: 600; color: #1f2937; margin-bottom: 0.75rem;">Order Items ({{ $order->items->count() }} items)</h4>
        @if($order->items->count() > 0)
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                @foreach($order->items as $item)
                    <div style="display: flex; align-items: center; width: 100%;">
                        @if($item->product && $item->product->images && $item->product->images->count() > 0)
                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                 alt="{{ $item->product_name }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.25rem; margin-right: 0.75rem;">
                        @else
                            <div style="width: 50px; height: 50px; background-color: #e5e7eb; border-radius: 0.25rem; margin-right: 0.75rem; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 0.625rem;">No Image</span>
                            </div>
                        @endif
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start;">
                                <div>
                                    <h5 style="font-size: 0.875rem; font-weight: 500; color: #1f2937; margin: 0;">
                                        {{ $item->product_name }}
                                    </h5>
                                    <p style="margin: 0.125rem 0; color: #6b7280; font-size: 0.8125rem;">
                                        @if($order->customer_type === 'dealer')
                                            {{ $item->total_pieces }} Pieces
                                        @else
                                            Qty: {{ $item->quantity }} units ({{ $item->total_pieces }} pcs)
                                        @endif
                                        @if($item->dispatched_quantity > 0 && $item->rejected_quantity > 0 && $item->pending_quantity == 0)
                                            <span style="display: inline-block; margin-left: 8px; font-size: 0.65rem; background-color: #f3e8ff; color: #7e22ce; padding: 2px 6px; border-radius: 4px; border: 1px solid #e9d5ff; font-weight: 700; text-transform: uppercase;">Completed with Shortage</span>
                                        @endif
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <p style="margin: 0; font-weight: 700; color: #111827;">₹{{ number_format($item->total + $item->tax, 2) }}</p>
                                    <p style="margin: 0; font-size: 0.75rem; color: #6b7280;">(Incl. ₹{{ number_format($item->tax, 2) }} GST)</p>
                                </div>
                            </div>
                            @if($order->customer_type === 'dealer')
                            <p style="margin: 0.125rem 0; color: #6b7280; font-size: 0.8125rem; font-weight: bold;">
                                Rate: ₹{{ number_format($item->piece_price, 2) }} / piece
                            </p>
                            @else
                            <p style="margin: 0.125rem 0; color: #6b7280; font-size: 0.75rem; font-style: italic;">
                                1 unit = {{ $item->per_unit_pieces }} pcs @ ₹{{ number_format($item->piece_price, 2) }} / pc
                            </p>
                            <p style="margin: 0.125rem 0; color: #6b7280; font-size: 0.8125rem; font-weight: bold;">
                                Rate: ₹{{ number_format($item->price, 2) }} / unit
                            </p>
                            @endif
                            @if($item->size || $item->thickness || $item->color)
                                <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin: 0.25rem 0;">
                                    @if($item->size)
                                        <span style="font-size: 0.65rem; background-color: #f1f5f9; color: #475569; padding: 1px 4px; border-radius: 3px; border: 1px solid #e2e8f0;">Size: {{ $item->size }}</span>
                                    @endif
                                    @if($item->thickness)
                                        <span style="font-size: 0.65rem; background-color: #f1f5f9; color: #475569; padding: 1px 4px; border-radius: 3px; border: 1px solid #e2e8f0;">Thk: {{ $item->thickness }}</span>
                                    @endif
                                    @if($item->color)
                                        <span style="font-size: 0.65rem; background-color: #f1f5f9; color: #475569; padding: 1px 4px; border-radius: 3px; border: 1px solid #e2e8f0;">Color: {{ $item->color }}</span>
                                    @endif
                                </div>
                            @endif
                            <!-- Show status for each item -->
                            <div style="margin: 0.25rem 0; color: #6b7280; font-size: 0.75rem; background: #f8fafc; padding: 0.5rem; border-radius: 0.5rem; border: 1px solid #f1f5f9;">
                                @if($order->customer_type === 'dealer')
                                @php
                                    $dispatchedPieces = $item->dispatched_quantity * $item->per_unit_pieces;
                                    $pendingPieces = max(0, $item->total_pieces - $dispatchedPieces - $item->rejected_pieces);
                                @endphp
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                    <span style="font-weight: 700;">Dispatched:</span>
                                    <span style="color: #8B0000; font-weight: 800;">{{ $dispatchedPieces }}/{{ $item->total_pieces }} Pieces</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                    <span style="font-weight: 700;">Pending:</span>
                                    <span style="color: #d97706; font-weight: 800;">{{ $pendingPieces }} Pieces</span>
                                </div>
                                @if($item->rejected_pieces > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                    <span style="font-weight: 700; color: #dc3545;">Rejected:</span>
                                    <span style="color: #dc3545; font-weight: 800;">{{ $item->rejected_pieces }} Pieces</span>
                                </div>
                                @if($item->rejection_reason)
                                <div style="font-size: 0.7rem; color: #dc3545; font-style: italic; text-align: right; margin-bottom: 2px;">
                                    Reason: {{ $item->rejection_reason }}
                                </div>
                                @endif
                                @endif
                                @else
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                    <span style="font-weight: 700;">Manufacturing:</span>
                                    <span style="color: #28a745; font-weight: 800;">{{ $item->manufactured_pieces }}/{{ $item->total_pieces }} Pieces</span>
                                </div>
                                @if($item->rejected_pieces > 0)
                                <div style="display: flex; justify-content: space-between; margin-bottom: 2px;">
                                    <span style="font-weight: 700; color: #dc3545;">Rejected:</span>
                                    <span style="color: #dc3545; font-weight: 800;" title="{{ $item->rejection_reason }}">{{ $item->rejected_pieces }} Pieces</span>
                                </div>
                                @if($item->rejection_reason)
                                    <div style="margin-bottom: 4px; font-size: 0.7rem; color: #dc3545; font-style: italic; text-align: right;">
                                        Reason: {{ $item->rejection_reason }}
                                    </div>
                                @endif
                                @endif
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="font-weight: 700;">Admin Dispatch:</span>
                                    <span style="color: #8B0000; font-weight: 800;">{{ $item->dispatched_quantity }}/{{ $item->quantity }} Units</span>
                                </div>
                                @endif
                            
                            <!-- @if(request()->routeIs('b2b.*') && $item->dispatched_quantity > 0)
                                <div style="margin-top: 0.5rem;">
                                    @php
                                        // Check for active return requests (pending, approved, processing)
                                        $activeReturnRequest = $item->returnRequests->whereIn('status', ['pending', 'approved', 'processing'])->first();
                                    @endphp
                                    
                                    @if($activeReturnRequest)
                                        <span style="display: inline-block; font-size: 0.75rem; padding: 0.25rem 0.5rem; background-color: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; border-radius: 0.25rem;">
                                            Return {{ ucfirst($activeReturnRequest->status) }}
                                        </span>
                                    @else
                                        <a href="{{ route('b2b.orders.return-request.form', ['order' => $order->id, 'orderItem' => $item->id]) }}" 
                                           style="display: inline-block; font-size: 0.75rem; padding: 0.25rem 0.5rem; background-color: white; color: #8B0000; border: 1px solid #8B0000; border-radius: 0.25rem; text-decoration: none; transition: all 0.2s;">
                                            Request Return
                                        </a>
                                    @endif
                                </div>
                            @endif -->

                            @if($order->customer_type !== 'dealer')
                            <div style="margin-top: 4px; border-top: 1px dashed #e2e8f0; padding-top: 4px; font-style: italic; font-size: 0.7rem;">
                                1 Unit = {{ $item->per_unit_pieces }} pieces | Total Units: {{ $item->quantity }}
                            </div>
                            @endif
                            </div>
                            <p style="margin: 0.125rem 0; font-weight: 600; color: #1f2937; font-size: 0.8125rem;">
                                ₹{{ number_format($item->total, 2) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Overall order dispatch status -->
            @if($order->has_pending_items)
                <div style="margin-top: 1rem; padding: 0.75rem; background-color: #fffbeb; border: 1px solid #fbbf24; border-radius: 0.375rem;">
                    <p style="margin: 0; color: #92400e; font-weight: 500;">
                        @if($order->customer_type === 'dealer')
                            This order has {{ $order->items->sum(function($i){ return $i->total_pieces - $i->dispatched_quantity * $i->per_unit_pieces - $i->rejected_pieces; }) }} pending pieces out of {{ $order->items->sum('total_pieces') }} total pieces.
                        @else
                            This order has {{ $order->total_pending_quantity }} pending items out of {{ $order->items->sum('quantity') }} total items.
                        @endif
                    </p>
                </div>
            @else
                <div style="margin-top: 1rem; padding: 0.75rem; background-color: #f0fdf4; border: 1px solid #34d399; border-radius: 0.375rem;">
                    <p style="margin: 0; color: #065f46; font-weight: 500;">
                        All items in this order have been dispatched.
                    </p>
                </div>
            @endif
        @else
            <p style="color: #6b7280; font-style: italic;">No items found in this order.</p>
        @endif
    </div>
    
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <p style="margin: 0.25rem 0; color: #374151;">
                <strong>Customer:</strong> {{ $order->customer->name }}
            </p>
            <p style="margin: 0.25rem 0; color: #374151;">
                @php
                    $rejectedRefund = 0;
                    foreach($order->items as $item) {
                        if($item->rejected_quantity > 0) {
                            $p = $item->product;
                            $unitPrice = $item->original_price ?? ($p->price ?? $item->price);
                            $lineOriginalTotal = $unitPrice * $item->rejected_quantity;
                            
                            $unitDiscount = ($item->discount_amount ?? 0) > 0 ? ($item->discount_amount / $item->quantity) : 0;
                            if ($item->quantity > 0) {
                                $unitDiscount = $item->discount_amount / $item->quantity;
                            }
                            $lineDiscountTotal = $unitDiscount * $item->rejected_quantity;
                            
                            $lineFinalTotal = $lineOriginalTotal - $lineDiscountTotal;
                            $gstRate = $p ? $p->gst_percentage : ($order->tax > 0 ? ($order->tax / $order->subtotal * 100) : 18);
                            $lineGst = $lineFinalTotal * $gstRate / 100;
                            
                            $rejectedRefund += ($lineFinalTotal + $lineGst);
                        }
                    }
                @endphp
                <strong>Total Amount:</strong> 
                @if($rejectedRefund > 0)
                    <span style="text-decoration: line-through; margin-right: 4px; font-size: 0.8em; color: #9ca3af;">₹{{ number_format($order->total, 2) }}</span>
                @endif
                ₹{{ number_format($order->total - $rejectedRefund, 2) }}
            </p>
        </div>
        <div>
            @if(isset($showViewButton) && $showViewButton)
                @if(request()->routeIs('b2b.orders'))
                    <a href="{{ route('b2b.orders.show', $order) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; margin-right: 0.5rem;">View Details</a>
                @elseif(request()->routeIs('b2c.orders'))
                    <a href="{{ route('b2c.orders.show', $order) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; margin-right: 0.5rem;">View Details</a>
                @else
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; margin-right: 0.5rem;">View Details</a>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.order-status.pending {
    background-color: #f0f0f0;
    color: #8B0000;
}

.order-status.accepted {
    background-color: #e0f2fe;
    color: #0369a1;
}

.order-status.under_process {
    background-color: #fef3c7;
    color: #d97706;
}

.order-status.rejected {
    background-color: #fee2e2;
    color: #b91c1c;
}

.order-status.processing {
    background-color: #f0f0f0;
    color: #8B0000;
}

.order-status.completed {
    background-color: #f0f0f0;
    color: #8B0000;
}

.order-status.cancelled {
    background-color: #f0f0f0;
    color: #A9A9A9;
}

.btn-outline {
    background-color: transparent;
    border: 2px solid #8B0000;
    color: #8B0000;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
}

.btn-outline:hover {
    background-color: #8B0000;
    color: white;
}
</style>