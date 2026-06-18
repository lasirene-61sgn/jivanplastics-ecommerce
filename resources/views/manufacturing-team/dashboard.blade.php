<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manufacturing Portal - V4 Kitchen Partner</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --primary-red: #E31E24;
            --deep-red: #8B0000;
            --soft-bg: #F8FAFC;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--soft-bg);
            color: #0F172A;
            -webkit-font-smoothing: antialiased;
        }

        .premium-shadow {
            box-shadow: 0 20px 50px -12px rgba(0, 0, 0, 0.05);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        .nav-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(227, 30, 36, 0.08);
            border-color: var(--primary-red);
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>
</head>

<body class="min-h-screen pb-20">

    <!-- Premium Header -->
    <header class="glass-header sticky top-0 z-50 px-4 sm:px-8">
        <div class="max-w-[1440px] mx-auto h-24 flex items-center justify-between">
            <div class="flex items-center gap-12">
                <a href="#" class="flex items-center group">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-16 w-auto transition-transform group-hover:scale-105">
                </a>
                <div class="hidden md:block h-8 w-px bg-slate-200"></div>
                <div class="hidden md:block">
                    <h1 class="text-xl font-black text-slate-900 uppercase tracking-tighter">Manufacturing <span class="text-rose-600">Hub</span></h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] leading-tight">V4 Kitchen Partner</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-sm font-black text-slate-900">{{ $manufacturingTeam->factory_name }}</span>
                    <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        Active Unit
                    </span>
                </div>

                <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="p-3 bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 rounded-2xl transition-all duration-300 group">
                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
                <form id="logout-form" action="{{ route('manufacturing-team.logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-[1440px] mx-auto px-4 sm:px-8 py-10">

        <!-- Welcome Section -->
        <div class="mb-12">
            <h2 class="text-4xl font-black text-slate-900 uppercase tracking-tight mb-2">Production <span class="text-rose-600">Console</span></h2>
            <p class="text-slate-500 font-medium">Monitoring manufacturing flow for {{ $manufacturingTeam->factory_name }}</p>
        </div>

        <!-- Dashboard Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-6">
                <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Orders</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $orders->total() }}</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-6">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Pending Orders</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $orders->where('manufacturing_status', 'allocated')->count() }}</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-6 lg:col-span-2">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Team Contact</span>
                    <span class="text-lg font-black text-slate-900 tracking-tight block">{{ $manufacturingTeam->contact_person }}</span>
                    <span class="text-xs text-slate-500 font-medium">{{ $manufacturingTeam->phone }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-10">
            <!-- Orders Main Section -->
            <div class="xl:col-span-2 space-y-8">
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden" x-data="{ 
                    selectedCount: 0,
                    updateSelectedCount() {
                        this.selectedCount = document.querySelectorAll('.order-checkbox:checked').length;
                    }
                }">
                    <div class="p-8 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">
                                @if($tab === 'allocated')
                                Admin Allocated <span class="text-rose-600">Orders</span>
                                @elseif($tab === 'accepted')
                                Manufacturing Accepted <span class="text-rose-600">Orders</span>
                                @else
                                Completed <span class="text-rose-600">Orders</span>
                                @endif
                            </h3>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manage production cycles</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span x-show="selectedCount > 0" x-cloak class="px-3 py-1.5 bg-rose-50 text-rose-600 rounded-xl text-[10px] font-black border border-rose-100 uppercase tracking-widest">
                                <span x-text="selectedCount"></span> Selected
                            </span>
                        </div>
                    </div>

                    <!-- Tabs Section -->
                    <div class="px-8 pt-4 flex gap-4 border-b border-slate-50 overflow-x-auto">
                        <a href="{{ route('manufacturing-team.dashboard', ['tab' => 'allocated']) }}" class="pb-4 px-2 text-sm font-black tracking-tight {{ $tab === 'allocated' ? 'text-rose-600 border-b-2 border-rose-600' : 'text-slate-500 hover:text-slate-900' }} transition-colors whitespace-nowrap">
                            Admin Allocated Orders 
                            <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'allocated' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">{{ $allocatedCount }}</span>
                        </a>
                        <a href="{{ route('manufacturing-team.dashboard', ['tab' => 'accepted']) }}" class="pb-4 px-2 text-sm font-black tracking-tight {{ $tab === 'accepted' ? 'text-rose-600 border-b-2 border-rose-600' : 'text-slate-500 hover:text-slate-900' }} transition-colors whitespace-nowrap">
                            Manufacturing Accepted Orders
                            <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'accepted' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">{{ $acceptedCount }}</span>
                        </a>
                        <a href="{{ route('manufacturing-team.dashboard', ['tab' => 'completed']) }}" class="pb-4 px-2 text-sm font-black tracking-tight {{ $tab === 'completed' ? 'text-rose-600 border-b-2 border-rose-600' : 'text-slate-500 hover:text-slate-900' }} transition-colors whitespace-nowrap">
                            Completed Orders
                            <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'completed' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">{{ $completedCount }}</span>
                        </a>
                    </div>

                    @if($orders->count() > 0)
                    <form id="bulkActionForm" method="POST" class="p-0 m-0">
                        @csrf
                        @if($tab !== 'completed')
                        <!-- Bulk Action Toolbar -->
                        <div class="bg-slate-50/50 p-4 px-8 border-b border-slate-50 flex flex-wrap items-center gap-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" id="selectAll" class="peer hidden">
                                    <div class="w-6 h-6 border-2 border-slate-200 rounded-lg group-hover:border-rose-400 transition-colors peer-checked:bg-rose-600 peer-checked:border-rose-600 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Select All</span>
                            </label>

                            <div class="h-6 w-px bg-slate-200 mx-2 hidden sm:block"></div>
                            
                            @if($tab === 'allocated')
                            <div class="flex items-center gap-2">
                                <label for="bulk_dispatch_date" class="text-[10px] font-black text-slate-500 uppercase tracking-widest hidden md:block">Dispatch Date:</label>
                                <input type="date" id="bulk_dispatch_date" name="tentative_dispatch_date" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 focus:outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400" min="{{ date('Y-m-d') }}">
                            </div>

                            <button type="button" id="bulkAcceptBtn" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-emerald-200 active:scale-95 disabled:opacity-50 disabled:pointer-events-none" :disabled="selectedCount === 0">
                                Bulk Accept
                            </button>
                            @endif

                            @if($tab === 'accepted')
                            <div class="flex items-center bg-white border border-slate-200 rounded-xl px-2 ml-auto w-full sm:w-auto mt-2 sm:mt-0">
                                <select name="bulk_status" id="bulkStatusSelect" class="bg-transparent border-none text-[10px] font-black uppercase tracking-widest focus:ring-0 text-slate-600 py-2.5">
                                    <option value="">Choose Status</option>
                                    <option value="completed">Completed</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                                <button type="button" id="bulkUpdateBtn" class="ml-2 p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" :disabled="selectedCount === 0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/30">
                                        <th class="py-5 px-8"></th>
                                        <th class="py-5 px-4 font-black">Order Ref</th>
                                        <th class="py-5 px-4 font-black text-center">Status</th>
                                        <th class="py-5 px-4 font-black">Timeline</th>
                                        <th class="py-5 px-8 text-right font-black">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($orders as $order)
                                    <tr class="group hover:bg-slate-50/50 transition-colors">
                                        <td class="py-6 px-8">
                                            @if(in_array($order->manufacturing_status, ['allocated', 'processing']))
                                            <label class="cursor-pointer">
                                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox hidden peer" @change="updateSelectedCount()">
                                                <div class="w-6 h-6 border-2 border-slate-200 rounded-lg group-hover:border-rose-400 transition-colors peer-checked:bg-rose-600 peer-checked:border-rose-600 flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white scale-0 peer-checked:scale-100 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </label>
                                            @endif
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-900 tracking-tight">#{{ $order->order_number }}</span>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $order->customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-4 text-center">
                                            @php
                                            $statusColors = [
                                            'allocated' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'processing' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            ];
                                            $colorClass = $statusColors[$order->manufacturing_status] ?? 'bg-slate-50 text-slate-600 border-slate-100';
                                            @endphp
                                            <span class="px-3 py-1 rounded-lg border {{ $colorClass }} text-[10px] font-black uppercase tracking-widest">
                                                {{ $order->manufacturing_status }}
                                            </span>
                                        </td>
                                        <td class="py-6 px-4">
                                            <div class="flex flex-col">
                                                <span class="text-[11px] font-black text-slate-700 tracking-tight">{{ $order->allocated_at ? $order->allocated_at->format('d M, Y') : '---' }}</span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $order->allocated_at ? $order->allocated_at->format('H:i A') : 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-6 px-8 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('manufacturing-team.orders.show', $order) }}" class="p-2.5 bg-slate-100 hover:bg-slate-900 text-slate-600 hover:text-white rounded-xl transition-all">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                @if($order->manufacturing_status == 'allocated')
                                                <button type="button" class="accept-btn p-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl transition-all shadow-lg shadow-rose-100 active:scale-95" data-order-id="{{ $order->id }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="p-8 bg-slate-50/30 border-t border-slate-100">
                        {{ $orders->links() }}
                    </div>
                    @else
                    <div class="p-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-300 mx-auto mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">Zero Load Currently</h4>
                        <p class="text-slate-400 text-sm mt-2">Check back later for new order allocations</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar Info Section -->
            <div class="space-y-8">
                <!-- Team Card -->
                <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-slate-200">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-rose-600/30 blur-[60px] rounded-full"></div>
                    <div class="relative z-10 font-black tracking-tight text-3xl mb-8 uppercase">Unit <span class="text-rose-500">Specs</span></div>

                    <div class="space-y-6 relative z-10">
                        <div class="flex items-center gap-4 group">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center transition-colors group-hover:bg-rose-500">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Factory Name</span>
                                <span class="text-sm font-bold tracking-tight">{{ $manufacturingTeam->factory_name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 group">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center transition-colors group-hover:bg-rose-500">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Email Secure</span>
                                <span class="text-sm font-bold tracking-tight">{{ $manufacturingTeam->email }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 group">
                            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center transition-colors group-hover:bg-rose-500">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Unit Type</span>
                                <span class="text-sm font-bold tracking-tight">{{ $manufacturingTeam->manufacturing_unit_type ?? 'Standard' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Utilities Grid -->
                <!--<div class="grid grid-cols-2 gap-4">-->
                <!--    <a href="#" class="nav-card bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm block">-->
                <!--        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-900 mb-4 transition-colors">-->
                <!--            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                <!--                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />-->
                <!--            </svg>-->
                <!--        </div>-->
                <!--        <span class="text-[10px] font-black uppercase tracking-widest text-slate-900">Inventory</span>-->
                <!--    </a>-->
                <!--    <a href="#" class="nav-card bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm block">-->
                <!--        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-900 mb-4 transition-colors">-->
                <!--            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                <!--                <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />-->
                <!--            </svg>-->
                <!--        </div>-->
                <!--        <span class="text-[10px] font-black uppercase tracking-widest text-slate-900">Alerts</span>-->
                <!--    </a>-->
                <!--    <a href="#" class="nav-card bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm block">-->
                <!--        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-900 mb-4 transition-colors">-->
                <!--            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                <!--                <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />-->
                <!--            </svg>-->
                <!--        </div>-->
                <!--        <span class="text-[10px] font-black uppercase tracking-widest text-slate-900">Reports</span>-->
                <!--    </a>-->
                <!--    <a href="#" class="nav-card bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm block">-->
                <!--        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-900 mb-4 transition-colors">-->
                <!--            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                <!--                <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />-->
                <!--                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />-->
                <!--            </svg>-->
                <!--        </div>-->
                <!--        <span class="text-[10px] font-black uppercase tracking-widest text-slate-900">Settings</span>-->
                <!--    </a>-->
                <!--</div>-->
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkboxes
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.order-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    // Manual trigger for Alpine x-data update if needed, but here we use vanila for form submission
                });
            }

            // Bulk action validation helper
            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
            }

            // Bulk accept button
            const bulkAcceptBtn = document.getElementById('bulkAcceptBtn');
            if (bulkAcceptBtn) {
                bulkAcceptBtn.addEventListener('click', function() {
                    const orderIds = getSelectedIds();
                    const dispatchDate = document.getElementById('bulk_dispatch_date').value;
                    
                    if (orderIds.length === 0) return;
                    
                    if (!dispatchDate) {
                        alert('Please select a tentative dispatch date before accepting the orders.');
                        document.getElementById('bulk_dispatch_date').focus();
                        return;
                    }

                    if (confirm(`Verify: Move all selected orders to Active Manufacturing status with dispatch date ${dispatchDate}?`)) {
                        const form = document.getElementById('bulkActionForm');
                        form.action = '{{ route("manufacturing-team.orders.bulk-accept") }}';
                        form.submit();
                    }
                });
            }

            // Bulk update button
            const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
            if (bulkUpdateBtn) {
                bulkUpdateBtn.addEventListener('click', function() {
                    const orderIds = getSelectedIds();
                    const statusSelect = document.getElementById('bulkStatusSelect');
                    const selectedStatus = statusSelect.value;

                    if (orderIds.length === 0 || !selectedStatus) return;

                    if (confirm(`Action: Mark ${orderIds.length} orders as ${selectedStatus.toUpperCase()}?`)) {
                        const form = document.getElementById('bulkActionForm');

                        // Create hidden input for status
                        const statusInput = document.createElement('input');
                        statusInput.type = 'hidden';
                        statusInput.name = 'manufacturing_status';
                        statusInput.value = selectedStatus;
                        form.appendChild(statusInput);

                        form.action = '{{ route("manufacturing-team.orders.bulk-update") }}';
                        form.submit();
                    }
                });
            }

            // Individual accept buttons
            document.querySelectorAll('.accept-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    const dispatchDateInput = document.getElementById('bulk_dispatch_date');
                    const dispatchDate = dispatchDateInput ? dispatchDateInput.value : null;
                    
                    if (dispatchDateInput && !dispatchDate) {
                        alert('Please select a tentative dispatch date at the top before accepting.');
                        dispatchDateInput.focus();
                        return;
                    }
                    
                    if (confirm(`Process this order individually${dispatchDate ? ' with dispatch date ' + dispatchDate : ''}?`)) {
                        const form = document.getElementById('bulkActionForm');
                        form.action = '{{ route("manufacturing-team.orders.bulk-accept") }}';

                        // Clear existing check hidden inputs if any (form submission clears them)
                        // but specifically we want to ONLY send this one ID
                        const checkboxes = document.querySelectorAll('.order-checkbox');
                        checkboxes.forEach(cb => cb.checked = false);

                        const targetCb = document.querySelector(`.order-checkbox[value="${orderId}"]`);
                        if (targetCb) targetCb.checked = true;

                        form.submit();
                    }
                });
            });
        });
    </script>
</body>

</html>