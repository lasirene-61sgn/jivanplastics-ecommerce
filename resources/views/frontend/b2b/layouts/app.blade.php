<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dealer Portal - V4 Kitchen Partner')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    @if (file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --primary-red: #E31E24;
            --deep-red: #8B0000;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }

        /* Modern Navbar */
        .navbar {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(25px) saturate(200%);
            -webkit-backdrop-filter: blur(25px) saturate(200%);
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 50px rgba(0, 0, 0, 0.02);
            height: 90px;
            display: flex;
            align-items: center;
        }

        .navbar svg {
            width: 1.1rem !important;
            height: 1.1rem !important;
            stroke-width: 2.2;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-link {
            font-size: 0.775rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            font-weight: 800;
            color: #475569;
            padding: 0.75rem 1.25rem;
            border-radius: 1rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 0.6rem;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-red);
            background: white;
            box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.04);
            transform: translateY(-2px);
        }

        .nav-link:hover svg {
            transform: scale(1.15) rotate(5deg);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #E31E24 0%, #B91C1C 100%);
            color: white;
            box-shadow: 0 12px 24px -6px rgba(227, 30, 36, 0.35);
        }

        .nav-link.active svg {
            color: white;
        }

        /* Premium Dropdown Enhancement */
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 15px);
            left: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            min-width: 280px;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            padding: 1rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px) scale(0.95);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            transform-origin: top left;
        }

        .dropdown-item {
            padding: 0.9rem 1.25rem;
            border-radius: 1rem;
            font-size: 0.825rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(227, 30, 36, 0.08) 0%, rgba(227, 30, 36, 0) 100%);
            color: var(--primary-red);
            transform: translateX(5px);
        }

        .has-dropdown:hover>.dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        /* Stats Cards Decoration */
        .stat-card-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            position: relative;
        }

        .stat-card-gradient::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle at 100% 0%, rgba(227, 30, 36, 0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Logo Animation */
        .logo-text {
            transition: all 0.3s;
        }

        .logo-text:hover {
            transform: scale(1.05);
            letter-spacing: -0.05em;
        }

        /* Nested Dropdown */
        .dropdown-submenu {
            position: absolute;
            left: 100%;
            top: 0;
            margin-left: 0.25rem;
        }

        /* Responsive Sidebar (Mobile Only) */
        .mobile-sidebar {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .mobile-sidebar.open {
            opacity: 1;
            visibility: visible;
        }

        .mobile-sidebar-content {
            position: absolute;
            left: -280px;
            top: 0;
            bottom: 0;
            width: 280px;
            background: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-sidebar.open .mobile-sidebar-content {
            left: 0;
        }

        /* Stats Cards Dashboard */
        .stat-card {
            background: white;
            border: 1px solid #f1f5f9;
            border-radius: 1rem;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.05);
            border-color: var(--primary-red);
        }

        .b2b-main-wrapper {
            max-width: 1440px;
            margin: 0 auto;
            padding: 2rem;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center gap-10">
                    <a href="{{ route('b2b.dashboard') }}" class="flex items-center group">
                        <span class="logo-text text-2xl font-black tracking-tighter text-slate-900 uppercase"><img src="{{ asset('images/logo.png') }}" style="height:90px;width:90px"></span>
                    </a>


                    <!-- Main Navigation (Desktop) -->
                    <div class="hidden lg:flex items-center gap-1">
                        <a href="{{ route('b2b.dashboard') }}" class="nav-link {{ request()->routeIs('b2b.dashboard') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Dashboard
                        </a>

                        <!-- Products Cascading Menu -->
                        <div class="relative has-dropdown">
                            <button class="nav-link {{ request()->routeIs('b2b.products') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                Products
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="dropdown-menu">
                                <!-- <a href="{{ route('b2b.products') }}" class="dropdown-item font-bold border-b border-slate-50 mb-1">All Products</a> -->
                                @foreach($navCategories as $category)
                                <div class="relative has-dropdown">
                                    <a href="{{ route('b2b.products', ['category' => $category->id]) }}" class="dropdown-item">
                                        {{ $category->name }}
                                        @if($category->subcategories->count() > 0)
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 5l7 7-7 7"></path>
                                        </svg>
                                        @endif
                                    </a>
                                    @if($category->subcategories->count() > 0)
                                    <div class="dropdown-menu dropdown-submenu">
                                        @foreach($category->subcategories as $sub)
                                        <a href="{{ route('b2b.products', ['category' => $category->id, 'subcategory' => $sub->id]) }}" class="dropdown-item font-medium">{{ $sub->name }}</a>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <a href="{{ route('b2b.discounts.index') }}" class="nav-link {{ request()->routeIs('b2b.discounts.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M7 7h.01M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                            </svg>
                            Discounts
                        </a>

                        <a href="{{ route('b2b.orders') }}" class="nav-link {{ request()->routeIs('b2b.orders*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            Orders
                        </a>

                        <a href="{{ route('b2b.rewards.index') }}" class="nav-link {{ request()->routeIs('b2b.rewards.*') || request()->routeIs('b2b.reward-claims.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12z"></path>
                            </svg>
                            Rewards
                        </a>
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-4">
                    @if(isset($customer))
                    <div class="hidden md:flex flex-col items-end mr-4">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Loyalty Points</span>
                        <span class="text-lg font-black text-rose-600">{{ $customer->loyalty_points }}</span>
                    </div>
                    @endif

                    <a href="{{ route('cart.index') }}" class="relative p-2 text-slate-600 hover:text-red-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        @if(count((array)session('cart')) > 0)
                        <span class="absolute top-0 right-0 w-4 h-4 bg-red-600 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white">{{ count((array)session('cart')) }}</span>
                        @endif
                    </a>

                    <div class="relative has-dropdown">
                        <button class="flex items-center gap-2 p-1 pl-3 bg-slate-50 border border-slate-100 rounded-full hover:bg-slate-100 transition-all">
                            <span class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</span>
                            <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white text-xs font-black uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                        <div class="dropdown-menu left-auto right-0">
                            <a href="{{ route('b2b.profile') }}" class="dropdown-item">Profile Settings</a>
                            <div class="border-t border-slate-50 my-1"></div>
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="dropdown-item text-red-600 font-bold">Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
                        </div>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button class="lg:hidden p-2 text-slate-600" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Announcement Ticker Banner -->
    <div class="bg-slate-900 text-white py-2 overflow-hidden relative w-full">
        <div class="flex whitespace-nowrap animate-marquee">
            <span class="mx-8 text-sm font-medium tracking-wide">
                🚀 <span class="text-rose-400 font-bold uppercase">Instant Savings:</span> Select <span class="underline decoration-rose-400">Online Payment</span> for an immediate <strong class="text-white">2% Discount</strong>!
            </span>
            <span class="mx-8 text-sm font-medium tracking-wide">
                ✨ <span class="text-rose-400 font-bold uppercase">Bonus Offer:</span> Enjoy an additional <strong class="text-white">2% OFF</strong> on your total order today!
            </span>
            <span class="mx-8 text-sm font-medium tracking-wide">
                🚀 <span class="text-rose-400 font-bold uppercase">Instant Savings:</span> Select <span class="underline decoration-rose-400">Online Payment</span> for an immediate <strong class="text-white">2% Discount</strong>!
            </span>
            <span class="mx-8 text-sm font-medium tracking-wide">
                ✨ <span class="text-rose-400 font-bold uppercase">Bonus Offer:</span> Enjoy an additional <strong class="text-white">2% OFF</strong> on your total order today!
            </span>
        </div>
    </div>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 20s linear infinite;
        }

        .animate-marquee:hover {
            animation-play-state: paused;
        }
    </style>

    <!-- Mobile Sidebar -->
    <div id="mobileSidebar" class="mobile-sidebar" onclick="toggleMobileMenu()">
        <div class="mobile-sidebar-content p-6" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center mb-10">
                <span class="text-xl font-black uppercase tracking-tighter">V4<span class="text-red-600">Kitchen</span></span>
                <button onclick="toggleMobileMenu()"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>

            <div class="space-y-2">
                <a href="{{ route('b2b.dashboard') }}" class="dropdown-item font-bold py-4">Dashboard</a>
                <a href="{{ route('b2b.products') }}" class="dropdown-item font-bold py-4 text-red-600 italic">View All Products</a>
                <a href="{{ route('b2b.discounts.index') }}" class="dropdown-item font-bold py-4">Discounts</a>
                <a href="{{ route('b2b.orders') }}" class="dropdown-item font-bold py-4">Orders</a>
                <a href="{{ route('b2b.rewards.index') }}" class="dropdown-item font-bold py-4">Rewards</a>
                <a href="{{ route('b2b.profile') }}" class="dropdown-item font-bold py-4">Profile</a>
            </div>
        </div>
    </div>

    <!-- Main Content Shell -->
    <main class="b2b-main-wrapper">
        @yield('content')
    </main>

    <script>
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobileSidebar');
            sidebar.classList.toggle('open');
        }
    </script>
</body>

</html>