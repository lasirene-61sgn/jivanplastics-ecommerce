@extends('layouts.admin')

@section('title', 'Portfolio Analytics - Admin Panel')

@section('header', 'Dealer Portfolio Management')

@section('content')
<div class="space-y-8 pb-12">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Portfolio: {{ $salesTeam->name }}</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase">Client Relationship Mapping</p>
            </div>
        </div>
        <a href="{{ route('admin.sales-team.show', $salesTeam) }}" class="inline-flex items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Profile View
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm text-center group hover:border-indigo-300 transition-all">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Reach</p>
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter italic">{{ $totalCustomers }}</h3>
        </div>
        <div class="bg-emerald-50 p-5 rounded-3xl border border-emerald-100 text-center group">
            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">Active</p>
            <h3 class="text-2xl font-black text-emerald-700 tracking-tighter italic">{{ $activeCustomers }}</h3>
        </div>
        <div class="bg-slate-50 p-5 rounded-3xl border border-slate-200 text-center group">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Inactive</p>
            <h3 class="text-2xl font-black text-slate-500 tracking-tighter italic">{{ $inactiveCustomers }}</h3>
        </div>
        <div class="bg-indigo-50 p-5 rounded-3xl border border-indigo-100 text-center group">
            <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">High Value</p>
            <h3 class="text-2xl font-black text-indigo-700 tracking-tighter italic">{{ $highValueCustomers }}</h3>
        </div>
        <div class="bg-amber-50 p-5 rounded-3xl border border-amber-100 text-center group">
            <p class="text-[9px] font-black text-amber-600 uppercase tracking-widest mb-1">Mid Tier</p>
            <h3 class="text-2xl font-black text-amber-700 tracking-tighter italic">{{ $mediumValueCustomers }}</h3>
        </div>
        <div class="bg-rose-50 p-5 rounded-3xl border border-rose-100 text-center group">
            <p class="text-[9px] font-black text-rose-600 uppercase tracking-widest mb-1">Low Tier</p>
            <h3 class="text-2xl font-black text-rose-700 tracking-tighter italic">{{ $lowValueCustomers }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Assigned Portfolio Manifest</h3>
            <span class="text-[10px] font-black bg-indigo-600 text-white px-3 py-1 rounded-lg uppercase tracking-tighter shadow-lg shadow-indigo-100 italic">Total {{ $dealers->count() }} Dealers</span>
        </div>

        <div class="p-4">
            @if($dealers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                                <th class="px-6 py-4">Dealer Identity</th>
                                <th class="px-6 py-4">Business Entity</th>
                                <th class="px-6 py-4">Contact Logic</th>
                                <th class="px-6 py-4 text-center">Volume</th>
                                <th class="px-6 py-4">Last Node</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($dealers as $dealer)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $dealer->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">UID: #DLR-{{ $dealer->id }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-xs font-bold text-slate-600 uppercase">{{ $dealer->company_name ?? 'Individual' }}</div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="text-[11px] font-bold text-slate-500">{{ $dealer->email }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">{{ $dealer->phone ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="px-3 py-1 bg-slate-100 rounded-lg text-xs font-black text-slate-900 border border-slate-200">
                                            {{ $dealer->orders_count }} <small class="text-[8px] uppercase tracking-tighter ml-1">Orders</small>
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-xs font-bold text-slate-400 italic">
                                        @php $lastOrder = $dealer->orders()->latest()->first(); @endphp
                                        {{ $lastOrder ? $lastOrder->created_at->format('M d, Y') : 'No History' }}
                                    </td>
                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.sales-team.customer-details', [$salesTeam, $dealer]) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all active:scale-95 group/btn">
                                            Intelligence 
                                            <svg class="w-3 h-3 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-20 text-center text-slate-400 italic font-medium">
                    No active dealer assignments detected for this representative.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection