@extends('layouts.admin')

@section('title', 'Return Requests')

@section('header', 'Post-Purchase Support')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Return Requests</h2>
            <p class="text-sm text-slate-500 font-medium italic">Manage customer inquiries regarding product returns.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        @if($returnRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Order Info</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Customer & Product</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Qty / Type</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Reason</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($returnRequests as $request)
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    @php
                                        $lineNum = 1;
                                        if ($request->order && $request->order_item_id) {
                                            $lineNum = $request->order->items->search(function($item) use ($request) {
                                                return $item->id === $request->order_item_id;
                                            }) + 1;
                                        }
                                    @endphp
                                    <span class="text-sm font-black text-slate-800">RR{{ str_pad($request->id, 3, '0', STR_PAD_LEFT) }}/{{ $lineNum }}</span>
                                    @if($request->request_number)
                                        <div class="text-xs font-bold text-slate-500">{{ $request->request_number }}</div>
                                    @endif
                                    @if($request->order)
                                        <div class="text-xs font-black text-slate-900 mt-0.5">Ord: #{{ $request->order->order_number }}</div>
                                    @else
                                        <div class="text-[10px] font-bold text-rose-500 uppercase mt-1 tracking-widest bg-rose-50 px-2 py-0.5 rounded inline-block border border-rose-100">Mfg Direct</div>
                                    @endif
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tight mt-1">{{ $request->created_at->format('d M, Y') }}</div>
                                    
                                    @if($request->order && $request->order->manufacturingTeam)
                                    <div class="mt-3 p-2 bg-slate-50 rounded-lg border border-slate-100">
                                        <div class="text-[10px] font-black text-slate-700 uppercase tracking-widest">{{ $request->order->manufacturingTeam->factory_name }}</div>
                                        <div class="flex flex-col mt-1 space-y-0.5">
                                            <span class="text-[9px] text-slate-500 font-bold">Alloc: {{ $request->created_at->format('d M, y') }}</span>
                                            <span class="text-[9px] text-slate-500 font-bold">Comp: {{ $request->resolved_at ? $request->resolved_at->format('d M, y') : 'Pending' }}</span>
                                        </div>
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $request->customer->name }} <span class="text-[10px] text-slate-500 uppercase">({{ $request->customer->customer_type }})</span></div>
                                    <div class="text-xs text-slate-500 mb-1"><i class="fas fa-phone-alt text-[10px] mr-1"></i>{{ $request->customer->phone }}</div>
                                    @php
                                        $productModel = null;
                                        $productName = 'N/A';
                                        if ($request->orderItem) {
                                            $productModel = $request->orderItem->product;
                                            $productName = $productModel ? $productModel->name : $request->orderItem->product_name;
                                        } elseif ($request->product) {
                                            $productModel = $request->product;
                                            $productName = $productModel->name;
                                        }
                                    @endphp
                                    <div class="text-xs text-indigo-600 font-bold truncate max-w-[180px]">
                                        {{ $productName }}
                                    </div>
                                    @if($productModel)
                                        <div class="mt-1 flex flex-wrap gap-1 text-[9px] font-bold text-slate-500 uppercase tracking-tighter">
                                            @if($productModel->size)
                                                <span class="px-1.5 py-0.5 bg-slate-100 rounded">S: {{ $productModel->size }}</span>
                                            @endif
                                            @if($productModel->thickness)
                                                <span class="px-1.5 py-0.5 bg-slate-100 rounded">T: {{ $productModel->thickness }}</span>
                                            @endif
                                            @if($productModel->color)
                                                <span class="px-1.5 py-0.5 bg-slate-100 rounded">C: {{ $productModel->color }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="text-sm font-black text-slate-900">{{ $request->quantity }} units</div>
                                    @if($request->pieces > 0)
                                        <div class="text-sm font-black text-slate-900 mt-1">{{ $request->pieces }} pieces</div>
                                    @endif
                                    <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 italic">{{ $request->type }}</span>
                                </td>

                                <td class="px-6 py-4">
                                    <p class="text-xs text-slate-600 line-clamp-1 max-w-[150px] italic">"{{ $request->reason }}"</p>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badgeClass = match($request->status) {
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'approved' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'completed' => 'bg-slate-900 text-white',
                                            default => 'bg-slate-100 text-slate-500'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-sm {{ $badgeClass }}">
                                        {{ $request->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.return-requests.show', $request) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-black uppercase tracking-widest rounded-xl shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all active:scale-95">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($returnRequests->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $returnRequests->links() }}
                </div>
            @endif
        @else
            <div class="p-20 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                </div>
                <p class="text-slate-500 font-medium italic text-sm">No return requests found in the pipeline.</p>
            </div>
        @endif
    </div>
</div>
@endsection