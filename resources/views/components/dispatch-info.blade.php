@props(['order'])

@if($order->dispatchImages->count() > 0)
<div x-data="{ imgModalOpen: false, modalImgSrc: '', modalDesc: '' }" class="mt-8">
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h5 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] flex items-center">
                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                Logistics & Dispatch Proofs
            </h5>
            <span class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                {{ $order->dispatchImages->count() }} Files Attached
            </span>
        </div>

        <div class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($order->dispatchImages as $dispatchImage)
                    <div class="group relative bg-slate-50 rounded-3xl border border-slate-100 p-4 transition-all hover:shadow-xl hover:shadow-slate-200/50 hover:bg-white hover:border-indigo-100">
                        <div class="mb-4 flex items-center justify-between">
                            @if($dispatchImage->order_item_id)
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-indigo-100 italic">
                                    Item Proof
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100 italic">
                                    Full Dispatch
                                </span>
                            @endif
                        </div>

                        <div class="relative aspect-video rounded-2xl overflow-hidden bg-slate-200 mb-4 cursor-zoom-in shadow-inner"
                             @click="imgModalOpen = true; modalImgSrc = '{{ asset('storage/' . $dispatchImage->image_path) }}'; modalDesc = '{{ $dispatchImage->description ?? 'Dispatch Proof' }}'">
                            <img src="{{ asset('storage/' . $dispatchImage->image_path) }}" 
                                 alt="Proof" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/20 transition-all flex items-center justify-center">
                                <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                            </div>
                        </div>

                        <div class="space-y-3">
                            @if($dispatchImage->order_item_id)
                                <p class="text-xs font-bold text-slate-800 line-clamp-1">
                                    {{ $dispatchImage->orderItem->product_name ?? 'Unknown Item' }}
                                </p>
                            @endif

                            @if($dispatchImage->description)
                                <div class="p-3 bg-white rounded-xl border border-slate-100 italic text-[11px] text-slate-500 leading-relaxed shadow-sm">
                                    <span class="font-black text-[9px] uppercase text-slate-400 block not-italic mb-1">Admin Notes:</span>
                                    "{{ $dispatchImage->description }}"
                                </div>
                            @endif

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">
                                    By: {{ $dispatchImage->uploaded_by }}
                                </div>
                                <div class="text-[9px] font-bold text-indigo-400 italic">
                                    {{ $dispatchImage->created_at->format('d M, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div x-show="imgModalOpen" 
         class="fixed inset-0 z-[100] overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-md transition-opacity" @click="imgModalOpen = false"></div>

            <button @click="imgModalOpen = false" class="fixed top-6 right-6 text-white/50 hover:text-white transition-colors z-[110]">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="inline-block align-middle bg-transparent rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full z-[105]">
                <div class="relative">
                    <img :src="modalImgSrc" class="w-full h-auto rounded-3xl border-4 border-white/10 shadow-2xl">
                    <div class="mt-4 text-center">
                        <p x-text="modalDesc" class="text-white text-lg font-medium italic"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>