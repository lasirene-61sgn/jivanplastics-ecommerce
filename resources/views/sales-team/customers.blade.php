@extends('layouts.sales-team')

@section('title', 'Client Portfolio - Sales Team Portal')

@section('header', 'Relationship Management')

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic text-indigo-600">Client Portfolio</h2>
            <p class="text-sm text-slate-500 font-medium italic">Directory of dealers and customers assigned to your executive desk.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-400 shadow-sm">
                Active Nodes: {{ $customers->count() }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Assigned Portfolio Manifest</h3>
        </div>

        <div class="p-4">
            @if($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 italic">
                                <th class="px-6 py-4">Liaison Identity</th>
                                <th class="px-6 py-4">Corporate Entity</th>
                                <th class="px-6 py-4">Communication Node</th>
                                <th class="px-6 py-4 text-center">Lifetime Volume</th>
                                <th class="px-6 py-4 text-right">Intel</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($customers as $customer)
                                <tr class="hover:bg-slate-50/80 transition-colors group">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm italic">
                                                {{ substr($customer->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $customer->name }}</div>
                                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $customer->customer_type }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="text-xs font-bold text-slate-600 uppercase tracking-tighter italic">
                                            {{ $customer->company_name ?? 'Individual' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="text-[11px] font-bold text-slate-500 lowercase">{{ $customer->email }}</div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $customer->phone ?? 'N/A' }}</div>
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-black text-slate-900 italic">{{ $customer->orders_count }}</span>
                                            <span class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Invoices</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5 text-right whitespace-nowrap">
                                        <a href="{{ route('sales-team.customers.show', $customer) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all active:scale-95 group/btn">
                                            Inspect Profile
                                            <svg class="w-3 h-3 ml-2 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="py-24 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-200 mb-4 italic">
                        <i class="fas fa-users-slash fa-2x"></i>
                    </div>
                    <p class="text-slate-400 font-bold tracking-tight italic">No assigned customers detected in your portfolio.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection