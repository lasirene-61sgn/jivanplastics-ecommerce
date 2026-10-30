@extends('frontend.b2b.layouts.app')

@section('title', 'Rewards & Claims - E-Commerce Store')
@section('header', 'Rewards & Claims')

@section('content')
<div class="b2b-content" x-data="{ activeTab: '{{ request('tab', 'available') }}' }">
    {{-- Header with Points and Action --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Loyalty Rewards & Claims</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Redeem your accumulated loyalty points and track your claimed rewards history.</p>
        </div>
        
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Points Display --}}
            <div class="bg-white border border-slate-200/80 rounded-2xl px-4 py-2.5 shadow-xs flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Available Points</span>
                    <span class="text-base font-black text-red-600 tracking-tight">{{ number_format($customer->loyalty_points) }} pts</span>
                </div>
            </div>

            {{-- View Claimed Rewards Button --}}
            <button type="button" 
                    @click="activeTab = 'claimed'"
                    :class="activeTab === 'claimed' ? 'bg-slate-900 text-white' : 'bg-red-600 hover:bg-red-700 text-white shadow-md shadow-red-200'"
                    class="flex items-center gap-2 px-4 py-3 rounded-2xl font-black text-xs uppercase tracking-wider transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>View Claimed Rewards ({{ $myClaims->count() }})</span>
            </button>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="flex items-center gap-2 border-b border-slate-200 mb-6">
        <button type="button" 
                @click="activeTab = 'available'" 
                :class="activeTab === 'available' ? 'border-b-2 border-red-600 text-red-600 font-black' : 'text-slate-500 font-bold hover:text-slate-800'" 
                class="pb-3 px-4 text-xs uppercase tracking-wider transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z"></path>
            </svg>
            <span>Available Rewards ({{ $rewards->count() }})</span>
        </button>

        <button type="button" 
                @click="activeTab = 'claimed'" 
                :class="activeTab === 'claimed' ? 'border-b-2 border-red-600 text-red-600 font-black' : 'text-slate-500 font-bold hover:text-slate-800'" 
                class="pb-3 px-4 text-xs uppercase tracking-wider transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span>My Claimed Rewards ({{ $myClaims->count() }})</span>
            @if($myClaims->where('status', 'pending')->count() > 0)
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            @endif
        </button>
    </div>

    {{-- TAB 1: AVAILABLE REWARDS --}}
    <div x-show="activeTab === 'available'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
        @if($rewards->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($rewards as $reward)
                    @php
                        $existingClaim = $customer->rewardClaims()->where('reward_id', $reward->id)->whereIn('status', ['pending', 'approved', 'fulfilled'])->first();
                        $hasEnoughPoints = $customer->loyalty_points >= $reward->required_points;
                    @endphp
                    <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col group">
                        {{-- Image / Visual --}}
                        <div class="relative h-48 bg-slate-100 overflow-hidden flex items-center justify-center">
                            @if($reward->type === 'product' && $reward->product && $reward->product->images->count() > 0)
                                <img src="{{ asset('storage/' . $reward->product->images->first()->image_path) }}" 
                                     alt="{{ $reward->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @elseif($reward->type === 'travel_package')
                                <div class="w-full h-full bg-gradient-to-br from-indigo-900 to-indigo-700 flex flex-col items-center justify-center text-white p-4">
                                    <svg class="w-12 h-12 text-indigo-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-black uppercase tracking-widest text-indigo-200">Travel Package</span>
                                </div>
                            @else
                                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            {{-- Points Badge (Floating Top Right) --}}
                            <div class="absolute top-3 right-3 bg-slate-900/90 backdrop-blur-xs text-white px-3 py-1 rounded-xl text-xs font-black shadow-md">
                                {{ number_format($reward->required_points) }} pts
                            </div>

                            {{-- Type Badge (Floating Top Left) --}}
                            <div class="absolute top-3 left-3">
                                @if($reward->type === 'product')
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-emerald-500 text-white shadow-xs">
                                        Free Product
                                    </span>
                                @elseif($reward->type === 'travel_package')
                                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-indigo-500 text-white shadow-xs">
                                        Travel Tour
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Details --}}
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-base font-black text-slate-900 leading-tight mb-2 uppercase tracking-tight">
                                    {{ $reward->name }}
                                </h3>

                                @if($reward->description)
                                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                        {{ $reward->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="mt-4 pt-4 border-t border-slate-100">
                                @if($existingClaim)
                                    <div class="space-y-2">
                                        <div class="w-full py-2.5 px-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-xs font-black text-center flex items-center justify-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>Already Claimed ({{ ucfirst($existingClaim->status) }})</span>
                                        </div>
                                        <button type="button" 
                                                @click="activeTab = 'claimed'" 
                                                class="w-full text-center text-[11px] font-bold text-red-600 hover:text-red-700 hover:underline block">
                                            Track Claim Details & Evidence →
                                        </button>
                                    </div>
                                @elseif($hasEnoughPoints)
                                    <a href="{{ route('b2b.rewards.claim.form', $reward) }}" 
                                       class="w-full block py-3 bg-red-600 hover:bg-red-700 active:scale-95 text-white text-center rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-md shadow-red-200">
                                        Claim This Reward
                                    </a>
                                @else
                                    <button disabled 
                                            class="w-full py-3 bg-slate-100 text-slate-400 rounded-xl text-xs font-bold uppercase tracking-wider cursor-not-allowed">
                                        Need {{ number_format($reward->required_points - $customer->loyalty_points) }} more pts
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-black text-slate-900 mb-1">No Rewards Available Right Now</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Please check back later for new promotional rewards and trade loyalty offers.</p>
            </div>
        @endif
    </div>

    {{-- TAB 2: MY CLAIMED REWARDS TRACKING --}}
    <div x-show="activeTab === 'claimed'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
        {{-- Claims KPI Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Claims</span>
                <span class="text-xl font-black text-slate-900">{{ $myClaims->count() }}</span>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Pending Approval</span>
                <span class="text-xl font-black text-amber-600">{{ $myClaims->where('status', 'pending')->count() }}</span>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Approved & Fulfilled</span>
                <span class="text-xl font-black text-emerald-600">{{ $myClaims->whereIn('status', ['approved', 'fulfilled'])->count() }}</span>
            </div>
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Points Redeemed</span>
                <span class="text-xl font-black text-red-600">{{ number_format($myClaims->sum(fn($c) => $c->reward->required_points ?? 0)) }} pts</span>
            </div>
        </div>

        @if($myClaims->count() > 0)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-black text-slate-900 tracking-tight">Claimed Rewards History</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Track the approval, dispatch, and tax invoice documentation for your claimed rewards.</p>
                    </div>
                    <a href="{{ route('b2b.reward-claims.index') }}" class="text-xs font-black text-red-600 hover:text-red-700 hover:underline flex items-center gap-1">
                        <span>Full Claims Page</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100 text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                <th class="px-6 py-4">Reward Item</th>
                                <th class="px-4 py-4 text-center">Type</th>
                                <th class="px-4 py-4 text-center">Points</th>
                                <th class="px-4 py-4 text-center">Status</th>
                                <th class="px-4 py-4 text-center">Evidence / Invoice</th>
                                <th class="px-6 py-4 text-right">Claimed Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($myClaims as $claim)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    {{-- Reward Item --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($claim->reward->type === 'product' && $claim->reward->product && $claim->reward->product->images->count() > 0)
                                                <img src="{{ asset('storage/' . $claim->reward->product->images->first()->image_path) }}" 
                                                     alt="{{ $claim->reward->name }}" 
                                                     class="w-12 h-12 rounded-xl object-cover border border-slate-200/80 flex-shrink-0">
                                            @elseif($claim->reward->type === 'travel_package')
                                                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-xs flex-shrink-0">
                                                    Tour
                                                </div>
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                    Gift
                                                </div>
                                            @endif

                                            <div>
                                                <h4 class="text-sm font-black text-slate-900 leading-tight uppercase">
                                                    {{ $claim->reward->name }}
                                                </h4>
                                                @if($claim->rejection_reason && $claim->status === 'rejected')
                                                    <p class="text-[11px] text-red-600 mt-0.5">Reason: {{ $claim->rejection_reason }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Type --}}
                                    <td class="px-4 py-4 text-center">
                                        @if($claim->reward->type === 'product')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                Product
                                            </span>
                                        @elseif($claim->reward->type === 'travel_package')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                Travel
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-50 text-slate-600">
                                                Reward
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Points --}}
                                    <td class="px-4 py-4 text-center font-black text-sm text-slate-800">
                                        {{ number_format($claim->reward->required_points) }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-4 text-center">
                                        @if($claim->status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Pending
                                            </span>
                                        @elseif($claim->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Approved
                                            </span>
                                        @elseif($claim->status === 'fulfilled')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-sky-50 text-sky-700 border border-sky-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                                Fulfilled
                                            </span>
                                        @elseif($claim->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Rejected
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Evidence / Documents --}}
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if($claim->dispatch_proof_image)
                                                <a href="{{ asset('storage/' . $claim->dispatch_proof_image) }}" 
                                                   target="_blank" 
                                                   class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-bold border border-emerald-200 flex items-center gap-1 transition-colors"
                                                   title="View Dispatch Proof">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                                    </svg>
                                                    <span>Dispatch</span>
                                                </a>
                                            @endif

                                            @if($claim->invoice_id)
                                                <a href="{{ route('b2b.reward-claims.invoice', $claim) }}" 
                                                   target="_blank" 
                                                   class="px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-[10px] font-bold border border-blue-200 flex items-center gap-1 transition-colors"
                                                   title="Download System Tax Invoice">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span>Invoice</span>
                                                </a>
                                            @elseif($claim->invoice_proof_image)
                                                <a href="{{ asset('storage/' . $claim->invoice_proof_image) }}" 
                                                   target="_blank" 
                                                   class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-lg text-[10px] font-bold border border-slate-200 flex items-center gap-1 transition-colors"
                                                   title="View Invoice Proof">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span>Proof</span>
                                                </a>
                                            @endif

                                            @if(!$claim->dispatch_proof_image && !$claim->invoice_id && !$claim->invoice_proof_image)
                                                <span class="text-slate-300 text-xs font-bold">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Claimed Date --}}
                                    <td class="px-6 py-4 text-right text-xs font-bold text-slate-500 whitespace-nowrap">
                                        {{ $claim->claimed_at ? $claim->claimed_at->format('M d, Y') : ($claim->created_at ? $claim->created_at->format('M d, Y') : 'N/A') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <h3 class="text-base font-black text-slate-900 mb-1">No Claimed Rewards Yet</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">You have not claimed any rewards yet. When you claim an available reward, it will appear here for status tracking.</p>
                <button type="button" @click="activeTab = 'available'" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all">
                    Explore Available Rewards
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
