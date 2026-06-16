@extends('frontend.b2b.layouts.app')

@section('title', 'Order Details - E-Commerce Store')
@section('header', 'Order Details')

@section('content')
<div class="b2b-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="b2b-section-title">Order #{{ $order->order_number }}</h2>
        <!-- <div class="flex gap-2">
            <a href="{{ route('b2b.orders.return-requests', $order) }}" class="px-4 py-2 border-2 border-[#8B0000] text-[#8B0000] font-bold rounded hover:bg-[#8B0000] hover:text-white transition-colors">Return Requests</a>
            <a href="{{ route('b2b.orders') }}" class="px-4 py-2 border-2 border-slate-300 text-slate-600 font-bold rounded hover:bg-slate-100 transition-colors">Back</a>
        </div> -->
    </div>
    
    <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem; border: 4px solid #f8fafc;">
        <h3 style="font-size: 1.25rem; font-weight: 800; color: #1f2937; margin-bottom: 1.5rem; display: flex; align-items: center;">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            Production & Dispatch Tracker
        </h3>
        @include('components.order-display', ['order' => $order, 'showViewButton' => false])
    </div>

    <div style="margin-top: 3rem;">
        <h3 class="b2b-section-title">Your Invoices</h3>
        @if($order->invoices->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                @foreach($order->invoices as $invoice)
                    <a href="{{ route('b2b.orders.invoice', ['order' => $order->id, 'invoice' => $invoice->id]) }}" style="display: block; background: white; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: 900; color: #1f2937; font-size: 1.1rem;">{{ $invoice->invoice_number }}</span>
                            <span style="background-color: #e0e7ff; color: #4338ca; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">INVOICE</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #6b7280;">
                            <span>Date: {{ $invoice->created_at->format('M d, Y') }}</span>
                            <span style="font-weight: 700; color: #1f2937;">₹{{ number_format($invoice->total, 2) }}</span>
                        </div>
                         <div style="margin-top: 1rem; text-align: center; color: #4f46e5; font-weight: 700; font-size: 0.875rem;">
                            View Invoice Details &rarr;
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 0.75rem; padding: 2rem; text-align: center; color: #64748b; font-style: italic;">
                No invoices have been generated for this order yet.
            </div>
        @endif
    </div>

    <!-- <div style="margin-top: 3rem;">
        <h3 class="b2b-section-title">Credit & Debit Notes</h3>
        @php
            $returnNotes = \App\Models\ReturnNote::where('order_id', $order->id)->get();
        @endphp
        @if($returnNotes->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                @foreach($returnNotes as $note)
                    <a href="{{ route('b2b.orders.return-note', [$order, $note]) }}" style="display: block; background: white; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; position: relative; border-left: 4px solid {{ $note->type == 'credit' ? '#10b981' : '#f59e0b' }}; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='#4f46e5'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';" onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none';">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: 900; color: #1f2937; font-size: 1.1rem;">{{ $note->note_number }}</span>
                            <span style="background-color: {{ $note->type == 'credit' ? '#d1fae5' : '#fef3c7' }}; color: {{ $note->type == 'credit' ? '#065f46' : '#92400e' }}; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">{{ strtoupper($note->type) }} NOTE</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #6b7280;">
                            <span>Issued: {{ $note->created_at->format('M d, Y') }}</span>
                            <span style="font-weight: 700; color: #1f2937;">₹{{ number_format($note->total, 2) }}</span>
                        </div>
                        <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #64748b;">Ref Return Req #{{ $note->return_request_id }}</p>
                        <div style="margin-top: 0.5rem; text-align: right; color: #4f46e5; font-weight: 700; font-size: 0.75rem;">View Details &rarr;</div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 0.75rem; padding: 2rem; text-align: center; color: #64748b; font-style: italic;">
                No credit or debit notes have been issued for this order.
            </div>
        @endif
    </div> -->

    <!-- <div style="margin-top: 3rem;">
        <h3 class="b2b-section-title">Return & Replacement Invoices</h3>
        @php
            $returnInvoices = $order->returnRequests->whereNotNull('invoice_proof_image');
        @endphp
        @if($returnInvoices->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem;">
                @foreach($returnInvoices as $request)
                    <a href="{{ $request->invoice_id ? route('b2b.orders.invoice', [$order, $request->invoice]) : asset('storage/' . $request->invoice_proof_image) }}" target="_blank" style="display: block; background: #fff5f5; padding: 1.5rem; border-radius: 0.75rem; border: 1px solid #fee2e2; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='#ef4444'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)';" onmouseout="this.style.borderColor='#fee2e2'; this.style.boxShadow='none';">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <span style="font-weight: 900; color: #991b1b; font-size: 1.1rem;">Return Invoice</span>
                            <span style="background-color: #fee2e2; color: #b91c1c; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;">{{ $request->invoice_id ? 'AUTOMATED' : 'REPLACEMENT' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #7f1d1d;">
                            <span>Request Date: {{ $request->created_at->format('M d, Y') }}</span>
                            <span style="font-weight: 700;">Qty: {{ $request->quantity }}</span>
                        </div>
                        <div style="margin-top: 0.5rem; font-size: 0.75rem; font-weight: 600; color: #991b1b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $request->orderItem->product_name }}
                        </div>
                        <div style="margin-top: 1rem; text-align: center; color: #b91c1c; font-weight: 700; font-size: 0.875rem;">
                            View {{ $request->invoice_id ? 'Detailed' : 'Return' }} Invoice &rarr;
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 0.75rem; padding: 2rem; text-align: center; color: #64748b; font-style: italic;">
                No return/replacement invoices have been uploaded for this order.
            </div>
        @endif
    </div> -->

    <div style="margin-top: 3rem;">
        @include('components.rejected-items-invoice', ['order' => $order])
    </div>

    @if($order->items->sum('dispatched_quantity') > 0)
    <h3 class="b2b-section-title" style="margin-top: 3rem;">Dispatch Invoice Summary</h3>
    @include('components.invoice-display', ['order' => $order])
    @endif
</div>

<style>
.order-status {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.order-status.pending {
    background-color: #f0f0f0;
    color: #8B0000;
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
    background-color: #f3f4f6;
    color: #374151;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
    border: 1px solid #e5e7eb;
}

.btn-outline:hover {
    background-color: #e5e7eb;
}
</style>
@endsection