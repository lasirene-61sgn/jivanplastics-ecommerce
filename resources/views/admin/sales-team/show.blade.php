@extends('layouts.admin')

@section('title', 'Sales Representative Profile - Admin Panel')

@section('header', 'Personnel Intelligence')

@section('content')
<div class="max-w-7xl mx-auto pb-12 space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-5">
            <div class="h-20 w-20 rounded-3xl bg-indigo-600 flex items-center justify-center text-white text-4xl font-black shadow-2xl shadow-indigo-100 italic">
                {{ substr($salesTeam->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $salesTeam->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-indigo-100 text-indigo-700">
                        {{ $salesTeam->department }} Executive
                    </span>
                    @if($salesTeam->is_active)
                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-green-100 text-green-700 uppercase tracking-widest border border-green-200">Active Duty</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-rose-50 text-rose-500 uppercase tracking-widest border border-rose-100">Suspended</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.sales-team.edit', $salesTeam) }}" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-amber-100 transition-all active:scale-95">
                <i class="fas fa-edit mr-2"></i> Edit Access
            </a>
            <a href="{{ route('admin.sales-team.orders', $salesTeam) }}" class="px-6 py-3 bg-slate-900 hover:bg-black text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-slate-200 transition-all active:scale-95">
                <i class="fas fa-shopping-cart mr-2"></i> Track Sales
            </a>
            <a href="{{ route('admin.sales-team.dealer-support', $salesTeam) }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all active:scale-95">
                <i class="fas fa-headset mr-2"></i> Desk Support
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="space-y-8">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400 italic">Security Profile</div>
                <div class="p-8 space-y-6">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Business Email</p>
                        <p class="text-sm font-bold text-slate-900 underline underline-offset-4 decoration-indigo-200 decoration-2">{{ $salesTeam->email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Direct Extension</p>
                        <p class="text-sm font-mono font-bold text-slate-700">{{ $salesTeam->phone ?? 'Unlisted' }}</p>
                    </div>
                    <div class="pt-6 border-t border-slate-50 space-y-4">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-400 uppercase tracking-widest">Enrollment</span>
                            <span class="font-black text-slate-900 italic">{{ $salesTeam->created_at->format('d M, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-400 uppercase tracking-widest">Audit Sync</span>
                            <span class="font-black text-slate-900 italic">{{ $salesTeam->updated_at->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-900 rounded-[2rem] p-8 text-white shadow-2xl relative overflow-hidden">
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-4 italic relative z-10">Managed Portfolio</h3>
                <div class="relative z-10">
                    <p class="text-4xl font-black italic tracking-tighter">{{ is_array($assignedDealers) ? count($assignedDealers) : 0 }}</p>
                    <p class="text-xs font-bold text-indigo-300 uppercase tracking-widest">Active Dealer Assets</p>
                </div>
                <svg class="absolute -right-4 -bottom-4 w-24 h-24 text-white/5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Client Asset Portfolio</h3>
                    <span class="text-[10px] font-black bg-slate-900 text-white px-3 py-1 rounded-lg uppercase tracking-tighter shadow-sm italic">Primary Map</span>
                </div>
                <div class="overflow-x-auto p-4">
                    @if(is_array($assignedDealers) && count($assignedDealers) > 0)
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50">
                                    <th class="px-6 py-3 italic">Authorized Dealer</th>
                                    <th class="px-6 py-3">Invoiced Email</th>
                                    <th class="px-6 py-3 text-right">Intel</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($assignedDealers as $dealer)
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-black text-slate-800">{{ $dealer->company_name ?? $dealer->name }}</div>
                                            <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Liaison: {{ $dealer->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-bold text-slate-500 italic lowercase">{{ $dealer->email }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.customers.show', $dealer) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all inline-block shadow-sm border border-transparent hover:border-indigo-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="p-8 text-center text-slate-400 italic font-medium">No dealer assets assigned to this representative.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Recent Portfolio Transactions</h3>
                    <span class="text-[10px] font-black bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg uppercase tracking-tighter">Verified Stream</span>
                </div>
                <div class="overflow-x-auto p-4">
                    @if($orders->count() > 0)
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50">
                                    <th class="px-6 py-3">Order Node</th>
                                    <th class="px-6 py-3">Invoiced Value</th>
                                    <th class="px-6 py-3">Lifecycle</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-black text-slate-900">#{{ $order->order_number }}</div>
                                            <div class="text-[9px] text-slate-400 font-bold uppercase">{{ $order->created_at->format('d M, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-black text-slate-900 italic tracking-tighter">₹{{ number_format($order->total, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php $oCol = match($order->status) { 'completed' => 'bg-emerald-100 text-emerald-700', 'pending' => 'bg-amber-100 text-amber-700', default => 'bg-slate-100 text-slate-500' }; @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest {{ $oCol }}">{{ $order->status }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all inline-block">
                                                <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <p class="p-12 text-center text-slate-400 italic text-sm">No transaction stream detected for assigned dealers.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection