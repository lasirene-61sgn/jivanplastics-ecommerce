<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sales Portal - ' . config('app.name', 'Laravel'))</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Instrument Sans', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900 antialiased overflow-x-hidden">

    <div class="lg:hidden flex items-center justify-between bg-white border-b border-slate-200 px-6 py-4 sticky top-0 z-[50]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="font-black text-slate-900 uppercase tracking-tighter italic text-lg">SalesHub</span>
        </div>
        <button @click="sidebarOpen = true" class="p-2 text-slate-500 hover:text-indigo-600 transition-colors">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
    </div>

    <div class="flex min-h-screen">
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:sticky top-0 left-0 z-[60] w-72 h-screen bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none">
            
            <div class="p-8 border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white shadow-xl italic font-black text-xl">S</div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-[0.2em]">Sales Portal</h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Authorized Area</span>
                        </div>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-8 overflow-y-auto custom-scrollbar">
                <div class="space-y-1">
                    <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 italic">Command Center</p>
                    
                    <a href="{{ route('sales-team.dashboard') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('sales-team.dashboard') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i class="fas fa-th-large w-5 {{ request()->routeIs('sales-team.dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('sales-team.orders.index') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('sales-team.orders.*') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i class="fas fa-shopping-bag w-5 {{ request()->routeIs('sales-team.orders.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                        Orders Manifest
                    </a>

                    <a href="{{ route('sales-team.customers.index') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('sales-team.customers.*') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i class="fas fa-users w-5 {{ request()->routeIs('sales-team.customers.*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                        Client Portfolio
                    </a>
                </div>

                <div class="mt-10 space-y-1">
                    <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 italic">Assistance</p>
                    
                    <a href="{{ route('sales-team.dealer-support') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('sales-team.dealer-support') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i class="fas fa-headset w-5 {{ request()->routeIs('sales-team.dealer-support') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                        Basic Desk
                    </a>

                    <a href="{{ route('sales-team.enhanced-dealer-support') }}" 
                       class="flex items-center gap-3 px-4 py-3.5 rounded-2xl text-sm font-bold transition-all group {{ request()->routeIs('sales-team.enhanced-dealer-support') ? 'bg-indigo-600 text-white shadow-xl shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
                        <i class="fas fa-desktop w-5 {{ request()->routeIs('sales-team.enhanced-dealer-support') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}"></i>
                        Enhanced Support
                    </a>
                </div>
            </nav>

            <div class="p-6 border-t border-slate-50">
                <form action="{{ route('sales-team.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-4 rounded-2xl bg-rose-50 text-rose-600 font-black text-[11px] uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                        <i class="fas fa-sign-out-alt"></i>
                        Secure Terminate
                    </button>
                </form>
            </div>
        </aside>

        <div x-show="sidebarOpen" 
             x-transition:opacity
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[55] lg:hidden" x-cloak></div>

        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-8 py-6 hidden lg:flex items-center justify-between sticky top-0 z-40">
                <h1 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">
                    @yield('header', 'Sales Terminal')
                </h1>
                
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active Session</p>
                        <p class="text-xs font-bold text-slate-600">{{ now()->format('l, d M Y') }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                        <i class="fas fa-user-circle text-lg"></i>
                    </div>
                </div>
            </header>

            <div class="flex-1 px-6 lg:px-12 py-10 overflow-y-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Simple handler to ensure clicking sidebar links on mobile closes the drawer
        document.querySelectorAll('aside nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    // Alpine.js will handle this via data binding if needed, 
                    // but we ensure it for standard links
                }
            });
        });
    </script>
</body>
</html>