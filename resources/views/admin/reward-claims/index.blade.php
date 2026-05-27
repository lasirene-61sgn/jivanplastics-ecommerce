@extends('layouts.admin')

@section('title', 'Reward Claims - Admin Panel')

@section('header', 'Loyalty Fulfillment')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Reward Claims</h2>
            <p class="text-sm text-slate-500 font-medium italic">Review and process customer redemptions for points.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        @if($claims->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Member</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Reward Asset</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Cost</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Requested</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($claims as $claim)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $claim->customer->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium truncate max-w-[150px]">{{ $claim->customer->email }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-800">{{ $claim->reward->name }}</div>
                                    @if($claim->reward->type === 'product')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-tighter">Physical Item</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-sky-50 text-sky-700 border border-sky-100 uppercase tracking-tighter">Travel Package</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center px-3 py-1 bg-amber-50 rounded-xl border border-amber-100">
                                        <svg class="w-3 h-3 text-amber-500 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <span class="text-xs font-black text-amber-700 uppercase tracking-tighter">{{ $claim->reward->required_points }} PTS</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($claim->status) {
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'rejected' => 'bg-rose-100 text-rose-700',
                                            'fulfilled' => 'bg-indigo-600 text-white shadow-md',
                                            default => 'bg-slate-100 text-slate-500'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                        {{ $claim->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-xs font-bold text-slate-500 tracking-tight">
                                    {{ $claim->claimed_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.reward-claims.show', $claim) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                                        Process
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $claims->links() }}
            </div>
        @else
            <div class="p-20 text-center text-slate-400 font-medium italic">
                No redemption requests found.
            </div>
        @endif
    </div>
</div>
@endsection