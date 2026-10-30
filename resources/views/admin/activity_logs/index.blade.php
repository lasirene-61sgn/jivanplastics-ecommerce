@extends('layouts.admin')

@section('title', 'Activity Logs & User Tracking')
@section('header', 'Activity Logs & Tracking')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">System Activity & User Tracking</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Monitor B2B dealer and B2C customer logins, device usage, order frequency, and administrative actions.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-black rounded-xl border border-indigo-100 flex items-center gap-1.5 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Live Tracking Active
            </span>
        </div>
    </div>

    {{-- Top KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: B2B Dealers --}}
        <a href="{{ route('admin.activity-logs.index', ['tab' => 'b2b']) }}" 
           class="bg-white rounded-2xl p-5 border {{ $activeTab === 'b2b' ? 'border-indigo-500 ring-2 ring-indigo-100' : 'border-slate-200' }} shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-wider rounded-md border border-indigo-100">B2B Wholesale</span>
            </div>
            <div class="space-y-1">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['b2b_logins']) }}</span>
                    <span class="text-xs font-bold text-slate-500">Logins</span>
                </div>
                <div class="text-xs text-slate-500 flex items-center justify-between pt-2 border-t border-slate-100 font-medium">
                    <span>{{ $stats['b2b_active_dealers'] }} Active Dealers</span>
                    <span class="font-bold text-indigo-600">{{ $stats['b2b_orders_count'] }} Orders</span>
                </div>
            </div>
        </a>

        {{-- Card 2: B2C Retail Customers --}}
        <a href="{{ route('admin.activity-logs.index', ['tab' => 'b2c']) }}" 
           class="bg-white rounded-2xl p-5 border {{ $activeTab === 'b2c' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200' }} shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider rounded-md border border-emerald-100">B2C Retail</span>
            </div>
            <div class="space-y-1">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['b2c_logins']) }}</span>
                    <span class="text-xs font-bold text-slate-500">Logins</span>
                </div>
                <div class="text-xs text-slate-500 flex items-center justify-between pt-2 border-t border-slate-100 font-medium">
                    <span>{{ $stats['b2c_active_count'] }} Active Retail</span>
                    <span class="font-bold text-emerald-600">{{ $stats['b2c_orders_count'] }} Orders</span>
                </div>
            </div>
        </a>

        {{-- Card 3: Device Breakdown --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-black text-slate-500 uppercase tracking-wider">Device Breakdown</span>
                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-md">Clients</span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center pt-1">
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase">💻 PC</div>
                    <div class="text-base font-black text-slate-800">{{ $stats['pc_count'] }}</div>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase">📱 Mobile</div>
                    <div class="text-base font-black text-slate-800">{{ $stats['mobile_count'] }}</div>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] font-bold text-slate-400 uppercase">📲 Tablet</div>
                    <div class="text-base font-black text-slate-800">{{ $stats['tablet_count'] }}</div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Activity / Admin Actions --}}
        <a href="{{ route('admin.activity-logs.index', ['tab' => 'admin']) }}" 
           class="bg-white rounded-2xl p-5 border {{ $activeTab === 'admin' ? 'border-purple-500 ring-2 ring-purple-100' : 'border-slate-200' }} shadow-xs hover:shadow-md transition-all group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <span class="px-2 py-0.5 bg-purple-50 text-purple-700 text-[10px] font-black uppercase tracking-wider rounded-md border border-purple-100">Admin Control</span>
            </div>
            <div class="space-y-1">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-900">{{ number_format($stats['admin_actions']) }}</span>
                    <span class="text-xs font-bold text-slate-500">Admin Actions</span>
                </div>
                <div class="text-xs text-slate-500 flex items-center justify-between pt-2 border-t border-slate-100 font-medium">
                    <span>{{ $activeAdmins->count() }} Admins Tracked</span>
                    <span class="font-bold text-purple-600">{{ number_format($stats['total_logs']) }} Logs Total</span>
                </div>
            </div>
        </a>
    </div>

    {{-- Main Tabs Navigation --}}
    <div class="border-b border-slate-200 flex items-center gap-2 overflow-x-auto pb-px">
        <a href="{{ route('admin.activity-logs.index', ['tab' => 'all']) }}" 
           class="px-4 py-3 text-xs uppercase tracking-wider font-black flex items-center gap-2 border-b-2 transition-all whitespace-nowrap {{ $activeTab === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            <span>All System Logs</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'all' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">{{ $logs->total() }}</span>
        </a>

        <a href="{{ route('admin.activity-logs.index', ['tab' => 'b2b']) }}" 
           class="px-4 py-3 text-xs uppercase tracking-wider font-black flex items-center gap-2 border-b-2 transition-all whitespace-nowrap {{ $activeTab === 'b2b' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span>B2B Regular Logins & Ordering</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'b2b' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">{{ $b2bCustomers->total() }} Dealers</span>
        </a>

        <a href="{{ route('admin.activity-logs.index', ['tab' => 'b2c']) }}" 
           class="px-4 py-3 text-xs uppercase tracking-wider font-black flex items-center gap-2 border-b-2 transition-all whitespace-nowrap {{ $activeTab === 'b2c' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span>B2C Retail Logins & Orders</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'b2c' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">{{ $b2cCustomers->total() }} Users</span>
        </a>

        <a href="{{ route('admin.activity-logs.index', ['tab' => 'admin']) }}" 
           class="px-4 py-3 text-xs uppercase tracking-wider font-black flex items-center gap-2 border-b-2 transition-all whitespace-nowrap {{ $activeTab === 'admin' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <span>Admin Sessions & History</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $activeTab === 'admin' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">{{ $activeAdmins->count() }} Admins</span>
        </a>
    </div>

    {{-- TAB CONTENT 1: B2B DEALERS REGULAR LOGINS & ORDERING TRACKER --}}
    @if($activeTab === 'b2b')
        <div class="space-y-6">
            {{-- Filter & Search for B2B --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                    <input type="hidden" name="tab" value="b2b">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" 
                               name="b2b_search" 
                               value="{{ request('b2b_search') }}" 
                               placeholder="Search dealer by name, company, email, phone..." 
                               class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <select name="b2b_frequency" class="text-xs rounded-xl border border-slate-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium">
                            <option value="all" {{ request('b2b_frequency') === 'all' ? 'selected' : '' }}>All B2B Dealers</option>
                            <option value="regular" {{ request('b2b_frequency') === 'regular' ? 'selected' : '' }}>Frequent Buyers (5+ Logins)</option>
                        </select>

                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-colors shadow-xs">
                            Filter
                        </button>
                        @if(request('b2b_search') || request('b2b_frequency'))
                            <a href="{{ route('admin.activity-logs.index', ['tab' => 'b2b']) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- B2B Dealers Summary Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/50">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span>B2B Dealers Logins & Order Engagement</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $b2bCustomers->total() }} Total</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Track how frequently dealers log in, what devices they use, and their total order volumes.</p>
                    </div>
                    <a href="{{ route('admin.customers.dealers') }}" class="text-xs font-black text-indigo-600 hover:text-indigo-700 hover:underline flex items-center gap-1 self-start sm:self-auto">
                        <span>Manage Dealers</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Dealer / Company</th>
                                <th class="px-6 py-4 text-center">Login Frequency</th>
                                <th class="px-6 py-4">Last Device & Client</th>
                                <th class="px-6 py-4 text-center">Orders Count</th>
                                <th class="px-6 py-4 text-right">Total Ordered (₹)</th>
                                <th class="px-6 py-4 text-center">Engagement Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($b2bCustomers as $dealer)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    {{-- Dealer / Company --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 font-black flex items-center justify-center text-sm border border-indigo-100">
                                                {{ strtoupper(substr($dealer->company_name ?: $dealer->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-black text-slate-900 uppercase">
                                                    {{ $dealer->company_name ?: $dealer->name }}
                                                </div>
                                                <div class="text-[11px] text-slate-500">Contact: {{ $dealer->name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $dealer->email }} • {{ $dealer->phone ?? 'No Phone' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Login Frequency --}}
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="px-3 py-1 rounded-full text-xs font-black bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                {{ number_format($dealer->login_count) }} Logins
                                            </span>
                                            <span class="text-[10px] text-slate-400 mt-1">
                                                {{ $dealer->last_login_at ? $dealer->last_login_at->diffForHumans() : 'Never logged in' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Last Device & Client --}}
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                                                @if(str_contains(strtolower($dealer->last_device ?? ''), 'mobile'))
                                                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[10px] font-black border border-amber-100">📱 Mobile</span>
                                                @elseif(str_contains(strtolower($dealer->last_device ?? ''), 'tablet'))
                                                    <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[10px] font-black border border-purple-100">📲 Tablet</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-black border border-blue-100">💻 Desktop PC</span>
                                                @endif
                                                <span class="text-[11px] text-slate-600">{{ $dealer->last_device ?? 'Desktop (Chrome)' }}</span>
                                            </div>
                                            <div class="font-mono text-[10px] text-slate-400">
                                                IP: {{ $dealer->current_login_ip ?? '127.0.0.1' }}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Orders Count --}}
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-slate-900 block">
                                            {{ $dealer->orders_count }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">Orders placed</span>
                                    </td>

                                    {{-- Total Ordered --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="text-xs font-black text-slate-900">
                                            ₹{{ number_format((float)$dealer->orders_sum_total, 2) }}
                                        </div>
                                        @if($dealer->orders_count > 0)
                                            <div class="text-[10px] text-slate-400">
                                                Avg: ₹{{ number_format((float)$dealer->orders_sum_total / $dealer->orders_count, 2) }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Engagement Status --}}
                                    <td class="px-6 py-4 text-center">
                                        @if($dealer->login_count >= 10 || $dealer->orders_count >= 5)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Frequent Buyer
                                            </span>
                                        @elseif($dealer->login_count >= 2 || $dealer->orders_count >= 1)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                Active Dealer
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-50 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Occasional
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.customers.show', $dealer) }}" 
                                               class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                                                Profile
                                            </a>
                                            <a href="{{ route('admin.orders.index', ['customer_id' => $dealer->id]) }}" 
                                               class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-bold transition-colors">
                                                Orders
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">
                                        No B2B dealers found matching your query.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination for B2B --}}
                @if($b2bCustomers->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $b2bCustomers->links() }}
                    </div>
                @endif
            </div>

            {{-- Recent B2B Activity Feed --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        Recent B2B Activity Logs (Logins, Orders, and Updates)
                    </h3>
                    <span class="text-xs text-slate-400 font-bold">Showing filtered B2B logs</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Time</th>
                                <th class="px-6 py-4">Dealer</th>
                                <th class="px-6 py-4 text-center">Action</th>
                                <th class="px-6 py-4">Item / Target</th>
                                <th class="px-6 py-4">Details</th>
                                <th class="px-6 py-4">Device & IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logs->where('user_type', 'b2b') as $log)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-slate-800">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-black text-slate-900">
                                            {{ $log->customer->name ?? ($log->details['name'] ?? 'B2B Dealer') }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $log->customer->company_name ?? ($log->details['company_name'] ?? '') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($log->action === 'login')
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700">Login</span>
                                        @elseif($log->action === 'logout')
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-700">Logout</span>
                                        @elseif($log->action === 'order_placed')
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700">Order Placed</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-700">{{ $log->action }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-slate-700">{{ $log->model_type }}</span>
                                        @if($log->model_id)
                                            <span class="text-[10px] text-slate-400">#{{ $log->model_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->action === 'order_placed' && isset($log->details['order_number']))
                                            <div class="text-xs font-black text-emerald-700">Order #{{ $log->details['order_number'] }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold">Total: ₹{{ number_format((float)($log->details['total'] ?? 0), 2) }}</div>
                                        @elseif($log->action === 'login')
                                            <span class="text-xs text-slate-600 font-bold">Logged in via {{ $log->details['channel'] ?? 'Web' }}</span>
                                        @else
                                            <span class="text-xs text-slate-600">{{ $log->details['name'] ?? 'Action performed' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 inline-block">
                                            {{ $log->ip_address ?? '127.0.0.1' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                            @if(($log->details['device'] ?? '') === 'Mobile')
                                                <span>📱 Mobile</span>
                                            @elseif(($log->details['device'] ?? '') === 'Tablet')
                                                <span>📲 Tablet</span>
                                            @else
                                                <span>💻 PC</span>
                                            @endif
                                            <span>({{ $log->details['browser'] ?? 'Browser' }})</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-xs">No B2B activity logs recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB CONTENT 2: B2C RETAIL CUSTOMERS TRACKER --}}
    @if($activeTab === 'b2c')
        <div class="space-y-6">
            {{-- Filter & Search for B2C --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-stretch md:items-center justify-between">
                    <input type="hidden" name="tab" value="b2c">
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" 
                               name="b2c_search" 
                               value="{{ request('b2c_search') }}" 
                               placeholder="Search B2C customer by name, email, phone..." 
                               class="w-full pl-10 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-colors shadow-xs">
                            Filter
                        </button>
                        @if(request('b2c_search'))
                            <a href="{{ route('admin.activity-logs.index', ['tab' => 'b2c']) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- B2C Retail Customers Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/50">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span>B2C Retail Customers Logins & Orders</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $b2cCustomers->total() }} Retail</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Track individual end-consumer logins, device preferences, and purchase frequency.</p>
                    </div>
                    <a href="{{ route('admin.customers.individuals') }}" class="text-xs font-black text-emerald-600 hover:text-emerald-700 hover:underline flex items-center gap-1 self-start sm:self-auto">
                        <span>Manage Retail Customers</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Customer Details</th>
                                <th class="px-6 py-4 text-center">Total Logins</th>
                                <th class="px-6 py-4">Device & Browser</th>
                                <th class="px-6 py-4 text-center">Orders</th>
                                <th class="px-6 py-4 text-right">Total Spent (₹)</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($b2cCustomers as $retail)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 font-black flex items-center justify-center text-sm border border-emerald-100">
                                                {{ strtoupper(substr($retail->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="text-xs font-black text-slate-900">{{ $retail->name }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono">{{ $retail->email }} • {{ $retail->phone ?? 'No Phone' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            {{ number_format($retail->login_count) }} Logins
                                        </span>
                                        <span class="text-[10px] text-slate-400 block mt-1">
                                            {{ $retail->last_login_at ? $retail->last_login_at->diffForHumans() : 'Never' }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-slate-700">
                                            @if(str_contains(strtolower($retail->last_device ?? ''), 'mobile'))
                                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[10px] font-black">📱 Mobile</span>
                                            @elseif(str_contains(strtolower($retail->last_device ?? ''), 'tablet'))
                                                <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[10px] font-black">📲 Tablet</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-black">💻 PC</span>
                                            @endif
                                            <span class="text-[11px] text-slate-600">{{ $retail->last_device ?? 'Desktop (Chrome)' }}</span>
                                        </div>
                                        <div class="font-mono text-[10px] text-slate-400 mt-0.5">
                                            IP: {{ $retail->current_login_ip ?? '127.0.0.1' }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="text-sm font-black text-slate-900 block">{{ $retail->orders_count }}</span>
                                        <span class="text-[10px] text-slate-400">Orders placed</span>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="text-xs font-black text-slate-900">
                                            ₹{{ number_format((float)$retail->orders_sum_total, 2) }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.customers.show', $retail) }}" 
                                               class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">
                                                Profile
                                            </a>
                                            <a href="{{ route('admin.orders.index', ['customer_id' => $retail->id]) }}" 
                                               class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-bold transition-colors">
                                                Orders
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-sm">
                                        No B2C customers found matching your search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($b2cCustomers->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $b2cCustomers->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- TAB CONTENT 3: ADMIN SESSIONS & HISTORY --}}
    @if($activeTab === 'admin')
        <div class="space-y-6">
            {{-- Active Admins Cards --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Recent Admin Logins & Active Sessions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($activeAdmins as $admin)
                    <div class="p-4 rounded-xl border {{ $admin->id === auth('admin')->id() ? 'border-purple-200 bg-purple-50/50' : 'border-slate-100 bg-slate-50' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-black text-slate-800 text-sm">{{ $admin->name }}</span>
                            @if($admin->id === auth('admin')->id())
                                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[10px] font-black rounded uppercase">You</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <p><span class="font-semibold text-slate-600">Last Login:</span> {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Unknown' }}</p>
                            <p><span class="font-semibold text-slate-600">Current IP:</span> <span class="font-mono bg-white px-1.5 py-0.5 rounded border border-slate-200">{{ $admin->current_login_ip ?? 'Unknown' }}</span></p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- TAB CONTENT 4: ALL / COMPREHENSIVE ACTIVITY LOGS (Always visible or on tab=all) --}}
    @if($activeTab === 'all' || $activeTab === 'admin')
        <div class="space-y-6">
            {{-- Multi-Filter Bar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="space-y-4">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        {{-- Search Input --}}
                        <div class="lg:col-span-2">
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Search Keywords</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search }}" 
                                       placeholder="Name, email, IP, order #, device..." 
                                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        </div>

                        {{-- User Type Filter --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">User Category</label>
                            <select name="user_type" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium">
                                <option value="all" {{ $userTypeFilter === 'all' ? 'selected' : '' }}>All Users</option>
                                <option value="b2b" {{ $userTypeFilter === 'b2b' ? 'selected' : '' }}>B2B (Dealers)</option>
                                <option value="b2c" {{ $userTypeFilter === 'b2c' ? 'selected' : '' }}>B2C (Retail)</option>
                                <option value="admin" {{ $userTypeFilter === 'admin' ? 'selected' : '' }}>Admin Team</option>
                            </select>
                        </div>

                        {{-- Action Filter --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Action Type</label>
                            <select name="action" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium">
                                <option value="all" {{ $actionFilter === 'all' ? 'selected' : '' }}>All Actions</option>
                                <option value="login" {{ $actionFilter === 'login' ? 'selected' : '' }}>Logins</option>
                                <option value="logout" {{ $actionFilter === 'logout' ? 'selected' : '' }}>Logouts</option>
                                <option value="order_placed" {{ $actionFilter === 'order_placed' ? 'selected' : '' }}>Order Placed</option>
                                <option value="created" {{ $actionFilter === 'created' ? 'selected' : '' }}>Created</option>
                                <option value="updated" {{ $actionFilter === 'updated' ? 'selected' : '' }}>Updated</option>
                                <option value="deleted" {{ $actionFilter === 'deleted' ? 'selected' : '' }}>Deleted</option>
                            </select>
                        </div>

                        {{-- Device Filter --}}
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Device Type</label>
                            <select name="device" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium">
                                <option value="all" {{ $deviceFilter === 'all' ? 'selected' : '' }}>All Devices</option>
                                <option value="PC" {{ $deviceFilter === 'PC' ? 'selected' : '' }}>💻 Desktop PC</option>
                                <option value="Mobile" {{ $deviceFilter === 'Mobile' ? 'selected' : '' }}>📱 Mobile</option>
                                <option value="Tablet" {{ $deviceFilter === 'Tablet' ? 'selected' : '' }}>📲 Tablet</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-bold">Date Range:</span>
                            <div class="inline-flex rounded-lg border border-slate-200 p-0.5 bg-slate-50 text-xs">
                                <button type="submit" name="date_range" value="all" class="px-2.5 py-1 rounded-md font-bold {{ $dateFilter === 'all' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">All Time</button>
                                <button type="submit" name="date_range" value="today" class="px-2.5 py-1 rounded-md font-bold {{ $dateFilter === 'today' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">Today</button>
                                <button type="submit" name="date_range" value="this_week" class="px-2.5 py-1 rounded-md font-bold {{ $dateFilter === 'this_week' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">This Week</button>
                                <button type="submit" name="date_range" value="this_month" class="px-2.5 py-1 rounded-md font-bold {{ $dateFilter === 'this_month' ? 'bg-white text-indigo-600 shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">This Month</button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-colors shadow-xs">
                                Apply Filters
                            </button>
                            @if($search || $userTypeFilter !== 'all' || $actionFilter !== 'all' || $deviceFilter !== 'all' || $dateFilter !== 'all')
                                <a href="{{ route('admin.activity-logs.index', ['tab' => $activeTab]) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors">
                                    Clear Filters
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- Full Logs Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                            <span>Detailed Action & Session History</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $logs->total() }} Total</span>
                        </h3>
                    </div>
                    <span class="text-xs text-slate-400 font-bold">Page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-6 py-4">Timestamp</th>
                                <th class="px-6 py-4">User / Actor</th>
                                <th class="px-6 py-4 text-center">Action</th>
                                <th class="px-6 py-4">Item / Target</th>
                                <th class="px-6 py-4">Details</th>
                                <th class="px-6 py-4">Device & IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    {{-- Time --}}
                                    <td class="px-6 py-4">
                                        <div class="text-xs font-bold text-slate-800">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                                    </td>

                                    {{-- User / Actor --}}
                                    <td class="px-6 py-4">
                                        @if($log->user_type === 'b2b')
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">
                                                    B2B
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-slate-900">
                                                        {{ $log->customer->name ?? ($log->details['name'] ?? 'B2B Dealer') }}
                                                    </div>
                                                    <div class="text-[10px] text-indigo-600 font-bold">
                                                        {{ $log->customer->company_name ?? ($log->details['company_name'] ?? 'Dealer Account') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($log->user_type === 'b2c')
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-black">
                                                    B2C
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-slate-900">
                                                        {{ $log->customer->name ?? ($log->details['name'] ?? 'Retail Customer') }}
                                                    </div>
                                                    <div class="text-[10px] text-emerald-600 font-bold">Individual / Retail</div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-black">
                                                    ADM
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-slate-900">
                                                        {{ $log->admin->name ?? ($log->details['name'] ?? 'Administrator') }}
                                                    </div>
                                                    <div class="text-[10px] text-purple-600 font-bold">Admin Team</div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $actionColors = [
                                                'login' => 'bg-blue-100 text-blue-700 border border-blue-200',
                                                'logout' => 'bg-slate-100 text-slate-700 border border-slate-200',
                                                'order_placed' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                                'created' => 'bg-green-100 text-green-700 border border-green-200',
                                                'updated' => 'bg-amber-100 text-amber-700 border border-amber-200',
                                                'deleted' => 'bg-rose-100 text-rose-700 border border-rose-200'
                                            ];
                                            $actionClass = $actionColors[$log->action] ?? 'bg-slate-100 text-slate-700';
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $actionClass }}">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                    </td>

                                    {{-- Item / Target --}}
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-bold text-slate-700">{{ $log->model_type }}</span>
                                        @if($log->model_id)
                                            <span class="text-[10px] text-slate-400">#{{ $log->model_id }}</span>
                                        @endif
                                    </td>

                                    {{-- Details --}}
                                    <td class="px-6 py-4">
                                        @if($log->action === 'order_placed' && isset($log->details['order_number']))
                                            <div class="text-xs font-black text-emerald-700">Order #{{ $log->details['order_number'] }}</div>
                                            <div class="text-[10px] text-slate-500 font-bold">Total: ₹{{ number_format((float)($log->details['total'] ?? 0), 2) }}</div>
                                        @elseif($log->action === 'login')
                                            <span class="text-xs text-slate-600 font-bold">Logged in via {{ $log->details['channel'] ?? 'Web' }}</span>
                                        @elseif($log->details && isset($log->details['name']))
                                            <span class="text-xs font-bold text-slate-800">{{ $log->details['name'] }}</span>
                                        @else
                                            <span class="text-xs italic text-slate-400">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Device & IP Address --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                                {{ $log->ip_address ?? '127.0.0.1' }}
                                            </span>

                                            @if($log->details && isset($log->details['device']))
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">
                                                    @if($log->details['device'] === 'Mobile')
                                                        <span>📱 Mobile</span>
                                                    @elseif($log->details['device'] === 'Tablet')
                                                        <span>📲 Tablet</span>
                                                    @else
                                                        <span>💻 PC</span>
                                                    @endif
                                                    <span class="text-slate-400">({{ $log->details['browser'] ?? 'Browser' }})</span>
                                                </span>
                                            @endif
                                        </div>

                                        @if($log->details && isset($log->details['pc_name']))
                                            <div class="text-[9px] text-slate-400 mt-1 truncate max-w-[180px]" title="{{ $log->details['pc_name'] }}">
                                                Host: {{ $log->details['pc_name'] }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-sm">
                                        No activity logs found matching the selected criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($logs->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
