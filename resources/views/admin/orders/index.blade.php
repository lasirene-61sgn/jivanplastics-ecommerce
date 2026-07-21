@extends('layouts.admin')

@section('title', 'Orders - Admin Panel')

@section('header', 'Order Fulfillment')

@section('content')
<div x-data="{ modalOpen: false, selectedCount: 0 }" class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Orders Management</h2>
            <p class="text-sm text-slate-500 font-medium italic">Assign new orders to a manufacturing factory.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button 
                type="button" 
                @click="modalOpen = true" 
                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Allocate Selected <span class="ml-2 bg-indigo-500 px-2 py-0.5 rounded-lg" x-text="selectedCount">0</span>
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Order ID, Customer Name..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                </div>

                <!-- Customer Type -->
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Customer Type</label>
                    <select name="customer_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none bg-white">
                        <option value="">All Types</option>
                        <option value="dealer" {{ request('customer_type') == 'dealer' ? 'selected' : '' }}>B2B (Dealer)</option>
                        <option value="individual" {{ request('customer_type') == 'individual' ? 'selected' : '' }}>B2C (Individual)</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none bg-white">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>New / Pending</option>
                        <option value="allocated" {{ request('status') == 'allocated' ? 'selected' : '' }}>Allocated (Mfg)</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing / Mfg Accepted</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected (Mfg)</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Per Page</label>
                    <select name="per_page" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none bg-white">
                        @foreach([10, 20, 30, 50, 100, 200, 500] as $num)
                            <option value="{{ $num }}" {{ request('per_page', 20) == $num ? 'selected' : '' }}>{{ $num }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Category</label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none bg-white" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @if(isset($categories))
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- Subcategory -->
                @if(request()->filled('category_id') && isset($subcategories) && count($subcategories) > 0)
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Subcategory</label>
                    <select name="subcategory_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none bg-white" onchange="this.form.submit()">
                        <option value="">All Subcategories</option>
                        @foreach($subcategories as $subcat)
                            <option value="{{ $subcat->id }}" {{ request('subcategory_id') == $subcat->id ? 'selected' : '' }}>{{ $subcat->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Sub-Subcategory -->
                @if(request()->filled('subcategory_id') && isset($subSubcategories) && count($subSubcategories) > 0)
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Sub-Subcategory</label>
                    <select name="sub_subcategory_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none bg-white" onchange="this.form.submit()">
                        <option value="">All Sub-Subcategories</option>
                        @foreach($subSubcategories as $sscat)
                            <option value="{{ $sscat->id }}" {{ request('sub_subcategory_id') == $sscat->id ? 'selected' : '' }}>{{ $sscat->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div class="flex justify-end gap-2 mt-2">
                @if(request()->anyFilled(['search', 'customer_type', 'status', 'category_id']))
                    <a href="{{ route('admin.orders.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-sm transition-colors">
                        Clear Filters
                    </a>
                @endif
                <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-sm transition-colors">
                    Filter Orders
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg flex items-center shadow-sm text-sm">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <form id="bulkActionForm" method="POST" action="{{ route('admin.orders.allocate') }}">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200">
                            <th class="px-6 py-4 w-10">
                                <input type="checkbox" id="selectAll" class="w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Manufacturing</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    @if(!$order->manufacturingTeam)
                                        <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox w-5 h-5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 cursor-pointer">
                                    @else
                                        <div class="w-5 h-5 flex items-center justify-center text-slate-300">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-slate-900">#{{ $order->order_number }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ $order->created_at->format('d M, Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-700">{{ $order->customer->name }}</div>
                                    <span class="text-[9px] text-slate-400 font-black uppercase tracking-tighter">{{ $order->customer_type }}</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">₹{{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusClass = match($order->status) {
                                            'completed' => 'bg-green-100 text-green-700',
                                            'processing' => 'bg-amber-100 text-amber-700',
                                            default => 'bg-slate-100 text-slate-500',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($order->manufacturingTeam)
                                        <div class="text-xs font-bold text-slate-800">{{ $order->manufacturingTeam->factory_name }}</div>
                                        <div class="text-[9px] text-indigo-500 font-bold uppercase tracking-tight italic">{{ $order->manufacturing_status }}</div>
                                    @else
                                        <span class="text-xs text-slate-300 italic">Waiting for Factory</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl shadow-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-20 text-center text-slate-500 italic font-medium">No orders found in database.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $orders->links() }}
                </div>
            @endif

            <div x-show="modalOpen" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="modalOpen" x-transition:opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="modalOpen = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                    <div x-show="modalOpen" x-transition:scale class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="px-8 pt-8 pb-6">
                            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-6">Allocate selected orders</h3>
                            <div class="space-y-6">
                                <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 text-indigo-700 text-sm font-semibold">
                                    Orders to assign: <span class="font-black underline" x-text="selectedCount">0</span>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest">Select Factory Team</label>
                                    <select name="manufacturing_team_id" class="w-full px-4 py-4 rounded-2xl border border-slate-200 bg-slate-50 font-bold outline-none focus:border-indigo-500" required>
                                        <option value="">Choose a Factory Team</option>
                                        @foreach(App\Models\ManufacturingTeam::where('is_active', true)->get() as $team)
                                            <option value="{{ $team->id }}">{{ $team->factory_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="px-8 py-6 bg-slate-50 flex items-center justify-end gap-3">
                            <button type="button" @click="modalOpen = false" class="px-6 py-2 text-sm font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600">Discard</button>
                            <button type="submit" class="px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-black shadow-lg uppercase tracking-widest active:scale-95 transition-all">Confirm Allocation</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    
    function updateAlpineCount() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        const alpineEl = document.querySelector('[x-data]');
        if (window.Alpine && alpineEl) {
            Alpine.$data(alpineEl).selectedCount = checkedCount;
        }
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateAlpineCount();
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateAlpineCount);
    });
});
</script>
@endsection