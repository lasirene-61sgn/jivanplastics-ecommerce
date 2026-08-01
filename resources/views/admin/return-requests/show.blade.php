@extends('layouts.admin')

@section('title', 'Return Request Details')

@section('header', 'Request Inspection')

@section('content')
<div class="max-w-6xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
            </div>
            @php
                $lineNum = 1;
                if ($returnRequest->order && $returnRequest->order_item_id) {
                    $lineNum = $returnRequest->order->items->search(function($item) use ($returnRequest) {
                        return $item->id === $returnRequest->order_item_id;
                    });
                    $lineNum = $lineNum !== false ? $lineNum + 1 : 1;
                }
            @endphp
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">ID: RR{{ str_pad($returnRequest->id, 3, '0', STR_PAD_LEFT) }}/{{ $lineNum }}</h2>
                @if($returnRequest->request_number)
                    <p class="text-xs text-rose-500 font-bold tracking-widest uppercase mt-1">{{ $returnRequest->request_number }} (Mfg Direct)</p>
                @endif
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase mt-1">Post-Purchase Ticket</p>
            </div>
        </div>
        <a href="{{ route('admin.return-requests.index', request()->only(['page', 'search', 'status', 'per_page'])) }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Back to Queue
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Request Intelligence</h3>
                </div>
                <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Customer Name</p>
                            <p class="text-lg font-black text-slate-900">{{ $returnRequest->customer->name }}</p>
                        </div>
                        <div>
                            <!-- <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Order Link</p>
                            @if($returnRequest->order)
                                <a href="{{ route('admin.orders.show', $returnRequest->order) }}" class="text-sm font-black text-indigo-600 underline">
                                    Order #{{ $returnRequest->order->order_number }}
                                </a>
                            @else
                                <span class="text-sm font-black text-slate-400 italic">No Order (Direct)</span>
                            @endif -->
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Request Category</p>
                            <p class="text-sm font-black text-slate-700 uppercase italic">{{ $returnRequest->type }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pieces</p>
                            <div class="flex gap-2">
                                <!-- <span class="px-3 py-1 bg-slate-900 text-white rounded-lg font-black text-xs">{{ $returnRequest->quantity }} Units</span> -->
                                @if($returnRequest->pieces > 0)
                                    <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg font-black text-xs">{{ $returnRequest->pieces }} Pieces</span>
                                @endif
                            </div>
                        </div>
                        @if($returnRequest->returnNote)
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Return Note</p>
                            <a href="{{ route('admin.return-requests.return-note', [$returnRequest, $returnRequest->returnNote]) }}" class="px-3 py-1 bg-emerald-600 text-white rounded-lg font-black text-xs hover:bg-emerald-700 transition-colors">
                                {{ ucfirst($returnRequest->returnNote->type) }} Note: {{ $returnRequest->returnNote->note_number }}
                            </a>
                        </div>
                        @endif
                    </div>
                    @if($returnRequest->order && $returnRequest->order->manufacturingTeam)
                    <div class="sm:col-span-2 pt-6 border-t border-slate-50">
                        <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-3">Manufacturing Information</p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Assigned Unit</p>
                                <p class="text-sm font-black text-slate-900">{{ $returnRequest->order->manufacturingTeam->factory_name }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Allocated Date</p>
                                <p class="text-sm font-black text-slate-900">{{ $returnRequest->created_at->format('d M, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Return Completed Date</p>
                                <p class="text-sm font-black text-slate-900">{{ $returnRequest->resolved_at ? $returnRequest->resolved_at->format('d M, Y') : 'Pending' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="sm:col-span-2 pt-6 border-t border-slate-50">
                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-2 italic">Stated Reason from Customer / Admin</p>
                        <p class="text-base text-slate-600 leading-relaxed font-medium">"{{ $returnRequest->reason }}"</p>
                    </div>
                </div>
            </div>

            <!-- <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Dealer Evidence</h3>
                </div>
                <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div>
                        <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-3 italic">Damage Proof Image</p>
                        @if($returnRequest->damage_proof_image)
                            <a href="{{ asset('storage/' . $returnRequest->damage_proof_image) }}" target="_blank" class="block w-full h-48 rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                <img src="{{ asset('storage/' . $returnRequest->damage_proof_image) }}" class="h-full w-full object-cover">
                            </a>
                        @else
                            <div class="w-full h-48 rounded-2xl border-2 border-dashed border-slate-100 flex items-center justify-center text-slate-300 text-[10px] font-black uppercase tracking-widest">
                                No Damage Proof
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 italic">Additional View</p>
                        @if($returnRequest->another_image)
                            <a href="{{ asset('storage/' . $returnRequest->another_image) }}" target="_blank" class="block w-full h-48 rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                <img src="{{ asset('storage/' . $returnRequest->another_image) }}" class="h-full w-full object-cover">
                            </a>
                        @else
                            <div class="w-full h-48 rounded-2xl border-2 border-dashed border-slate-100 flex items-center justify-center text-slate-300 text-[10px] font-black uppercase tracking-widest">
                                No Additional Image
                            </div>
                        @endif
                    </div>
                </div>
            </div> -->

            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Product Reference</h3>
                </div>
                <div class="p-8 flex flex-col md:flex-row gap-8 items-start">
                    @php
                        $productImage = null;
                        $productName = 'N/A';
                        $productSku = 'N/A';
                        $productPrice = null;
                        $productModel = null;
                        
                        if ($returnRequest->orderItem) {
                            $productModel = $returnRequest->orderItem->product;
                            if ($productModel && $productModel->images->count() > 0) {
                                $productImage = $productModel->images->first()->image_path;
                            }
                            $productName = $productModel ? $productModel->name : $returnRequest->orderItem->product_name;
                            $productSku = $returnRequest->orderItem->product_sku;
                            $productPrice = $returnRequest->orderItem->price;
                        } elseif ($returnRequest->product) {
                            $productModel = $returnRequest->product;
                            if ($productModel->images->count() > 0) {
                                $productImage = $productModel->images->first()->image_path;
                            }
                            $productName = $productModel->name;
                            $productSku = $productModel->sku ?? 'N/A';
                            $productPrice = $productModel->price;
                        }
                    @endphp
                    
                    @if($productImage)
                        <div class="w-full md:w-40 h-40 flex-shrink-0 rounded-2xl border border-slate-200 overflow-hidden shadow-inner bg-slate-50">
                            <img src="{{ asset('storage/' . $productImage) }}" class="h-full w-full object-cover">
                        </div>
                    @endif
                    <div class="flex-1 space-y-4">
                        <h4 class="text-xl font-black text-slate-900 leading-tight">
                            {{ $productName }}
                        </h4>
                        @if($productModel)
                            <div class="flex flex-wrap gap-2 text-xs font-bold text-slate-600 uppercase tracking-widest mt-2">
                                @if($productModel->size)
                                    <span class="px-2 py-1 bg-slate-100 rounded-lg">Size: {{ $productModel->size }}</span>
                                @endif
                                @if($productModel->thickness)
                                    <span class="px-2 py-1 bg-slate-100 rounded-lg">Thick: {{ $productModel->thickness }}</span>
                                @endif
                                @if($productModel->color)
                                    <span class="px-2 py-1 bg-slate-100 rounded-lg">Color: {{ $productModel->color }}</span>
                                @endif
                            </div>
                        @endif
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global SKU</p>
                                <p class="text-sm font-mono font-bold text-slate-700 uppercase tracking-tighter">{{ $productSku }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Invoiced Rate</p>
                                @if($productPrice !== null)
                                    <p class="text-sm font-bold text-slate-900 italic">{{ number_format($productPrice, 2) }}</p>
                                @else
                                    <p class="text-sm font-bold text-slate-500 italic">N/A</p>
                                @endif
                            </div>
                        </div>
                        
                        @if($returnRequest->orderItem)
                        <div class="pt-4 flex gap-4 text-center">
                            <div class="px-4 py-2 bg-slate-100 rounded-xl">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Ordered</p>
                                <p class="text-sm font-black text-slate-900">{{ $returnRequest->orderItem->quantity }}</p>
                            </div>
                            <div class="px-4 py-2 bg-slate-100 rounded-xl">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Dispatched</p>
                                <p class="text-sm font-black text-slate-900">{{ $returnRequest->orderItem->dispatched_quantity }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl">
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-6 italic">Work Status</h3>
                <div class="flex flex-col items-center justify-center py-4">
                    @php
                        $manColor = match($returnRequest->status) {
                            'approved', 'completed' => 'text-green-400',
                            'rejected' => 'text-rose-400',
                            'pending' => 'text-amber-400',
                            default => 'text-blue-400'
                        };
                    @endphp
                    <span class="text-3xl font-black uppercase tracking-tighter {{ $manColor }}">{{ $returnRequest->status === 'completed' ? 'approved' : $returnRequest->status }}</span>
                    <div class="mt-4 flex flex-col items-center text-center space-y-2">
                        <div class="text-[10px] font-bold text-slate-500 uppercase">Received: {{ $returnRequest->created_at->format('d M, Y') }}</div>
                        @if($returnRequest->resolved_at)
                            <div class="text-[10px] font-bold text-indigo-400 uppercase italic">Resolved: {{ $returnRequest->resolved_at->format('d M, Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            @if($returnRequest->status !== 'completed')
            @php
                // Pre-calculate expected debit note total (mirrors controller logic)
                $rr = $returnRequest;
                $rrOrderItem = $rr->orderItem;
                $rrProduct = $rrOrderItem ? $rrOrderItem->product : $rr->product;
                $rrQty = $rr->quantity;
                $rrPcs = $rr->pieces ?? 0;

                if ($rrOrderItem && $rrProduct) {
                    $rrUnitPrice    = $rrOrderItem->price;
                    $rrPqp          = $rrProduct->per_quantity_pieces > 0 ? $rrProduct->per_quantity_pieces : 1;
                    $rrPiecePrice   = $rrProduct->piece_price ?? ($rrUnitPrice / $rrPqp);
                    if (($rr->order && $rr->order->customer_type === 'dealer') || $rrPiecePrice <= 0) {
                        $rrPiecePrice = $rrUnitPrice / $rrPqp;
                    }
                    $rrUnitTax      = ($rrOrderItem->quantity > 0) ? ($rrOrderItem->tax / $rrOrderItem->quantity) : 0;
                    $rrPieceTax     = $rrUnitTax / $rrPqp;
                    $rrUnitDisc     = ($rrOrderItem->quantity > 0) ? (($rrOrderItem->discount_amount ?? 0) / $rrOrderItem->quantity) : 0;
                    $rrPieceDisc    = $rrUnitDisc / $rrPqp;
                } elseif ($rrProduct) {
                    $rrUnitPrice  = $rrProduct->price ?? 0;
                    $rrPqp        = $rrProduct->per_quantity_pieces > 0 ? $rrProduct->per_quantity_pieces : 1;
                    $rrPiecePrice = $rrProduct->piece_price ?? ($rrUnitPrice / $rrPqp);
                    if ($rrPiecePrice <= 0) { $rrPiecePrice = $rrUnitPrice / $rrPqp; }
                    $rrUnitTax = $rrPieceTax = $rrUnitDisc = $rrPieceDisc = 0;
                } else {
                    $rrUnitPrice = $rrPiecePrice = $rrUnitTax = $rrPieceTax = $rrUnitDisc = $rrPieceDisc = 0;
                }

                $rrSubtotal  = ($rrUnitPrice * $rrQty) + ($rrPiecePrice * $rrPcs);
                $rrTax       = ($rrUnitTax * $rrQty) + ($rrPieceTax * $rrPcs);
                $rrDiscount  = ($rrUnitDisc * $rrQty) + ($rrPieceDisc * $rrPcs);
                $rrTotal     = $rrSubtotal + $rrTax - $rrDiscount;
                $rrIsZero    = $rrTotal <= 0;
            @endphp

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-50 pb-2">Admin Resolution</h3>

                {{-- Zero amount preview warning --}}
                @if($rrIsZero)
                <div class="mb-5 p-4 bg-amber-50 border border-amber-300 rounded-2xl flex items-start gap-3">
                    <div class="w-8 h-8 flex-shrink-0 rounded-xl bg-amber-200 text-amber-700 flex items-center justify-center mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-amber-800 uppercase tracking-widest">Debit Note Will Be ₹0.00</p>
                        <p class="text-xs text-amber-700 mt-1 font-medium">No price data found for this product/order item. The debit note total will be <strong>₹0</strong>. No loyalty points will be deducted.</p>
                    </div>
                </div>
                @else
                <div class="mb-5 p-4 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estimated Debit Note</p>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Based on order item price × quantity</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xl font-black text-slate-900">₹{{ number_format($rrTotal, 2) }}</span>
                    </div>
                </div>
                @endif

                {{-- Dealer zero-points notice --}}
                @if($returnRequest->customer && $returnRequest->customer->customer_type === 'dealer' && $returnRequest->customer->loyalty_points <= 0)
                <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-start gap-3">
                    <div class="w-8 h-8 flex-shrink-0 rounded-xl bg-blue-200 text-blue-700 flex items-center justify-center mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z"/>
                        </svg>
                    </div>
                    <div>
                        <!-- <p class="text-[11px] font-black text-blue-800 uppercase tracking-widest">Customer Has 0 Loyalty Points</p> -->
                        <p class="text-xs text-blue-700 mt-1 font-medium">
                            <strong></strong> Currently has <strong>0 pts</strong>.
                            
                        </p>
                    </div>
                </div>
                @endif

                <form id="approveForm" action="{{ route('admin.return-requests.update-status', $returnRequest) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <input type="hidden" name="note_type" value="debit">
                    <input type="hidden" name="adjustment_amount" value="0">

                    @php
                        $dealerZeroPoints = $returnRequest->customer
                            && $returnRequest->customer->customer_type === 'dealer'
                            && $returnRequest->customer->loyalty_points <= 0;
                    @endphp

                    @if($dealerZeroPoints)
                        {{-- Blocked: dealer has 0 points --}}
                        <div class="w-full py-3 px-4 bg-slate-100 border border-slate-200 rounded-2xl flex flex-col items-center gap-1 cursor-not-allowed">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Cannot Approve</span>
                            <span class="text-[10px] text-slate-400 font-medium text-center">
                                Customer <strong>{{ $returnRequest->customer->name }}</strong> has <strong>0 loyalty points</strong>.<br>
                                Approval requires the customer to have points available to deduct.
                            </span>
                        </div>
                    @elseif($rrIsZero)
                        <button type="button" onclick="document.getElementById('zeroConfirmModal').classList.remove('hidden')"
                            class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-100 transition-all active:scale-95">
                            Approve Request
                        </button>
                    @else
                        <button type="submit"
                            class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-emerald-100 transition-all active:scale-95">
                            Approve Request
                        </button>
                    @endif
                </form>
            </div>

            {{-- Zero Total Confirmation Modal --}}
            @if($rrIsZero)
            <div id="zeroConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
                <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-8 border border-amber-200 animate-fade-in">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Debit Note is ₹0</h4>
                            <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mt-0.5">Confirmation Required</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed mb-2">
                        The calculated debit note total for this return request is <strong class="text-rose-600">₹0.00</strong>.
                    </p>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed mb-8">
                        This usually means no price data is available for the product or order item. No loyalty points will be deducted. Do you still want to approve?
                    </p>
                    <div class="flex gap-3">
                        <button type="button" onclick="document.getElementById('zeroConfirmModal').classList.add('hidden')"
                            class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Cancel
                        </button>
                        <button type="button" onclick="document.getElementById('approveForm').submit()"
                            class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg transition-all active:scale-95">
                            Yes, Approve Anyway
                        </button>
                    </div>
                </div>
            </div>
            @endif
            @else
                <!-- <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200 space-y-6">
                    @if($returnRequest->returnNote)
                    <div class="p-5 bg-amber-50 rounded-2xl border border-amber-200 flex items-center justify-between shadow-sm">
                        <div>
                            <span class="text-[10px] font-black text-amber-800 block uppercase tracking-widest">{{ ucfirst($returnRequest->returnNote->type) }} Note</span>
                            <span class="text-base font-mono font-bold text-slate-900 block mt-1">{{ $returnRequest->returnNote->note_number }}</span>
                        </div>
                        <div class="text-right flex flex-col items-end">
                            <span class="text-lg font-black text-slate-900 block">₹{{ number_format($returnRequest->returnNote->total, 2) }}</span>
                            <a href="{{ route('admin.return-requests.return-note', [$returnRequest, $returnRequest->returnNote]) }}" target="_blank" class="text-xs font-black uppercase text-indigo-600 hover:text-indigo-800 transition-colors mt-1 block">View Full Note <i class="fas fa-external-link-alt ml-1 text-[9px]"></i></a>
                        </div>
                    </div>
                    @endif

                    {{-- Loyalty Points Deduction Card --}}
                    @if($returnRequest->customer && $returnRequest->customer->customer_type === 'dealer')
                    <div class="p-5 rounded-2xl border flex items-center justify-between shadow-sm
                        {{ $returnRequest->points_deducted ? 'bg-rose-50 border-rose-200' : 'bg-slate-100 border-slate-200' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center
                                {{ $returnRequest->points_deducted ? 'bg-rose-200 text-rose-700' : 'bg-slate-200 text-slate-500' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-widest block
                                    {{ $returnRequest->points_deducted ? 'text-rose-700' : 'text-slate-500' }}">
                                    Loyalty Points Deducted
                                </span>
                                <span class="text-[10px] text-slate-400 font-medium block mt-0.5">0.1% of debit note value</span>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($returnRequest->points_deducted)
                                <span class="text-2xl font-black text-rose-600 block">-{{ number_format($returnRequest->points_deducted) }}</span>
                                <span class="text-[10px] font-bold text-rose-400 uppercase tracking-widest block">pts removed</span>
                            @else
                                <span class="text-sm font-black text-slate-400 block">N/A</span>
                                <span class="text-[10px] font-medium text-slate-300 block">not applicable</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Dispatch Proof</h3>
                            @if($returnRequest->dispatch_proof_image)
                                <a href="{{ asset('storage/' . $returnRequest->dispatch_proof_image) }}" target="_blank" class="block w-full h-32 rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <img src="{{ asset('storage/' . $returnRequest->dispatch_proof_image) }}" class="h-full w-full object-cover">
                                </a>
                            @else
                                <p class="text-xs font-medium text-slate-400 italic">No dispatch proof attached.</p>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Return/Replacement Invoice</h3>
                            @if($returnRequest->invoice_id)
                                <a href="{{ route('admin.orders.invoice', [$returnRequest->order, $returnRequest->invoice]) }}" target="_blank" class="flex items-center p-4 bg-indigo-50 hover:bg-indigo-100 rounded-2xl border border-indigo-100 transition-all group">
                                    <div class="p-2 bg-indigo-200 text-indigo-700 rounded-lg mr-3">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-black text-indigo-900 block">System Invoice</span>
                                        <span class="text-[10px] font-bold text-indigo-500 block">{{ $returnRequest->invoice->invoice_number }}</span>
                                    </div>
                                </a>
                            @elseif($returnRequest->invoice_proof_image)
                                <a href="{{ asset('storage/' . $returnRequest->invoice_proof_image) }}" target="_blank" class="block w-full h-32 rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                    <img src="{{ asset('storage/' . $returnRequest->invoice_proof_image) }}" class="h-full w-full object-cover">
                                </a>
                            @else
                                <p class="text-xs font-medium text-slate-400 italic">No invoice attached.</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Resolution Archive</h3>
                        <div class="text-sm font-medium text-slate-600 leading-relaxed italic">
                            {{ $returnRequest->admin_notes ?? 'No internal audit notes were archived for this request.' }}
                        </div>
                    </div>
                </div> -->
            @endif
        </div>
    </div>
</div>
@endsection



