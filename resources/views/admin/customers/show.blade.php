@extends('layouts.admin')

@section('title', 'Customer Details - Admin Panel')

@section('header', 'Customer Intelligence')

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-5">
            <div class="h-20 w-20 rounded-3xl {{ $customer->customer_type === 'dealer' ? 'bg-amber-600' : 'bg-indigo-600' }} flex items-center justify-center text-white text-4xl font-black shadow-2xl shadow-indigo-100">
                {{ substr($customer->name, 0, 1) }}
            </div>
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $customer->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $customer->customer_type === 'dealer' ? 'bg-amber-100 text-amber-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $customer->customer_type }} Account
                    </span>
                    @if($customer->is_active)
                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-green-100 text-green-700 uppercase tracking-widest">Active</span>
                    @else
                        <span class="px-3 py-1 rounded-full text-[10px] font-black bg-red-50 text-red-500 uppercase tracking-widest">Suspended</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="flex-1 md:flex-none px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5 active:scale-95 text-center">
                Edit Profile
            </a>
            <a href="{{ route('admin.customers.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 rounded-2xl text-sm font-bold hover:bg-slate-50 transition-all text-center">
                Return to Directory
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Personal Information</h3>
                </div>
                <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-indigo-500 uppercase">Contact Email</p>
                        <p class="text-base font-semibold text-slate-900 truncate">{{ $customer->email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-indigo-500 uppercase">Primary Phone</p>
                        <p class="text-base font-semibold text-slate-900">{{ $customer->phone ?? 'Not Provided' }}</p>
                    </div>
                    @if($customer->customer_type === 'dealer')
                    <div class="sm:col-span-2 p-5 bg-amber-50 rounded-2xl border border-amber-100">
                        <p class="text-[10px] font-bold text-amber-600 uppercase mb-2">Registered Company</p>
                        <p class="text-xl font-black text-slate-900 tracking-tight">{{ $customer->company_name ?? 'N/A' }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Logistics & Address</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Full Address</p>
                        <p class="text-base font-medium text-slate-600 leading-relaxed mt-1">{{ $customer->address ?? 'No address registered' }}</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">City</p>
                            <p class="text-sm font-bold text-slate-900">{{ $customer->city ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">State</p>
                            <p class="text-sm font-bold text-slate-900">{{ $customer->state ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Zip</p>
                            <p class="text-sm font-bold text-slate-900 font-mono">{{ $customer->zip_code ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Country</p>
                            <p class="text-sm font-bold text-slate-900">{{ $customer->country ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-2xl">
                <h3 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-6">Internal Audit</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Join Date:</span>
                        <span class="text-xs font-bold">{{ $customer->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400">Last Active:</span>
                        <span class="text-xs font-bold">{{ $customer->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-800">
                    <p class="text-[10px] text-slate-500 italic leading-relaxed">
                        User profile verified for Secure Transactions via StoreHub Admin Protocol.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection