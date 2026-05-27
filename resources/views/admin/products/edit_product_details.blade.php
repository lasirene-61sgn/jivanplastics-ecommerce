@extends('layouts.admin')

@section('title', 'Edit Product Details - Admin Panel')

@section('header', 'Edit Specifications')

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Edit Details: <span class="text-indigo-600">{{ $product->name }}</span></h2>
            <p class="text-sm text-slate-500 italic">Updating technical specifications and gallery for this product.</p>
        </div>
        <a href="{{ route('admin.product-details.list') }}" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all active:scale-95">
            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-8 pt-6">
            @if(session('success'))
                <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm font-medium text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form action="{{ route('admin.product-details.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-10">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-y-8">
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label for="description" class="text-sm font-bold text-slate-700 tracking-wide uppercase">Extended Description</label>
                        <span class="text-[10px] text-slate-400 font-bold bg-slate-100 px-2 py-1 rounded">ID: #{{ $product->id }}</span>
                    </div>
                    <div class="rounded-2xl border border-slate-200 overflow-hidden focus-within:ring-4 focus-within:ring-indigo-500/10 focus-within:border-indigo-500 transition-all shadow-inner">
                        <textarea 
                            id="description" 
                            name="description" 
                            class="w-full px-5 py-4 outline-none min-h-[300px] text-slate-600 leading-relaxed font-normal text-base bg-white" 
                            placeholder="Detail the product specs..."
                        >{{ old('description', $product->description) }}</textarea>
                        <div class="bg-slate-50 px-5 py-3 border-t border-slate-100 flex justify-between items-center">
                            <div class="flex items-center text-[11px] text-slate-500 space-x-4">
                                <span class="flex items-center"><i class="fas fa-keyboard mr-1.5"></i> Tab keys enabled</span>
                                <span class="flex items-center"><i class="fas fa-code mr-1.5"></i> HTML allowed</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($product->images->count() > 0)
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <label class="text-sm font-bold text-slate-700 tracking-wide uppercase">Current Gallery ({{ $product->images->count() }})</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
                        @foreach($product->images as $image)
                        <div class="relative group aspect-square rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 shadow-sm transition-hover hover:border-indigo-300">
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[1px]">
                                <a href="{{ route('admin.products.destroy-image', [$product, $image->id]) }}" 
                                   class="bg-white text-red-600 p-2.5 rounded-full shadow-2xl transform hover:scale-110 active:scale-95 transition-all"
                                   onclick="return confirm('Permanently remove this image from the gallery?')">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <label for="images" class="text-sm font-bold text-slate-700 tracking-wide uppercase flex items-center">
                        <i class="fas fa-plus-circle mr-2 text-indigo-500"></i> Append New Images
                    </label>
                    <div class="group relative flex flex-col items-center justify-center p-12 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50 hover:bg-white hover:border-indigo-400 transition-all cursor-pointer">
                        <input 
                            type="file" 
                            id="images" 
                            name="images[]" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            multiple
                        >
                        <div class="text-center space-y-3">
                            <div class="mx-auto w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 text-indigo-600 group-hover:rotate-12 transition-transform">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Click to add more files or drag them here</p>
                            <div class="flex items-center justify-center space-x-2 text-xs text-slate-400 font-medium">
                                <span class="px-2 py-0.5 bg-slate-100 rounded">JPG</span>
                                <span class="px-2 py-0.5 bg-slate-100 rounded">PNG</span>
                                <span class="px-2 py-0.5 bg-slate-100 rounded">GIF</span>
                                <span>Max 2MB per item</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="w-full sm:w-auto px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center uppercase tracking-widest">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Update Specifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const descriptionField = document.getElementById('description');
        if (descriptionField) {
            descriptionField.addEventListener('keydown', function(e) {
                if (e.key === 'Tab') {
                    e.preventDefault();
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    this.value = this.value.substring(0, start) + '\t' + this.value.substring(end);
                    this.selectionStart = this.selectionEnd = start + 1;
                }
            });
        }
    });
</script>
@endsection