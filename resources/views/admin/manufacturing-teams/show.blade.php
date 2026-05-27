@extends('layouts.admin')

@section('title', 'Manufacturing Team Details')

@section('header', 'Factory Profile')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="h-16 w-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-xl shadow-indigo-100">
                {{ substr($manufacturingTeam->factory_name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $manufacturingTeam->factory_name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $manufacturingTeam->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400' }}">
                    {{ $manufacturingTeam->is_active ? 'Account Active' : 'Account Suspended' }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.manufacturing-teams.edit', $manufacturingTeam) }}" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-amber-100 transition-all active:scale-95">Edit Profile</a>
            <a href="{{ route('admin.manufacturing-teams.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-50 transition-all">Exit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                <h3 class="text-xs font-bold text-indigo-500 uppercase tracking-widest mb-6">Contact & Logistics</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-12">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Primary Liaison</p>
                        <p class="text-lg font-bold text-slate-900">{{ $manufacturingTeam->contact_person }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Manufacturing Segment</p>
                        <p class="text-base font-bold text-slate-700 underline decoration-indigo-200 decoration-4 underline-offset-4">{{ $manufacturingTeam->manufacturing_unit_type ?? 'N/A' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Physical HQ Address</p>
                        <p class="text-base font-medium text-slate-600 leading-relaxed">{{ $manufacturingTeam->address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-indigo-900 rounded-2xl p-6 text-white shadow-xl shadow-indigo-100">
                <h3 class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest mb-4">Direct Connect</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 group cursor-pointer">
                        <div class="p-2 bg-indigo-800 rounded-lg"><i class="fas fa-envelope text-indigo-300"></i></div>
                        <span class="text-sm font-semibold truncate">{{ $manufacturingTeam->email }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-800 rounded-lg"><i class="fas fa-phone-alt text-indigo-300"></i></div>
                        <span class="text-sm font-semibold">{{ $manufacturingTeam->phone }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-tighter">System Timestamps</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Created:</span>
                        <span class="font-bold text-slate-900">{{ $manufacturingTeam->created_at->format('d M y') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-400">Modified:</span>
                        <span class="font-bold text-slate-900">{{ $manufacturingTeam->updated_at->format('d M y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection