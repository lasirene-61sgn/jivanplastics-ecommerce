@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-6xl mx-auto pb-12">
    @if($product->images->count() > 0)
        @foreach($product->images as $image)
            <form id="delete-image-form-{{ $image->id }}" action="{{ route('admin.products.destroy-image', [$product->id, $image->id]) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Edit Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Product Name *</label>
                    <input type="text" name="name" value="{{ $product->name }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500/20" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Description</label>
                    <textarea name="description" class="w-full px-4 py-3 rounded-xl border border-slate-200 min-h-[120px] outline-none">{{ $product->description }}</textarea>
                </div>

                {{-- Existing Images --}}
                @if($product->images->count() > 0)
                <div>
                    <label class="text-sm font-semibold text-slate-700 mb-3 block">Current Gallery Images</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($product->images as $image)
                            <div class="relative group rounded-xl overflow-hidden border border-slate-200 aspect-square bg-slate-50">
                                <img src="{{ Storage::url($image->image_path) }}" alt="Product Image" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <button type="button" onclick="if(confirm('Delete this image?')) document.getElementById('delete-image-form-{{ $image->id }}').submit()" class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors shadow-lg" title="Delete Image">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Upload New Images --}}
                <div>
                    <label class="text-sm font-semibold text-slate-700 mb-1 block">
                        {{ $product->images->count() > 0 ? 'Add More Images' : 'Gallery Images' }}
                    </label>
                    <input type="file" name="images[]" multiple class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-slate-400 mt-1">New images will be added to the gallery. Existing images are kept unless individually deleted.</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden" 
             x-data="{ 
                variations: [],
                add() { this.variations.push({ size: '', thickness: '', color: '', piece_price: 0, total_pieces: 1, gst_percentage: 18 }) },
                remove(index) { if(this.variations.length > 1) this.variations.splice(index, 1) }
             }" 
             x-init="variations = {{ $product->variations->count() > 0 ? $product->variations->toJson() : '[{ size: \'\', thickness: \'\', color: \'\', piece_price: 0, total_pieces: 1, gst_percentage: 18 }]' }}">
            
            <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Product Specifications
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-semibold">Modify technical details and pricing for this product</p>
                </div>
                <button type="button" @click="add()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-lg shadow-indigo-100 transition-all flex items-center group">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New Variation
                </button>
            </div>
            
            <div class="p-8 overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">
                            <th class="px-4 pb-2 text-left">Size</th>
                            <th class="px-4 pb-2 text-left">Thickness</th>
                            <th class="px-4 pb-2 text-left">Color</th>
                            <th class="px-4 pb-2 w-32">Price/Pc (₹)</th>
                            <th class="px-4 pb-2 w-24">Qty/Unit</th>
                            <th class="px-4 pb-2 w-24">GST %</th>
                            <th class="px-4 pb-2 text-right">Total (Incl. GST)</th>
                            <th class="px-4 pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(v, index) in variations" :key="index">
                            <tr class="group bg-white hover:bg-slate-50 transition-colors border border-slate-100">
                                <td class="px-4 py-3 first:rounded-l-2xl border-y border-l border-slate-100 group-hover:border-indigo-100">
                                    <input type="text" :name="'variations['+index+'][size]'" x-model="v.size" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="text" :name="'variations['+index+'][thickness]'" x-model="v.thickness" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="text" :name="'variations['+index+'][color]'" x-model="v.color" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <div class="relative">
                                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs font-bold">₹</span>
                                        <input type="number" :name="'variations['+index+'][piece_price]'" x-model="v.piece_price" step="0.01" class="w-full pl-7 bg-white border-slate-200 rounded-xl text-sm py-2 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-slate-700">
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="number" :name="'variations['+index+'][total_pieces]'" x-model="v.total_pieces" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-center">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="number" :name="'variations['+index+'][gst_percentage]'" x-model="v.gst_percentage" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm py-2 text-center font-bold text-slate-500">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100 text-right">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="text-[9px] text-slate-400 uppercase font-black tracking-widest">Calculated</span>
                                        <span class="text-sm font-black text-indigo-600">
                                            ₹<span x-text="((v.piece_price * v.total_pieces) * (1 + v.gst_percentage/100)).toFixed(2)"></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 last:rounded-r-2xl border-y border-r border-slate-100 group-hover:border-indigo-100 text-center">
                                    <button type="button" @click="remove(index)" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-4"
                 x-data="{ 
                    selectedCategory: '{{ $product->category_id }}', 
                    selectedSubcategory: '{{ $product->subcategory_id }}',
                    selectedSubSubcategory: '{{ $product->sub_subcategory_id }}',
                    subcategories: [], 
                    subSubcategories: [],
                    loadingSub: false,
                    loadingSubSub: false,
                    
                    async fetchSubcategories(isInitial = false) {
                        if (!this.selectedCategory) {
                            this.subcategories = [];
                            this.selectedSubcategory = '';
                            this.subSubcategories = [];
                            return;
                        }
                        this.loadingSub = true;
                        try {
                            const res = await fetch(`/admin/ajax/subcategories/${this.selectedCategory}`);
                            const data = await res.json();
                            this.subcategories = data;
                            if (!isInitial) {
                                this.selectedSubcategory = '';
                                this.subSubcategories = [];
                            } else {
                                if (this.selectedSubcategory) {
                                    await this.fetchSubSubcategories(true);
                                }
                            }
                        } finally {
                            this.loadingSub = false;
                        }
                    },
                    
                    async fetchSubSubcategories(isInitial = false) {
                        if (!this.selectedSubcategory) {
                            this.subSubcategories = [];
                            this.selectedSubSubcategory = '';
                            return;
                        }
                        this.loadingSubSub = true;
                        try {
                            const res = await fetch(`/admin/ajax/sub-subcategories/${this.selectedSubcategory}`);
                            const data = await res.json();
                            this.subSubcategories = data;
                            if (!isInitial) {
                                this.selectedSubSubcategory = '';
                            }
                        } finally {
                            this.loadingSubSub = false;
                        }
                    },

                    showAddCat: false, showAddSub: false, showAddSubSub: false,
                    newCatName: '', newSubName: '', newSubSubName: '',
                    
                    async saveCategory() {
                        if (!this.newCatName) return;
                        const res = await fetch('{{ route('admin.ajax.categories.store') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ name: this.newCatName })
                        });
                        const data = await res.json();
                        if (data.id) window.location.reload();
                    },
                    
                    async saveSubcategory() {
                        if (!this.newSubName || !this.selectedCategory) return;
                        const res = await fetch('{{ route('admin.ajax.subcategories.store') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ name: this.newSubName, category_id: this.selectedCategory })
                        });
                        const data = await res.json();
                        if (data.id) {
                            this.selectedSubcategory = data.id;
                            this.showAddSub = false;
                            this.newSubName = '';
                            await this.fetchSubcategories();
                            await this.fetchSubSubcategories();
                        }
                    },
                    
                    async saveSubSubcategory() {
                        if (!this.newSubSubName || !this.selectedSubcategory) return;
                        const res = await fetch('{{ route('admin.ajax.sub-subcategories.store') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ name: this.newSubSubName, subcategory_id: this.selectedSubcategory })
                        });
                        const data = await res.json();
                        if (data.id) {
                            this.selectedSubSubcategory = data.id;
                            this.showAddSubSub = false;
                            this.newSubSubName = '';
                            await this.fetchSubSubcategories();
                        }
                    }
                 }"
                 x-init="fetchSubcategories(true)">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <h3 class="font-bold text-slate-900 uppercase text-xs tracking-widest flex items-center">
                        <span class="w-2 h-6 bg-indigo-600 rounded-full mr-3"></span>
                        Product Classification
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Updating Hierarchy</span>
                    </div>
                </div>
                
                <div class="space-y-6 relative">
                    
                    <!-- Category Level -->
                    <div class="relative pl-8 pb-6 border-l-2 border-slate-100 last:border-0 last:pb-0">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 border-indigo-600 shadow-sm z-10"></div>
                        
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider">01. Primary Category *</label>
                            <button type="button" @click="showAddCat = !showAddCat" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase px-2 py-1 bg-indigo-50 rounded-md transition-colors">
                                <span x-text="showAddCat ? 'Cancel' : '+ Quick Add'"></span>
                            </button>
                        </div>

                        <div x-show="showAddCat" x-transition class="mb-3 p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 flex gap-2">
                            <input type="text" x-model="newCatName" placeholder="New Category Name" class="flex-1 px-3 py-2 rounded-lg border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                            <button type="button" @click="saveCategory()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all">Add</button>
                        </div>

                        <select name="category_id" x-model="selectedCategory" @change="fetchSubcategories()" class="w-full rounded-xl border-slate-200 text-sm py-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer bg-white shadow-sm" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sub Category Level -->
                    <div class="relative pl-8 pb-6 border-l-2 border-slate-100 last:border-0 last:pb-0" :class="!selectedCategory && 'opacity-40'">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 transition-colors duration-300 shadow-sm z-10" :class="selectedCategory ? 'border-indigo-400' : 'border-slate-200'"></div>
                        
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider">02. Sub Category</label>
                            <button type="button" x-show="selectedCategory" @click="showAddSub = !showAddSub" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase px-2 py-1 bg-indigo-50 rounded-md transition-colors">
                                <span x-text="showAddSub ? 'Cancel' : '+ Quick Add'"></span>
                            </button>
                        </div>

                        <div x-show="showAddSub" x-transition class="mb-3 p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 flex gap-2">
                            <input type="text" x-model="newSubName" placeholder="New Sub Category Name" class="flex-1 px-3 py-2 rounded-lg border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                            <button type="button" @click="saveSubcategory()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all">Add</button>
                        </div>

                        <div class="relative">
                            <select name="subcategory_id" x-model="selectedSubcategory" @change="fetchSubSubcategories()" class="w-full rounded-xl border-slate-200 text-sm py-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer bg-white shadow-sm disabled:bg-slate-50 disabled:cursor-not-allowed" :disabled="loadingSub || !selectedCategory">
                                <option value="">Select Sub Category</option>
                                <template x-for="sub in subcategories" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name" :selected="sub.id == selectedSubcategory"></option>
                                </template>
                            </select>
                            <div x-show="loadingSub" class="absolute right-10 top-3">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Sub Sub Category Level -->
                    <div class="relative pl-8 last:border-0 last:pb-0" :class="!selectedSubcategory && 'opacity-40'">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-2 transition-colors duration-300 shadow-sm z-10" :class="selectedSubcategory ? 'border-indigo-400' : 'border-slate-200'"></div>
                        
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider">03. Sub Sub Category</label>
                            <button type="button" x-show="selectedSubcategory" @click="showAddSubSub = !showAddSubSub" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase px-2 py-1 bg-indigo-50 rounded-md transition-colors">
                                <span x-text="showAddSubSub ? 'Cancel' : '+ Quick Add'"></span>
                            </button>
                        </div>

                        <div x-show="showAddSubSub" x-transition class="mb-3 p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 flex gap-2">
                            <input type="text" x-model="newSubSubName" placeholder="New Sub Sub Category Name" class="flex-1 px-3 py-2 rounded-lg border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500/20 outline-none">
                            <button type="button" @click="saveSubSubcategory()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all">Add</button>
                        </div>

                        <div class="relative">
                            <select name="sub_subcategory_id" x-model="selectedSubSubcategory" class="w-full rounded-xl border-slate-200 text-sm py-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer bg-white shadow-sm disabled:bg-slate-50 disabled:cursor-not-allowed" :disabled="loadingSubSub || !selectedSubcategory">
                                <option value="">Select Sub Sub Category</option>
                                <template x-for="subsub in subSubcategories" :key="subsub.id">
                                    <option :value="subsub.id" x-text="subsub.name" :selected="subsub.id == selectedSubSubcategory"></option>
                                </template>
                            </select>
                            <div x-show="loadingSubSub" class="absolute right-10 top-3">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                <h3 class="text-xs font-bold text-indigo-600 uppercase mb-4">Dealer Discounts (%)</h3>
                <div class="space-y-2 max-h-[150px] overflow-y-auto">
                    @foreach($dealers as $dealer)
                        @php $discount = $product->dealerDiscounts->where('customer_id', $dealer->id)->first(); @endphp
                        <div class="flex items-center justify-between bg-slate-50 p-2 rounded">
                            <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $dealer->company_name ?? $dealer->name }}</span>
                            <input type="number" name="dealer_discounts[{{ $dealer->id }}]" value="{{ $discount ? $discount->discount_percentage : '' }}" class="w-16 p-1 border-slate-200 text-xs">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h3 class="text-xs font-bold text-indigo-600 uppercase mb-4">Update Quantity Restrictions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-[9px] font-bold text-slate-400">Global Min/Max</label>
                    <div class="flex gap-1">
                        <input type="number" name="min_order_qty" value="{{ $product->min_order_qty }}" class="w-full border-slate-200 rounded text-sm" placeholder="Min">
                        <input type="number" name="max_order_qty" value="{{ $product->max_order_qty }}" class="w-full border-slate-200 rounded text-sm" placeholder="Max">
                    </div>
                </div>
                </div>
        </div>

        <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-bold rounded-xl shadow-xl hover:bg-indigo-700">
            Apply Product Updates
        </button>
    </form>
</div>
@endsection