@extends('layouts.admin')

@section('title', 'Create Reward - Admin Panel')

@section('header', 'Loyalty Asset Creation')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <nav class="flex mb-6 text-sm text-slate-500">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.rewards.index') }}" class="hover:text-indigo-600 transition-colors">Rewards</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
            <li class="font-medium text-slate-900">New Reward</li>
        </ol>
    </nav>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Configure New Reward</h3>
            <p class="text-sm text-slate-500 mt-1">Define the redemption criteria for physical products or travel assets.</p>
        </div>

        @if ($errors->any())
            <div class="m-8 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-xl shadow-sm text-sm font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.rewards.store') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6">
                <div class="space-y-2">
                    <label for="name" class="text-xs font-black text-slate-500 uppercase tracking-widest flex items-center">
                        Reward Title <span class="text-rose-500 ml-1">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Premium Cookware Set" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-800" required>
                </div>

                <div class="space-y-2">
                    <label for="description" class="text-xs font-black text-slate-500 uppercase tracking-widest">Asset Description</label>
                    <textarea name="description" id="description" rows="3" placeholder="Brief details about the reward..." class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all italic text-slate-600">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="type" class="text-xs font-black text-slate-500 uppercase tracking-widest">Redemption Category *</label>
                        <select name="type" id="type" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-slate-50 font-bold cursor-pointer" required>
                            <option value="">Select Asset Type</option>
                            <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Free Product</option>
                            <option value="travel_package" {{ old('type') === 'travel_package' ? 'selected' : '' }}>Travel Package</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="required_points" class="text-xs font-black text-slate-500 uppercase tracking-widest">Points Threshold *</label>
                        <div class="relative">
                            <input type="number" name="required_points" id="required_points" value="{{ old('required_points') }}" class="w-full pl-4 pr-12 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none font-black text-indigo-600 text-lg" required min="1">
                            <span class="absolute right-4 top-4 text-slate-300 font-black italic uppercase text-[10px]">Points</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 space-y-6 border-t border-slate-100">
                    <div id="product-field" class="space-y-2" style="display: none;">
                        <label for="product_id" class="text-xs font-black text-indigo-500 uppercase tracking-widest">Link Catalog Item *</label>
                        <select name="product_id" id="product_id" class="w-full px-4 py-4 rounded-2xl border border-indigo-100 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-indigo-50/30 font-bold">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="price-field" class="space-y-2" style="display: none;">
                        <label for="price" class="text-xs font-black text-sky-500 uppercase tracking-widest">Package Value (₹) *</label>
                        <div class="relative">
                            <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" class="w-full pl-10 py-4 rounded-2xl border border-sky-100 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none bg-sky-50/30 font-black text-slate-700">
                            <span class="absolute left-4 top-4.5 text-sky-400 font-bold italic">₹</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full border border-slate-200"></div>
                        <span class="ml-4 text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors uppercase tracking-widest">Reward Active & Redeemable</span>
                    </label>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.rewards.index') }}" class="px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">Discard</a>
                <button type="submit" class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    Launch Reward Asset
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const productField = document.getElementById('product-field');
    const priceField = document.getElementById('price-field');
    const productSelect = document.getElementById('product_id');
    const priceInput = document.getElementById('price');
    
    function toggleFields() {
        if (typeSelect.value === 'product') {
            productField.style.display = 'block';
            priceField.style.display = 'none';
            productSelect.setAttribute('required', 'required');
            priceInput.removeAttribute('required');
        } else if (typeSelect.value === 'travel_package') {
            productField.style.display = 'none';
            priceField.style.display = 'block';
            priceInput.setAttribute('required', 'required');
            productSelect.removeAttribute('required');
        } else {
            productField.style.display = 'none';
            priceField.style.display = 'none';
            productSelect.removeAttribute('required');
            priceInput.removeAttribute('required');
        }
    }
    
    typeSelect.addEventListener('change', toggleFields);
    toggleFields(); // Initial run
});
</script>
@endsection