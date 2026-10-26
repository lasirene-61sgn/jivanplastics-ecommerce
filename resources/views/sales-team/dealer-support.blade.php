@extends('layouts.sales-team')

@section('title', 'Basic Desk - Sales Team Portal')

@section('header', 'Direct Order Placement')

@section('content')
<div class="max-w-7xl mx-auto pb-20 space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="h-14 w-14 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-xl shadow-indigo-100">
                <i class="fas fa-headset text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase italic">Basic Order Desk</h2>
                <p class="text-xs text-slate-400 font-bold tracking-widest uppercase italic">Direct Fast Line-Item Order Entry</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('sales-team.enhanced-dealer-support') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-50 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-indigo-100 transition-all">
                <i class="fas fa-desktop mr-2"></i> Switch to Enhanced Support
            </a>
            <a href="{{ route('sales-team.dashboard') }}" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest shadow-sm hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm text-sm font-bold flex justify-between items-center animate-fadeIn">
            <span><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="text-green-500 hover:text-green-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-xl shadow-sm text-sm font-bold flex justify-between items-center animate-fadeIn">
            <span><i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}</span>
            <button onclick="this.parentElement.style.display='none'" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Dealer Selection Panel -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 uppercase tracking-widest text-[10px] font-black text-slate-400 flex justify-between items-center">
                    <span>Portfolio Selection</span>
                    <span class="bg-indigo-100 text-indigo-600 px-2 py-0.5 rounded text-[9px] font-bold">{{ $dealers->count() }} total</span>
                </div>
                <div class="p-4 overflow-y-auto max-h-[600px] custom-scrollbar">
                    @if($dealers->count() > 0)
                        <div class="space-y-2">
                            @foreach($dealers as $dealer)
                                <a href="#" 
                                   class="dealer-item block p-4 rounded-2xl border border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/50 transition-all group"
                                   data-dealer-id="{{ $dealer->id }}">
                                    <div class="flex justify-between items-start mb-1">
                                        <h6 class="text-sm font-black text-slate-900 group-hover:text-indigo-600 truncate max-w-[180px]">{{ $dealer->company_name ?? $dealer->name }}</h6>
                                        <div class="h-2.5 w-2.5 rounded-full bg-slate-200 group-hover:bg-indigo-500 transition-colors"></div>
                                    </div>
                                    <p class="text-[11px] text-slate-400 font-medium truncate mb-2">{{ $dealer->email }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-black uppercase tracking-wider text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                            {{ $dealer->phone ?? 'NO PHONE' }}
                                        </span>
                                        @if($dealer->city)
                                            <span class="text-[9px] font-bold text-slate-400">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $dealer->city }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 text-center text-slate-400 italic text-sm">No dealers assigned to your account.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Drafting Environment -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-slate-900 rounded-[2.5rem] p-1 border-4 border-slate-800 shadow-2xl">
                <div class="bg-white rounded-[2.1rem] overflow-hidden">
                    <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Order Drafting Environment</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Quickly key in products and quantities</p>
                        </div>
                        <span class="text-[10px] font-black bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full uppercase tracking-tighter">Direct Placement</span>
                    </div>

                    <form id="dealer-order-form" action="{{ route('sales-team.dealer-support.place-order') }}" method="POST" class="p-8 space-y-8">
                        @csrf
                        
                        <div class="space-y-2">
                            <label for="dealer_id" class="text-xs font-black text-slate-500 uppercase tracking-widest">Invoicing Target Asset *</label>
                            <select class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-slate-50 font-bold text-slate-800 cursor-pointer @error('dealer_id') border-rose-500 @enderror" id="dealer_id" name="dealer_id" required>
                                <option value="">Select a dealer from list or click on left panel...</option>
                                @foreach($dealers as $dealer)
                                    <option value="{{ $dealer->id }}" {{ old('dealer_id') == $dealer->id ? 'selected' : '' }}>
                                        {{ $dealer->company_name ?? $dealer->name }} ({{ $dealer->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('dealer_id') <p class="text-[10px] font-bold text-rose-500 italic mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-4">
                            <label class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center">
                                Product Manifest
                                <span class="ml-2 w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            </label>
                            
                            <div id="product-fields" class="space-y-3">
                                <div class="product-field-row grid grid-cols-1 md:grid-cols-12 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-all">
                                    <div class="md:col-span-7">
                                        <select class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white font-bold text-sm outline-none focus:border-indigo-500 product-select" name="product_ids[]" required>
                                            <option value="">Select an asset...</option>
                                            @foreach(\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }} — ₹{{ number_format($product->price, 2) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-3 italic font-black">
                                        <input type="number" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm outline-none focus:border-indigo-500 quantity-input" name="quantities[]" placeholder="Qty" min="1" value="1" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="button" class="w-full h-full flex items-center justify-center bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all remove-product">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="inline-flex items-center text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 hover:text-indigo-800 transition-colors pt-2" id="add-product">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Add Line Item
                            </button>
                        </div>

                        <div class="pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <button type="reset" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors italic">Clear Draft</button>
                            <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.3em] shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                                <i class="fas fa-paper-plane mr-3"></i>
                                Authorize &amp; Execute Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Product HTML Template
    const productTemplate = `
        <div class="product-field-row grid grid-cols-1 md:grid-cols-12 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-all animate-fadeIn">
            <div class="md:col-span-7">
                <select class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white font-bold text-sm outline-none focus:border-indigo-500 product-select" name="product_ids[]" required>
                    <option value="">Select an asset...</option>
                    @foreach(\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} — ₹{{ number_format($product->price, 2) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <input type="number" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-white text-sm font-black outline-none focus:border-indigo-500 quantity-input italic" name="quantities[]" placeholder="Qty" min="1" value="1" required>
            </div>
            <div class="md:col-span-2">
                <button type="button" class="w-full h-full flex items-center justify-center bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all remove-product">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    `;

    // Add product field
    document.getElementById('add-product').addEventListener('click', function() {
        const productFields = document.getElementById('product-fields');
        const div = document.createElement('div');
        div.innerHTML = productTemplate;
        productFields.appendChild(div.firstElementChild);
    });
    
    // Remove product field
    document.getElementById('product-fields').addEventListener('click', function(e) {
        const target = e.target.closest('.remove-product');
        if (target) {
            if (document.querySelectorAll('.product-field-row').length > 1) {
                target.closest('.product-field-row').remove();
            } else {
                alert('An order requires at least one asset line item.');
            }
        }
    });
    
    // Select dealer from list
    document.querySelectorAll('.dealer-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const dealerId = this.getAttribute('data-dealer-id');
            document.getElementById('dealer_id').value = dealerId;
            
            // Visual feedback for selected item
            document.querySelectorAll('.dealer-item').forEach(i => {
                i.classList.remove('border-indigo-500', 'bg-indigo-50/80');
                const dot = i.querySelector('div.rounded-full');
                if (dot) {
                    dot.classList.remove('bg-indigo-600');
                    dot.classList.add('bg-slate-200');
                }
            });
            this.classList.add('border-indigo-500', 'bg-indigo-50/80');
            const dot = this.querySelector('div.rounded-full');
            if (dot) {
                dot.classList.remove('bg-slate-200');
                dot.classList.add('bg-indigo-600');
            }
        });
    });
});
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
.animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endsection