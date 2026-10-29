@extends('layouts.admin')

@section('title', 'Dealer Customers - Admin Panel')

@section('header', 'B2B Partners')

@section('content')
<div class="space-y-6" x-data="{
    openDiscountModal: false,
    targetScope: 'all',
    selectedDealers: [],
    selectAll: false,
    toggleSelectAll(checked, ids) {
        this.selectAll = checked;
        this.selectedDealers = checked ? [...ids] : [];
    }
}">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Dealer Directory</h2>
            <p class="text-sm text-slate-500">Manage your verified wholesale and B2B business partners.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex bg-slate-100 p-1 rounded-xl mr-2">
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:text-indigo-600 transition-colors">All</a>
                <a href="{{ route('admin.customers.dealers') }}" class="px-4 py-2 text-xs font-bold rounded-lg bg-white text-indigo-600 shadow-sm">Dealers</a>
                <a href="{{ route('admin.customers.individuals') }}" class="px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:text-indigo-600 transition-colors">Individuals</a>
            </div>
            
            <button type="button" @click="openDiscountModal = true" class="inline-flex items-center px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-200 transition-all transform hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Set Discounts (All / Selected)
            </button>

            <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add Dealer
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Active Dealers</p>
            <p class="text-xl font-bold text-slate-900">{{ $customers->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global Reach</p>
            <p class="text-xl font-bold text-slate-900">{{ $customers->unique('city')->count() }} Cities</p>
        </div>
        <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-amber-700 uppercase tracking-widest">Selected Dealers</p>
                <p class="text-xl font-bold text-amber-900"><span x-text="selectedDealers.length"></span> / {{ $customers->count() }}</p>
            </div>
            <button type="button" x-show="selectedDealers.length > 0" @click="targetScope = 'selected'; openDiscountModal = true" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm">
                Apply to Selected
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg flex items-center shadow-sm italic text-sm">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg flex items-center shadow-sm italic text-sm">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        @if($customers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-4 py-4 w-10 text-center">
                                <input type="checkbox" @change="toggleSelectAll($event.target.checked, {{ $customers->pluck('id')->toJson() }})" :checked="selectAll && selectedDealers.length === {{ $customers->count() }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Representative</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Business Info</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Discounts (Draft & Online)</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($customers as $customer)
                            @php
                                $effectiveOnline = (float)($customer->online_discount > 0 ? $customer->online_discount : ($customer->bank_transfer_discount ?? 0));
                                $draftDisc = (float)($customer->draft_discount ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors group" :class="selectedDealers.includes({{ $customer->id }}) && 'bg-amber-50/30'">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" value="{{ $customer->id }}" x-model.number="selectedDealers" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>

                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="flex items-center group/link">
                                        <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold border border-indigo-100 group-hover/link:bg-indigo-600 group-hover/link:text-white transition-all">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-slate-900 group-hover/link:text-indigo-600 transition-colors">{{ $customer->name }}</div>
                                            <div class="text-xs text-slate-400 font-medium">{{ $customer->email }}</div>
                                        </div>
                                    </a>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-700">{{ $customer->company_name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono tracking-tighter">{{ $customer->phone ?? 'NO PHONE' }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Draft Disc: {{ $draftDisc }}%
                                            </span>
                                            @if($customer->is_cod_allowed)
                                                <span class="text-[9px] font-bold text-emerald-600" title="Draft Payment Allowed">● Allowed</span>
                                            @else
                                                <span class="text-[9px] font-bold text-rose-500" title="Draft Payment Locked">● Locked</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Online Disc: {{ $effectiveOnline }}%
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center text-xs text-slate-600">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        {{ $customer->city }}, {{ $customer->state }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($customer->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-widest">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-600 uppercase tracking-widest">
                                            Suspended
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="View Profile">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit Customer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Archive this dealer profile?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    {{ $customers->links() }}
                </div>
            @endif
        @else
            <div class="p-20 text-center">
                <div class="mx-auto w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">No dealers registered</h3>
                <p class="text-slate-500 mt-2">You haven't added any B2B dealer customers yet.</p>
                <a href="{{ route('admin.customers.create') }}" class="mt-6 inline-flex items-center text-indigo-600 font-bold hover:text-indigo-700">
                    Register first dealer →
                </a>
            </div>
        @endif
    </div>

    <!-- Bulk / Multi-Dealer Discount Modal -->
    <div x-show="openDiscountModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.away="openDiscountModal = false" class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-xl overflow-hidden">
            <div class="p-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center">
                        <span class="w-2.5 h-6 bg-amber-500 rounded-full mr-3"></span>
                        Set Dealer Payment Discounts
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Apply Draft Payment and Online discounts to All or Selected dealers.</p>
                </div>
                <button type="button" @click="openDiscountModal = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl hover:bg-white transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form action="{{ route('admin.customers.dealers.bulk-discounts') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <!-- Target Scope Selection -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Apply Discounts To:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-3 rounded-xl border cursor-pointer transition-all"
                               :class="targetScope === 'all' ? 'border-amber-500 bg-amber-50/40 text-amber-900 font-bold' : 'border-slate-200 bg-white text-slate-600'">
                            <input type="radio" name="target_scope" value="all" x-model="targetScope" class="sr-only">
                            <div>
                                <span class="block text-sm">🌟 All Dealers</span>
                                <span class="text-[11px] font-normal text-slate-400">All registered B2B dealers</span>
                            </div>
                        </label>
                        <label class="flex items-center p-3 rounded-xl border cursor-pointer transition-all"
                               :class="targetScope === 'selected' ? 'border-amber-500 bg-amber-50/40 text-amber-900 font-bold' : 'border-slate-200 bg-white text-slate-600'">
                            <input type="radio" name="target_scope" value="selected" x-model="targetScope" class="sr-only">
                            <div>
                                <span class="block text-sm">🎯 Selected Dealers</span>
                                <span class="text-[11px] font-normal text-slate-400"><span x-text="selectedDealers.length"></span> dealer(s) selected</span>
                            </div>
                        </label>
                    </div>
                    <div x-show="targetScope === 'selected' && selectedDealers.length === 0" class="text-xs text-rose-500 font-semibold mt-1">
                        ⚠️ Please check one or more dealers in the table to apply to selected.
                    </div>
                </div>

                <!-- Hidden inputs for selected dealer IDs -->
                <template x-for="id in selectedDealers" :key="id">
                    <input type="hidden" name="dealer_ids[]" :value="id">
                </template>

                <!-- Percentage Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div class="space-y-2 bg-amber-50/40 p-4 rounded-2xl border border-amber-200/60">
                        <label for="modal_draft_discount" class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center">
                            Draft Payment Disc (%)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" max="100" id="modal_draft_discount" name="draft_discount" class="w-full px-4 py-2.5 rounded-xl border border-amber-300 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none pr-10 font-bold text-amber-700 bg-white" placeholder="e.g. 5.00">
                            <span class="absolute right-4 top-2.5 text-sm font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[10px] text-slate-500">Discount when dealer places order with Draft Payment.</p>
                    </div>

                    <div class="space-y-2 bg-indigo-50/40 p-4 rounded-2xl border border-indigo-200/60">
                        <label for="modal_online_discount" class="text-xs font-bold text-indigo-900 uppercase tracking-wider flex items-center">
                            Online Discount (%)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" max="100" id="modal_online_discount" name="online_discount" class="w-full px-4 py-2.5 rounded-xl border border-indigo-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none pr-10 font-bold text-indigo-700 bg-white" placeholder="e.g. 10.00">
                            <span class="absolute right-4 top-2.5 text-sm font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[10px] text-slate-500">Discount when dealer places order with Online Payment.</p>
                    </div>
                </div>

                <!-- Draft Payment Permission (Optional) -->
                <div class="space-y-2 pt-2">
                    <label for="modal_is_cod_allowed" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Draft Payment Status (Optional)</label>
                    <select name="is_cod_allowed" id="modal_is_cod_allowed" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white text-sm font-medium">
                        <option value="">Leave status unchanged</option>
                        <option value="1">Allow Draft Payment</option>
                        <option value="0">Lock Draft Payment (Online Only)</option>
                    </select>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button type="button" @click="openDiscountModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" :disabled="targetScope === 'selected' && selectedDealers.length === 0" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl shadow-lg shadow-amber-200 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Apply Discount Percentage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection