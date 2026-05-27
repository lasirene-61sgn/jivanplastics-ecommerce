@extends('layouts.admin')

@section('title', 'Create Sub-Subcategory - Admin Panel')

@section('header', 'Create New Sub-Subcategory')

@section('content')
<div class="max-w-4xl mx-auto">
    <nav class="flex mb-6 text-sm text-slate-500" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.sub_subcategories.index') }}" class="hover:text-indigo-600 transition-colors">Sub-Subcategories</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
            <li class="font-medium text-slate-900">Create New</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-xl font-bold text-slate-900">Deep Categorization</h3>
            <p class="text-sm text-slate-500 mt-1">Assign this item to a specific subcategory for precise product filtering.</p>
        </div>

        <form action="{{ route('admin.sub_subcategories.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6">
                <div class="space-y-2">
                    <label for="subcategory_id" class="text-sm font-semibold text-slate-700 flex items-center">
                        Parent Subcategory <span class="text-red-500 ml-1">*</span>
                    </label>
                    <select name="subcategory_id" id="subcategory_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none bg-white" required>
                        <option value="">Select a Subcategory</option>
                        @foreach($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->category->name }} &nbsp;➔&nbsp; {{ $subcategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="image" class="text-sm font-semibold text-slate-700">Sub-Subcategory Thumbnail</label>
                    <input type="file" id="image" name="image" accept="image/*" class="w-full px-4 py-2 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none text-sm">
                </div>

                <div class="space-y-2">
                    <label for="name" class="text-sm font-semibold text-slate-700 flex items-center">
                        Sub-Subcategory Name <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Leather Boots" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>
                </div>
                
                <div class="space-y-2">
                    <label for="description" class="text-sm font-semibold text-slate-700">Description</label>
                    <textarea id="description" name="description" placeholder="Detailed description for SEO or internal tracking..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none min-h-[100px]">{{ old('description') }}</textarea>
                </div>
                
                @if($dealers->count() > 0)
                <div class="pt-6 border-t border-slate-100">
                    <label class="text-sm font-bold text-slate-900 uppercase tracking-wider block mb-4 text-indigo-600">Specific Dealer Pricing</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($dealers as $dealer)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-4">
                            <div class="truncate">
                                <span class="block font-semibold text-slate-800 truncate">{{ $dealer->company_name ?? $dealer->name }}</span>
                                <span class="text-[10px] text-slate-500">{{ $dealer->email }}</span>
                            </div>
                            <div class="relative w-24 flex-shrink-0">
                                <input type="number" name="dealer_discounts[{{ $dealer->id }}]" placeholder="0.00" class="w-full pl-3 pr-7 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none text-sm" min="0" max="100" step="0.01" value="{{ old('dealer_discounts.' . $dealer->id) }}">
                                <span class="absolute right-3 top-2 text-slate-400 text-xs">%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="flex items-center space-x-3 pt-4">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-semibold text-slate-700">Display in Store</span>
                    </label>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.sub_subcategories.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5">
                    Save Sub-Subcategory
                </button>
            </div>
        </form>
    </div>
</div>
@endsection