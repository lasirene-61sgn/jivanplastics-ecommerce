@extends('layouts.admin')

@section('title', 'Reward Claim Details')

@section('header', 'Redemption Audit')

@section('content')
<div class="max-w-6xl mx-auto pb-20">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-amber-500 flex items-center justify-center text-white shadow-lg shadow-amber-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Claim: #RED-{{ $claim->id }}</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">Loyalty Redemption Ticket</p>
            </div>
        </div>
        <a href="{{ route('admin.reward-claims.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all active:scale-95">
            <i class="fas fa-arrow-left mr-2"></i> Queue
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-6">Claimant Member</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Full Name</p>
                            <p class="text-lg font-black text-slate-900 tracking-tight">{{ $claim->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Registered Email</p>
                            <p class="text-sm font-bold text-indigo-600 underline">{{ $claim->customer->email }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-6">Target Asset</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Reward ID</p>
                            <p class="text-lg font-black text-slate-900 tracking-tight">{{ $claim->reward->name }}</p>
                        </div>
                        <div class="flex gap-3">
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-[10px] font-black uppercase tracking-widest border border-amber-100">
                                {{ str_replace('_', ' ', $claim->reward->type) }}
                            </span>
                            <span class="px-3 py-1 bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">
                                {{ $claim->reward->required_points }} PTS
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 space-y-8">
                    @if($claim->reward->type === 'product' && $claim->reward->product)
                        <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                            <div class="w-20 h-20 bg-white rounded-2xl border border-slate-200 overflow-hidden flex-shrink-0">
                                @if($claim->reward->product->images->first())
                                    <img src="{{ asset('storage/' . $claim->reward->product->images->first()->image_path) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-[10px] text-slate-300 font-bold uppercase">No Pic</div>
                                @endif
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase">Linked Catalog Product</p>
                                <p class="text-lg font-black text-slate-900">{{ $claim->reward->product->name }}</p>
                            </div>
                        </div>
                    @elseif($claim->reward->type === 'travel_package' && $claim->reward->price)
                        <div class="p-6 bg-sky-50 rounded-3xl border border-sky-100">
                            <p class="text-[10px] font-black text-sky-600 uppercase mb-1 tracking-widest">Market Valuation</p>
                            <p class="text-2xl font-black text-slate-900 italic">₹{{ number_format($claim->reward->price, 2) }}</p>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 pb-2">Redemption Narrative</h4>
                        <div class="text-base text-slate-700 leading-relaxed font-medium bg-slate-50/50 p-6 rounded-2xl italic">
                            {{ $claim->reward->description ?? 'No specific terms were outlined for this reward asset.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-slate-900 rounded-[2rem] p-8 shadow-2xl text-white">
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-8 italic">Administrative Control</h3>
                
                @if($claim->status !== 'fulfilled')
                    <form action="{{ route('admin.reward-claims.update-status', $claim) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf @method('PUT')
                        
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Adjust Pipeline</label>
                            <select name="status" class="w-full px-4 py-4 rounded-2xl border-0 bg-slate-800 font-bold text-sm outline-none focus:ring-2 focus:ring-indigo-500 text-white" required>
                                <option value="pending" {{ $claim->status === 'pending' ? 'selected' : '' }}>Pending Audit</option>
                                <option value="approved" {{ $claim->status === 'approved' ? 'selected' : '' }}>Approve Request</option>
                                <option value="rejected" {{ $claim->status === 'rejected' ? 'selected' : '' }}>Reject Claim</option>
                                <option value="fulfilled" {{ $claim->status === 'fulfilled' ? 'selected' : '' }}>Mark Fulfilled</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Dispatch Proof</label>
                                <input type="file" name="dispatch_proof_image" accept="image/*" class="w-full px-4 py-3 rounded-2xl bg-slate-800 text-xs font-bold text-slate-400 border-0 focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Invoice Proof</label>
                                <input type="file" name="invoice_proof_image" accept="image/*" class="w-full px-4 py-3 rounded-2xl bg-slate-800 text-xs font-bold text-slate-400 border-0 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Fulfillment Notes</label>
                            <textarea name="admin_notes" rows="4" class="w-full px-4 py-4 rounded-2xl border-0 bg-slate-800 text-sm font-medium focus:ring-2 focus:ring-indigo-500 italic text-white" placeholder="Enter courier IDs or processing notes...">{{ old('admin_notes', $claim->admin_notes) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] transition-all shadow-lg active:scale-95">
                            Commit Resolution
                        </button>
                    </form>
                @else
                    <div class="space-y-6">
                        <div class="text-center py-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-500/20 text-emerald-400 mb-4">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </div>
                            <p class="text-xl font-black uppercase tracking-tight text-emerald-400">Claim Fulfilled</p>
                            <p class="text-[10px] text-slate-500 font-bold uppercase mt-1">Processed: {{ $claim->processed_at->format('M d, Y') }}</p>
                        </div>

                        @if($claim->invoice_id)
                            <a href="{{ route('admin.reward-claims.invoice', $claim) }}" target="_blank" class="flex items-center p-4 bg-slate-800 hover:bg-slate-700 rounded-2xl border border-slate-700 transition-all group">
                                <div class="p-2 bg-indigo-500 text-white rounded-lg mr-3">
                                    <i class="fas fa-file-invoice"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-white block">Reward Invoice Generated</span>
                                    <span class="text-[10px] font-bold text-indigo-400 block">{{ $claim->invoice->invoice_number }}</span>
                                </div>
                            </a>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            @if($claim->dispatch_proof_image)
                                <a href="{{ asset('storage/' . $claim->dispatch_proof_image) }}" target="_blank" class="block p-3 bg-slate-800 rounded-2xl border border-slate-700 text-center">
                                    <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Dispatch Proof</p>
                                    <i class="fas fa-truck text-indigo-400"></i>
                                </a>
                            @endif
                            @if($claim->invoice_proof_image)
                                <a href="{{ asset('storage/' . $claim->invoice_proof_image) }}" target="_blank" class="block p-3 bg-slate-800 rounded-2xl border border-slate-700 text-center">
                                    <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Manual Invoice</p>
                                    <i class="fas fa-file-alt text-rose-400"></i>
                                </a>
                            @endif
                        </div>

                        <div class="p-6 bg-slate-800 rounded-2xl border border-slate-700">
                            <p class="text-[9px] font-black text-slate-500 uppercase mb-2 tracking-widest">Archived Internal Note</p>
                            <p class="text-sm font-medium text-slate-300 italic">{{ $claim->admin_notes ?? 'No internal notes recorded.' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-4">
                <div class="flex justify-between items-center text-xs font-bold uppercase tracking-tighter">
                    <span class="text-slate-400">Created:</span>
                    <span class="text-slate-900">{{ $claim->claimed_at->format('d M, Y H:i') }}</span>
                </div>
                @if($claim->processed_at)
                    <div class="flex justify-between items-center text-xs font-bold uppercase tracking-tighter text-indigo-500">
                        <span>Resolved:</span>
                        <span>{{ $claim->processed_at->format('d M, Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection