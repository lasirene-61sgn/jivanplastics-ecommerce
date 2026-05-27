@php
    $links = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
        ['route' => 'admin.subcategories.index', 'label' => 'Subcategories', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['route' => 'admin.sub_subcategories.index', 'label' => 'Sub-Subcategories', 'icon' => 'M4 6h16M4 12h8m-8 6h16'],
        ['route' => 'admin.products.index', 'label' => 'Products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['route' => 'admin.product-details', 'label' => 'Add Product Details', 'icon' => 'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => 'admin.product-details.list', 'label' => 'Product Details List', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['route' => 'admin.manufacturing-teams.index', 'label' => 'Manufacturing Teams', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5'],
        ['route' => 'admin.customers.index', 'label' => 'All Customers', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197'],
        ['route' => 'admin.customers.dealers', 'label' => 'Dealer Customers', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01'],
        ['route' => 'admin.customers.individuals', 'label' => 'Individual Customers', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ['route' => 'admin.orders.index', 'label' => 'Orders', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['route' => 'admin.return-requests.index', 'label' => 'Return Requests', 'icon' => 'M16 15L12 19L8 15M12 19V5'],
        ['route' => 'admin.rewards.index', 'label' => 'Rewards', 'icon' => 'M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2'],
        ['route' => 'admin.reward-claims.index', 'label' => 'Reward Claims', 'icon' => 'M9 12l2 2 4-4'],
        ['route' => 'admin.sales-team.index', 'label' => 'Sales Team', 'icon' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745'],
    ];
@endphp

@foreach($links as $link)
    <a href="{{ route($link['route']) }}" 
       class="group flex items-center px-4 py-2.5 text-[13px] font-semibold rounded-xl transition-all duration-300 
       {{ request()->routeIs($link['route'] . '*') 
          ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' 
          : 'text-slate-600 hover:bg-slate-50 hover:text-indigo-600 hover:translate-x-1' }}">
        
        <svg class="w-5 h-5 mr-3 transition-colors duration-300 
             {{ request()->routeIs($link['route'] . '*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $link['icon'] }}"></path>
        </svg>
        <span class="truncate">{{ $link['label'] }}</span>
    </a>
@endforeach