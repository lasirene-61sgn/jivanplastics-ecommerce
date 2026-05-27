@extends('layouts.admin')

@section('title', 'Product Details - Admin Panel')

@section('header', 'Product Specifications')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Add Product Details</h2>
            <p class="text-sm text-slate-500">Attach extended descriptions and high-resolution galleries to specific products.</p>
        </div>
        <a href="{{ route('admin.product-details.list') }}" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all active:scale-95">
            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            View All Details
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        
        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="m-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    <span class="font-bold">Validation Errors:</span>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1 ml-8">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.product-details.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 gap-y-6">
                <div class="space-y-2">
                    <label for="product_id" class="text-sm font-semibold text-slate-700 flex items-center">
                        Select Product <span class="text-red-500 ml-1">*</span>
                    </label>
                    <div class="relative">
                        <select name="product_id" id="product_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none bg-white appearance-none cursor-pointer font-medium" required>
                            <option value="">Choose a product from inventory</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="description" class="text-sm font-semibold text-slate-700">Detailed Technical Description</label>
                    <div class="rounded-xl border border-slate-200 overflow-hidden focus-within:ring-4 focus-within:ring-indigo-500/10 focus-within:border-indigo-500 transition-all">
                        <textarea 
                            id="description" 
                            name="description" 
                            class="w-full px-4 py-3 outline-none min-h-[250px] text-slate-600 leading-relaxed" 
                            placeholder="Enter technical specs, sizing charts, or manufacturing details here..."
                        ></textarea>
                        <div class="bg-slate-50 px-4 py-2 border-t border-slate-100 flex justify-between items-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Supports Tab Indentation</span>
                            <span class="text-[10px] text-slate-400">Inter Font Render</span>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label for="images" class="text-sm font-semibold text-slate-700">Additional Gallery Images</label>
                    <div class="group relative flex flex-col items-center justify-center p-10 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50 hover:bg-white hover:border-indigo-400 transition-all cursor-pointer">
                        <input 
                            type="file" 
                            id="images" 
                            name="images[]" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            multiple
                        >
                        <div class="text-center space-y-2">
                            <div class="mx-auto w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-100 text-indigo-600 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">Click to upload or drag and drop</p>
                            <p class="text-xs text-slate-400">PNG, JPG, GIF (Max 2MB per file)</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 text-indigo-500 mt-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"></path></svg>
                        <span class="text-xs font-bold uppercase tracking-tighter">Multiple Selection Enabled</span>
                    </div>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Product Specifications
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