@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Product Inventory</h2>
        <a href="{{ route('admin.products.create') }}" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-xl text-sm shadow-lg shadow-indigo-100">+ New Product</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    <th class="px-6 py-4">Product Name</th>
                    <th class="px-6 py-4">Category Hierarchy</th>
                    <th class="px-6 py-4">Variations</th>
                    <th class="px-6 py-4">Price Range (Inc. GST)</th>
                    <th class="px-6 py-4">Visibility</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($products as $product)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 border flex-shrink-0">
                                    @if($product->images->first())
                                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" class="w-full h-full object-cover rounded-lg">
                                    @endif
                                </div>
                                <span class="font-bold text-slate-900 text-sm">{{ $product->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-[10px] flex flex-col">
                                <span class="font-bold text-slate-700">{{ $product->category->name ?? 'N/A' }}</span>
                                @if($product->subcategory)
                                    <span class="text-slate-400">↳ {{ $product->subcategory->name }}</span>
                                @endif
                                @if($product->subSubcategory)
                                    <span class="text-slate-400 ml-2">↳ {{ $product->subSubcategory->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black uppercase rounded">{{ $product->variations->count() }} Variations</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-slate-700">
                                @if($product->variations->count() > 0)
                                    ₹{{ number_format($product->variations->min('total_price'), 2) }} - ₹{{ number_format($product->variations->max('total_price'), 2) }}
                                @else
                                    <span class="text-slate-300 italic text-[10px]">No Pricing Set</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400' }}">
                                {{ $product->is_active ? 'Published' : 'Hidden' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-lg hover:bg-indigo-600 hover:text-white transition-all">
                                    Edit
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-bold rounded-lg hover:bg-red-600 hover:text-white transition-all">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    @if($products->hasPages())
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection