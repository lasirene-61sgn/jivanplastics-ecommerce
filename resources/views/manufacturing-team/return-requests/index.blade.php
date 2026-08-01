<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Requests - Manufacturing Portal</title>

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

        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    </style>
</head>

<body class="min-h-screen pb-20">

    <!-- Premium Header -->
    <header class="glass-header sticky top-0 z-50 px-4 sm:px-8">
        <div class="max-w-[1440px] mx-auto h-24 flex items-center justify-between">
            <div class="flex items-center gap-12">
                <a href="{{ route('manufacturing-team.dashboard') }}" class="flex items-center group">
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

        <!-- Page Header -->
        <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('manufacturing-team.dashboard') }}" class="flex items-center gap-1.5 text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-rose-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                        Dashboard
                    </a>
                    <span class="text-slate-300 text-xs">/</span>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Return Requests</span>
                </div>
                <h2 class="text-4xl font-black text-slate-900 uppercase tracking-tight">Return <span class="text-rose-600">Requests</span></h2>
                <p class="text-slate-500 font-medium mt-1">Manage all return requests for {{ $manufacturingTeam->factory_name }}</p>
            </div>

            <a href="{{ route('manufacturing-team.returns.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-rose-200 active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create Request
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 px-6 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 px-6 py-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-700 text-sm font-bold flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Stats Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Total Returns</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $returnRequestsCount }}</span>
                </div>
            </div>
            <!-- <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Pending</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $returnRequests->where('status', 'pending')->count() }}</span>
                </div>
            </div> -->
            <!-- <div class="bg-white p-5 rounded-[1.5rem] border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Completed</span>
                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $returnRequests->where('status', 'completed')->count() }}</span>
                </div>
            </div> -->
        </div>

        <!-- Return Requests Table -->
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">All Return <span class="text-rose-600">Requests</span></h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $returnRequests->total() }} total records</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Show</span>
                    <select onchange="window.location.href = this.value"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 outline-none focus:border-rose-500">
                        @foreach([10, 20, 30, 100] as $size)
                            <option value="{{ request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]) }}"
                                {{ request()->get('per_page', 20) == $size ? 'selected' : '' }}>{{ $size }} per page</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($returnRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-slate-50/30">
                                <th class="py-5 px-8 font-black">Order Ref</th>
                                <th class="py-5 px-4 font-black">Product</th>
                                <th class="py-5 px-4 font-black">Pieces</th>
                                <th class="py-5 px-4 font-black">Reason</th>
                                <th class="py-5 px-4 font-black text-center">Status</th>
                                <th class="py-5 px-8 font-black text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($returnRequests as $return)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="py-6 px-8">
                                    <div class="flex flex-col">
                                        @if($return->order)
                                            <span class="text-sm font-black text-slate-900 tracking-tight">#{{ $return->order->order_number }}</span>
                                        @elseif($return->request_number)
                                            <span class="text-sm font-black text-rose-600 tracking-tight">{{ $return->request_number }}</span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Mfg Direct</span>
                                        @else
                                            <span class="text-sm font-black text-slate-500 tracking-tight italic">Direct</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <div class="flex flex-col">
                                        @if($return->orderItem)
                                            <span class="text-sm font-black text-slate-900 tracking-tight">{{ $return->orderItem->product_name }}</span>
                                        @elseif($return->product)
                                            <span class="text-sm font-black text-slate-900 tracking-tight">{{ $return->product->name }}</span>
                                        @else
                                            <span class="text-sm font-black text-slate-500 tracking-tight">N/A</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-6 px-4">
                                    <span class="text-xs font-black text-slate-700 tracking-tight">{{ $return->pieces }} pcs</span>
                                </td>
                                <td class="py-6 px-4">
                                    <span class="text-xs font-medium text-slate-600 max-w-[160px] block truncate" title="{{ $return->reason }}">{{ $return->reason ?? '—' }}</span>
                                </td>
                                <td class="py-6 px-4 text-center">
                                    @php
                                        $statusColors = [
                                            'pending'    => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'processing' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'completed'  => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'rejected'   => 'bg-rose-50 text-rose-600 border-rose-100',
                                        ];
                                        $colorClass = $statusColors[$return->status] ?? 'bg-slate-50 text-slate-600 border-slate-100';
                                    @endphp
                                    <span class="px-3 py-1 rounded-lg border {{ $colorClass }} text-[10px] font-black uppercase tracking-widest">
                                        {{ $return->status }}
                                    </span>
                                </td>
                                <td class="py-6 px-8 text-right">
                                    <span class="text-[11px] font-black text-slate-500 tracking-tight">{{ $return->created_at->format('d M, Y') }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-8 bg-slate-50/30 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Showing {{ $returnRequests->firstItem() }}–{{ $returnRequests->lastItem() }} of {{ $returnRequests->total() }} results
                    </p>
                    <div>
                        {{ $returnRequests->links() }}
                    </div>
                </div>
            @else
                <div class="p-24 text-center">
                    <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-300 mx-auto mb-6">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-black text-slate-900 uppercase tracking-tight">No Return Requests</h4>
                    <p class="text-slate-400 text-sm mt-2 mb-8">You don't have any return requests yet.</p>
                    <a href="{{ route('manufacturing-team.returns.create') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-sm font-black uppercase tracking-widest transition-all shadow-lg shadow-rose-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Create First Request
                    </a>
                </div>
            @endif
        </div>
    </main>

</body>
</html>
