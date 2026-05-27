@extends('layouts.admin')

@section('title', 'Edit Reward')

@section('header', 'Modify Reward Asset')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <nav class="flex mb-6 text-sm text-slate-500">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.rewards.index') }}" class="hover:text-indigo-600 transition-colors">Rewards</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
            <li class="font-medium text-slate-900 italic">{{ $reward->name }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight italic">Update Configuration</h3>
                <p class="text-sm text-slate-500 mt-1">Modify redemption rules or asset status.</p>
            </div>
            <div class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </div>
        </div>

        @if ($errors->any())
            <div class="m-8 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 rounded-r-xl shadow-sm text-sm">
                <ul class="list-disc list-inside font-bold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.rewards.update', $reward) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-y-6">
                <div class="space-y-2">
                    <label for="name" class="text-xs font-black text-slate-500 uppercase tracking-widest">Reward Title *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $reward->name) }}" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-800" required>
                </div>

                <div class="space-y-2">
                    <label for="description" class="text-xs font-black text-slate-500 uppercase tracking-widest">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none italic font-medium text-slate-600">{{ old('description', $reward->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="type" class="text-xs font-black text-slate-500 uppercase tracking-widest text-indigo-500">Asset Category *</label>
                        <select name="type" id="type" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-slate-50 font-bold cursor-pointer transition-all" required>
                            <option value="product" {{ (old('type', $reward->type) === 'product') ? 'selected' : '' }}>Free Product</option>
                            <option value="travel_package" {{ (old('type', $reward->type) === 'travel_package') ? 'selected' : '' }}>Travel Package</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="required_points" class="text-xs font-black text-slate-500 uppercase tracking-widest text-amber-500">Points Cost *</label>
                        <div class="relative">
                            <input type="number" name="required_points" id="required_points" value="{{ old('required_points', $reward->required_points) }}" class="w-full pl-4 pr-12 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 outline-none font-black text-amber-700 text-lg" required min="1">
                            <span class="absolute right-4 top-4.5 text-[10px] font-black text-slate-300 uppercase italic">Points</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 space-y-6 border-t border-slate-100">
                    <div id="product-field" class="space-y-2">
                        <label for="product_id" class="text-xs font-black text-emerald-500 uppercase tracking-widest">Linked Catalog Item *</label>
                        <select name="product_id" id="product_id" class="w-full px-4 py-4 rounded-2xl border border-emerald-100 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none bg-emerald-50/20 font-bold transition-all">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ (old('product_id', $reward->product_id) == $product->id) ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="price-field" class="space-y-2">
                        <label for="price" class="text-xs font-black text-sky-500 uppercase tracking-widest">Assessed Valuation (₹) *</label>
                        <div class="relative">
                            <input type="number" name="price" id="price" value="{{ old('price', $reward->price) }}" step="0.01" min="0" class="w-full pl-10 py-4 rounded-2xl border border-sky-100 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 outline-none bg-sky-50/20 font-black text-slate-700">
                            <span class="absolute left-4 top-4.5 text-sky-400 font-bold">₹</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" {{ old('is_active', $reward->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full border border-slate-200"></div>
                        <span class="ml-4 text-sm font-black text-slate-700 group-hover:text-indigo-600 transition-colors uppercase tracking-widest">Active & Redeemable</span>
                    </label>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.rewards.index') }}" class="px-6 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:text-slate-600 transition-colors">Discard Changes</a>
                <button type="submit" class="px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    Commit Updates
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
    toggleFields(); // Initial run on load
});
</script>
@endsection