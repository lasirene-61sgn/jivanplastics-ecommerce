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
                $lineNum = $returnRequest->order->items->search(function($item) use ($returnRequest) {
                    return $item->id === $returnRequest->order_item_id;
                });
                $lineNum = $lineNum !== false ? $lineNum + 1 : 1;
            @endphp
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">ID: RR{{ str_pad($returnRequest->id, 3, '0', STR_PAD_LEFT) }}/{{ $lineNum }}</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">Post-Purchase Ticket</p>
            </div>
        </div>
        <a href="{{ route('admin.return-requests.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
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
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Customer Profile</p>
                            <p class="text-lg font-black text-slate-900">{{ $returnRequest->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest mb-1">Order Link</p>
                            <a href="{{ route('admin.orders.show', $returnRequest->order) }}" class="text-sm font-black text-indigo-600 underline">
                                Order #{{ $returnRequest->order->order_number }}
                            </a>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Request Category</p>
                            <p class="text-sm font-black text-slate-700 uppercase italic">{{ $returnRequest->type }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Impacted Quantity</p>
                            <div class="flex gap-2">
                                <span class="px-3 py-1 bg-slate-900 text-white rounded-lg font-black text-xs">{{ $returnRequest->quantity }} Units</span>
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
                    @if($returnRequest->order->manufacturingTeam)
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
                    @if($returnRequest->orderItem->product && $returnRequest->orderItem->product->images->count() > 0)
                        <div class="w-full md:w-40 h-40 flex-shrink-0 rounded-2xl border border-slate-200 overflow-hidden shadow-inner bg-slate-50">
                            <img src="{{ asset('storage/' . $returnRequest->orderItem->product->images->first()->image_path) }}" class="h-full w-full object-cover">
                        </div>
                    @endif
                    <div class="flex-1 space-y-4">
                        <h4 class="text-xl font-black text-slate-900 leading-tight">
                            {{ $returnRequest->orderItem->product ? $returnRequest->orderItem->product->name : $returnRequest->orderItem->product_name }}
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global SKU</p>
                                <p class="text-sm font-mono font-bold text-slate-700 uppercase tracking-tighter">{{ $returnRequest->orderItem->product_sku }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Invoiced Rate</p>
                                <p class="text-sm font-bold text-slate-900 italic">{{ number_format($returnRequest->orderItem->price, 2) }}</p>
                            </div>
                        </div>
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
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-xl">
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-6 italic">Workflow Status</h3>
                <div class="flex flex-col items-center justify-center py-4">
                    @php
                        $manColor = match($returnRequest->status) {
                            'approved' => 'text-green-400',
                            'rejected' => 'text-rose-400',
                            'pending' => 'text-amber-400',
                            default => 'text-blue-400'
                        };
                    @endphp
                    <span class="text-3xl font-black uppercase tracking-tighter {{ $manColor }}">{{ $returnRequest->status }}</span>
                    <div class="mt-4 flex flex-col items-center text-center space-y-2">
                        <div class="text-[10px] font-bold text-slate-500 uppercase">Received: {{ $returnRequest->created_at->format('d M, Y') }}</div>
                        @if($returnRequest->resolved_at)
                            <div class="text-[10px] font-bold text-indigo-400 uppercase italic">Resolved: {{ $returnRequest->resolved_at->format('d M, Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            @if($returnRequest->status !== 'completed')
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-50 pb-2">Admin Resolution</h3>
                <form action="{{ route('admin.return-requests.update-status', $returnRequest) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Adjust Pipeline</label>
                        <select name="status" id="status_select" class="w-full px-4 py-4 rounded-2xl border border-slate-200 bg-slate-50 font-bold text-sm outline-none focus:border-indigo-500" required onchange="toggleNoteType(this.value)">
                            <option value="pending" {{ $returnRequest->status === 'pending' ? 'selected' : '' }}>Pending Audit</option>
                            <option value="approved" {{ $returnRequest->status === 'approved' ? 'selected' : '' }}>Approve Request</option>
                            <option value="rejected" {{ $returnRequest->status === 'rejected' ? 'selected' : '' }}>Reject Request</option>
                            <option value="processing" {{ $returnRequest->status === 'processing' ? 'selected' : '' }}>In-Processing</option>
                            <option value="completed" {{ $returnRequest->status === 'completed' ? 'selected' : '' }}>Close Ticket</option>
                        </select>
                    </div>

                    <div class="space-y-2" id="note_type_container" style="{{ old('status', $returnRequest->status) === 'completed' ? '' : 'display: none;' }}">
                        <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Select Note Type</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="note_type" value="credit" class="hidden peer" {{ old('note_type', 'credit') === 'credit' ? 'checked' : '' }}>
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 group-hover:border-slate-200">
                                    <span class="block text-xs font-black uppercase tracking-widest text-slate-400 peer-checked:text-emerald-600">Credit Note</span>
                                    <span class="block text-[9px] font-bold text-slate-400 mt-1 peer-checked:text-emerald-500 italic">(Return to dealer balance)</span>
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="note_type" value="debit" class="hidden peer" {{ old('note_type') === 'debit' ? 'checked' : '' }}>
                                <div class="p-4 rounded-2xl border-2 border-slate-100 bg-slate-50 text-center transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 group-hover:border-slate-200">
                                    <span class="block text-xs font-black uppercase tracking-widest text-slate-400 peer-checked:text-amber-600">Debit Note</span>
                                    <span class="block text-[9px] font-bold text-slate-400 mt-1 peer-checked:text-amber-500 italic">(Adjustment/Chargeback)</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-2" id="adjustment_container" style="{{ old('status', $returnRequest->status) === 'completed' ? '' : 'display: none;' }}">
                        <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Manual Adjustment Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold text-sm">₹</div>
                            <input type="number" step="0.01" name="adjustment_amount" value="{{ old('adjustment_amount', 0) }}" class="w-full pl-8 pr-4 py-4 rounded-2xl border border-slate-200 bg-slate-50 font-bold text-sm outline-none focus:border-indigo-500" placeholder="0.00">
                        </div>
                        <p class="text-[9px] font-bold text-slate-400 italic">Enter positive for charges (e.g. 500), negative for reductions (e.g. -200).</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Upload Dispatch Proof</label>
                        <input type="file" name="dispatch_proof_image" accept="image/*" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-xs font-bold outline-none focus:border-indigo-500">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Upload Return/Replacement Invoice</label>
                        <input type="file" name="invoice_proof_image" accept="image/*" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 text-xs font-bold outline-none focus:border-indigo-500">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-700 uppercase tracking-widest">Internal Audit Notes</label>
                        <textarea name="admin_notes" rows="4" class="w-full px-4 py-4 rounded-2xl border border-slate-200 text-sm font-medium outline-none focus:border-indigo-500 bg-slate-50 italic shadow-inner" placeholder="Detailed reasoning for approval/rejection...">{{ old('admin_notes', $returnRequest->admin_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 transition-all active:scale-95">
                        Commit Resolution
                    </button>
                </form>
            </div>
            @else
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200 space-y-6">
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
                </div>
            @endif
        </div>
    </div>
</div>


@push('scripts')
<script>
    function toggleNoteType(status) {
        const noteContainer = document.getElementById('note_type_container');
        const adjContainer = document.getElementById('adjustment_container');
        if (status === 'completed') {
            noteContainer.style.display = 'block';
            adjContainer.style.display = 'block';
        } else {
            noteContainer.style.display = 'none';
            adjContainer.style.display = 'none';
        }
    }
</script>
@endpush
@endsection



