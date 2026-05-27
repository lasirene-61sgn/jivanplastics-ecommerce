@extends('layouts.admin')

@section('title', 'Sales Team Management - Admin Panel')

@section('header', 'Sales Force Command')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Sales Team Directory</h2>
            <p class="text-sm text-slate-500 font-medium italic">Manage internal representatives and their assigned dealer portfolios.</p>
        </div>
        <a href="{{ route('admin.sales-team.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Add Team Member
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm text-sm font-bold flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-xl shadow-sm text-sm font-bold flex justify-between items-center">
            <span><i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400">Authorized Personnel</div>
        
        @if($salesTeams->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/30 border-b border-slate-100">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Representative</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Unit</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Portfolio</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Joined</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($salesTeams as $salesTeam)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black border border-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                            {{ substr($salesTeam->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $salesTeam->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono tracking-tight">{{ $salesTeam->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-black uppercase tracking-tighter text-slate-600 bg-slate-100 px-3 py-1 rounded-lg">
                                        {{ $salesTeam->department }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @php $assignedDealers = $salesTeam->getAssignedDealersList(); @endphp
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm font-black text-slate-900">{{ is_array($assignedDealers) ? count($assignedDealers) : 0 }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase">Dealers</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center text-sm">
                                    @if($salesTeam->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-green-100 text-green-700 tracking-widest border border-green-200">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-100 text-slate-400 tracking-widest border border-slate-200">Suspended</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-xs font-bold text-slate-500 italic">
                                    {{ $salesTeam->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <a href="{{ route('admin.sales-team.show', $salesTeam) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="View Profile">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('admin.sales-team.edit', $salesTeam) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit Access">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <a href="{{ route('admin.sales-team.orders', $salesTeam) }}" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg transition-all" title="Sales Performance">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                        </a>
                                        <form action="{{ route('admin.sales-team.destroy', $salesTeam) }}" method="POST" class="inline" onsubmit="return confirm('Erase this team member profile?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
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

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $salesTeams->links() }}
            </div>
        @else
            <div class="p-20 text-center">
                <div class="mx-auto w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-300">
                    <i class="fas fa-users-slash fa-2x"></i>
                </div>
                <p class="text-slate-500 font-bold tracking-tight">No sales representatives found.</p>
                <a href="{{ route('admin.sales-team.create') }}" class="mt-4 inline-block text-xs font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 underline underline-offset-4">Add your first member →</a>
            </div>
        @endif
    </div>
</div>
@endsection