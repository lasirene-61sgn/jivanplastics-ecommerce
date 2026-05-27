@extends('frontend.b2b.layouts.app')

@section('title', 'Return Requests - E-Commerce Store')
@section('header', 'Return Requests')

@section('content')
<div class="b2b-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2 class="b2b-section-title">Return Requests for Order #{{ $order->order_number }}</h2>
        <a href="{{ route('b2b.orders.show', $order) }}" class="btn btn-outline" style="padding: 0.5rem 1rem; border: 2px solid #8B0000; color: #8B0000;">Back to Order</a>
    </div>
    
    @if($order->returnRequests->count() > 0)
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Your Return Requests</h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #4b5563;">Product</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Type</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Quantity</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Reason</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Status</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Evidence</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #4b5563;">Requested On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->returnRequests as $request)
                            <tr style="border-bottom: 1px solid #e5e7eb;">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center;">
                                        @if($request->orderItem->product && $request->orderItem->product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $request->orderItem->product->images->first()->image_path) }}" 
                                                 alt="{{ $request->orderItem->product_name }}" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 0.25rem; margin-right: 1rem;">
                                        @else
                                            <div style="width: 60px; height: 60px; background-color: #e5e7eb; border-radius: 0.25rem; margin-right: 1rem; display: flex; align-items: center; justify-content: center;">
                                                <span style="font-size: 0.75rem;">No Image</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h5 style="font-size: 1rem; font-weight: 500; color: #1f2937; margin: 0;">
                                                {{ $request->orderItem->product_name }}
                                            </h5>
                                            <p style="color: #6b7280; margin: 0.25rem 0; font-size: 0.875rem;">
                                                SKU: {{ $request->orderItem->product_sku }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #6b7280;">
                                    {{ ucfirst($request->type) }}
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #6b7280;">
                                    {{ $request->quantity }}
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #6b7280;">
                                    {{ $request->reason }}
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($request->status === 'pending')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #fef3c7; color: #92400e;">
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #d1fae5; color: #065f46;">
                                            Approved
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #fee2e2; color: #991b1b;">
                                            Rejected
                                        </span>
                                    @elseif($request->status === 'processing')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #dbeafe; color: #1e40af;">
                                            Processing
                                        </span>
                                    @elseif($request->status === 'completed')
                                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 500; background-color: #d1fae5; color: #065f46;">
                                            Completed
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        @if($request->damage_proof_image)
                                            <a href="{{ asset('storage/' . $request->damage_proof_image) }}" target="_blank" title="Damage Proof" style="color: #dc2626;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </a>
                                        @endif
                                        @if($request->another_image)
                                            <a href="{{ asset('storage/' . $request->another_image) }}" target="_blank" title="Additional Image" style="color: #6b7280;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            </a>
                                        @endif
                                        @if($request->dispatch_proof_image)
                                            <a href="{{ asset('storage/' . $request->dispatch_proof_image) }}" target="_blank" title="Dispatch Proof" style="color: #059669;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </a>
                                        @endif
                                        @if($request->invoice_id)
                                            <a href="{{ route('b2b.orders.invoice', [$request->order, $request->invoice]) }}" target="_blank" title="Return Invoice" style="color: #2563eb;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                        @elseif($request->invoice_proof_image)
                                            <a href="{{ asset('storage/' . $request->invoice_proof_image) }}" target="_blank" title="Return Invoice (Manual)" style="color: #2563eb;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </a>
                                        @endif
                                        @if($request->returnNote)
                                            <a href="{{ route('b2b.orders.return-note', [$request->order, $request->returnNote]) }}" title="{{ ucfirst($request->returnNote->type) }} Note" style="color: #10b981;">
                                                <svg style="width: 1.25rem; h-1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align: center; color: #6b7280;">
                                    {{ $request->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 3rem; text-align: center; margin-bottom: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">No Return Requests Found</h3>
            <p style="color: #6b7280; margin-bottom: 1.5rem;">You haven't submitted any return requests for this order yet.</p>
            <a href="{{ route('b2b.orders.show', $order) }}" class="btn btn-primary" style="padding: 0.75rem 1.5rem; background-color: #8B0000; color: white; text-decoration: none; border-radius: 0.375rem;">Back to Order</a>
        </div>
    @endif
</div>

<style>
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

.btn-primary {
    background-color: #8B0000;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    text-decoration: none;
    font-weight: 500;
    border: none;
}

.btn-primary:hover {
    background-color: #6b0000;
}
</style>
@endsection