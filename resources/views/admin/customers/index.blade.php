@extends('layouts.admin')

@section('title', 'Customers - Admin Panel')

@section('header', 'User Directory')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">All Customers</h2>
            <p class="text-sm text-slate-500">Overview of all registered individuals and business partners.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex bg-slate-100 p-1 rounded-xl mr-2">
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 text-xs font-bold rounded-lg bg-white text-indigo-600 shadow-sm">All</a>
                <a href="{{ route('admin.customers.dealers') }}" class="px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:text-indigo-600 transition-colors">Dealers</a>
                <a href="{{ route('admin.customers.individuals') }}" class="px-4 py-2 text-xs font-bold rounded-lg text-slate-600 hover:text-indigo-600 transition-colors">Individuals</a>
            </div>
            <a href="{{ route('admin.customers.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add New Customer
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg flex items-center shadow-sm text-sm font-medium italic">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($customers->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Contact Info</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Account Type</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold border border-slate-200 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4 truncate max-w-[150px]">
                                            <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors truncate">{{ $customer->name }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">UID: {{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="text-slate-600 font-medium">{{ $customer->email }}</div>
                                    <div class="text-xs text-slate-400">{{ $customer->phone ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($customer->customer_type === 'dealer')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-50 text-amber-700 border border-amber-100 tracking-tighter">
                                            B2B Partner
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase bg-sky-50 text-sky-700 border border-sky-100 tracking-tighter">
                                            Individual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 italic">
                                    {{ $customer->company_name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($customer->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-600 uppercase tracking-tighter">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="View Profile">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit Profile">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Archive this customer?')">
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
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center">
                {{ $customers->links() }}
            </div>
        @else
            <div class="p-20 text-center">
                <p class="text-slate-500 font-medium">No customers found. <a href="{{ route('admin.customers.create') }}" class="text-indigo-600 hover:underline">Start registering today</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection