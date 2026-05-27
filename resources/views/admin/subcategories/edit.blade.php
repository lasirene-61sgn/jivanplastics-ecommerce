@extends('layouts.admin')

@section('title', 'Edit Subcategory - Admin Panel')

@section('header', 'Edit Subcategory')

@section('content')
<div class="max-w-4xl mx-auto">
    <nav class="flex mb-6 text-sm text-slate-500">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.subcategories.index') }}" class="hover:text-indigo-600 transition-colors">Subcategories</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
            <li class="font-medium text-slate-900">Edit Subcategory</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-xl font-bold text-slate-900">Update Subcategory</h3>
            <p class="text-sm text-slate-500 mt-1 italic text-indigo-600">Currently Editing: {{ $subcategory->name }}</p>
        </div>

        <form action="{{ route('admin.subcategories.update', $subcategory) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-y-6">
                <div class="space-y-2">
                    <label for="category_id" class="text-sm font-semibold text-slate-700">Parent Category *</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-white" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id', $subcategory->category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="name" class="text-sm font-semibold text-slate-700">Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $subcategory->name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none" required>
                </div>
                
                <div class="space-y-2">
                    <label for="image" class="text-sm font-semibold text-slate-700">Subcategory Thumbnail</label>
                    @if($subcategory->image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $subcategory->image) }}" alt="{{ $subcategory->name }}" class="w-32 h-32 object-cover rounded-xl border border-slate-200 shadow-sm">
                        </div>
                    @endif
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/*"
                        class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none text-sm"
                    >
                </div>
                
                <div class="space-y-2">
                    <label for="description" class="text-sm font-semibold text-slate-700">Description</label>
                    <textarea id="description" name="description" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none min-h-[100px]">{{ old('description', $subcategory->description) }}</textarea>
                </div>
                
                @if($dealers->count() > 0)
                <div class="pt-6 border-t border-slate-100">
                    <label class="text-sm font-bold text-slate-900 uppercase tracking-wider block mb-4 text-indigo-600">Dealer-Specific Discounts</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($dealers as $dealer)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-4">
                            <div class="truncate">
                                <span class="block font-semibold text-slate-800 truncate">{{ $dealer->company_name ?? $dealer->name }}</span>
                                <span class="text-[10px] text-slate-500">{{ $dealer->email }}</span>
                            </div>
                            <div class="relative w-24 flex-shrink-0">
                                <input type="number" name="dealer_discounts[{{ $dealer->id }}]" class="w-full pl-3 pr-7 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none text-sm" min="0" max="100" step="0.01" value="{{ old('dealer_discounts.' . $dealer->id, $subcategory->dealerDiscounts->where('customer_id', $dealer->id)->first()?->discount_percentage) }}">
                                <span class="absolute right-3 top-2 text-slate-400 text-xs">%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="flex items-center pt-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $subcategory->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-semibold text-slate-700">Subcategory Active</span>
                    </label>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.subcategories.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5">
                    Update Subcategory
                </button>
            </div>
        </form>
    </div>
</div>
@endsection