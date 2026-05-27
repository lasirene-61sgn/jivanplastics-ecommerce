@extends('layouts.admin')

@section('title', 'Rewards - Admin Panel')

@section('header', 'Loyalty Portfolio')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Reward Assets</h2>
            <p class="text-sm text-slate-500 font-medium italic">Configure the catalog of items available for points redemption.</p>
        </div>
        <a href="{{ route('admin.rewards.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Create New Asset
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        @if($rewards->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Asset Name</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Category</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Threshold</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Visibility</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rewards as $reward)
                            <tr class="hover:bg-slate-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $reward->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium italic truncate max-w-[200px]">{{ Str::limit($reward->description, 40) }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($reward->type === 'product')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[9px] font-black bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-widest">Physical Item</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[9px] font-black bg-sky-50 text-sky-700 border border-sky-100 uppercase tracking-widest">Travel Package</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex items-center px-3 py-1 bg-amber-50 rounded-lg border border-amber-100">
                                        <svg class="w-3 h-3 text-amber-500 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <span class="text-xs font-black text-amber-700 uppercase tracking-tighter">{{ $reward->required_points }} PTS</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($reward->is_active)
                                        <span class="inline-flex items-center text-green-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                                            <span class="text-[10px] font-black uppercase tracking-widest">Active</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-slate-300 italic">
                                            <span class="text-[10px] font-black uppercase tracking-widest">Inactive</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.rewards.edit', $reward) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Edit Asset">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </a>
                                        <form action="{{ route('admin.rewards.destroy', $reward) }}" method="POST" class="inline" onsubmit="return confirm('Erase this reward from the catalog?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
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
                {{ $rewards->links() }}
            </div>
        @else
            <div class="p-20 text-center">
                <p class="text-slate-400 italic">The reward catalog is currently empty.</p>
                <a href="{{ route('admin.rewards.create') }}" class="mt-4 inline-block text-indigo-600 font-bold hover:underline tracking-tight uppercase text-xs">Register first asset →</a>
            </div>
        @endif
    </div>
</div>
@endsection