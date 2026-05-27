<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50/50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased text-slate-900">
    <div x-data="{ sidebarOpen: false }">
        
        <div x-show="sidebarOpen" class="fixed inset-0 z-50 flex md:hidden" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>
            <div class="relative flex flex-col w-full max-w-xs bg-white shadow-xl">
                <div class="flex items-center h-20 px-6 border-b border-gray-100">
                    <span class="text-xl font-bold tracking-tight text-slate-900">V4 Kitchen<span class="text-indigo-600">Partner</span></span>
                </div>
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                    @include('partials.admin-nav-links')
                </nav>
            </div>
        </div>

        <div class="hidden md:flex md:w-72 md:flex-col md:fixed md:inset-y-0 border-r border-gray-200 bg-white">
            <div class="flex items-center h-20 px-8">
                <span class="text-2xl font-bold tracking-tight text-slate-900"><img src="{{asset('images/logo.png')}}" style="height:90px;width:90px;"></span></span>
            </div>
            <div class="flex-1 flex flex-col overflow-y-auto px-4 py-4">
                <nav class="space-y-1">
                    @include('partials.admin-nav-links')
                </nav>
            </div>
        </div>

        <div class="md:pl-72 flex flex-col flex-1">
            <header class="sticky top-0 z-10 flex h-20 bg-white/80 backdrop-blur-md border-b border-gray-200">
                <button @click="sidebarOpen = true" class="px-6 text-slate-500 md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="flex-1 flex justify-between px-8">
                    <div class="flex items-center">
                        <h2 class="text-lg font-semibold text-slate-800">@yield('header')</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:block text-right">
                            <p class="text-sm font-medium text-slate-900">Admin User</p>
                            <p class="text-xs text-slate-500">Super Admin</p>
                        </div>
                        <button onclick="document.getElementById('logout-form').submit();" class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:bg-red-50 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </div>
                </div>
            </header>

            <main class="p-8">
                @yield('content')
            </main>
        </div>
    </div>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">@csrf</form>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>