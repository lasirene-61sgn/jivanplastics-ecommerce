@extends('layouts.admin')

@section('title', 'Create Product - Admin Panel')

@section('content')
<div class="max-w-6xl mx-auto pb-12">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-6">Basic Information</h3>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="text-sm font-semibold text-slate-700">Product Name *</label>
                    <input type="text" name="name" class="w-full px-4 py-3 rounded-xl border border-slate-200 outline-none focus:ring-2 focus:ring-indigo-500/20" required>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Description</label>
                    <textarea name="description" class="w-full px-4 py-3 rounded-xl border border-slate-200 min-h-[120px] outline-none"></textarea>
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Gallery Images</label>
                    <input type="file" name="images[]" multiple accept=".jpeg,.png,.jpg,.gif,.webp" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" onchange="validateImageFiles(this)">
                    <div id="image-error" class="text-xs text-red-500 mt-1 font-semibold hidden"></div>
                    @error('images.*')
                        <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                    @error('images')
                        <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden" 
             x-data="{ 
                variations: [{ size: '', thickness: '', color: '', piece_price: 0, total_pieces: 1, gst_percentage: 18 }],
                addVariation() { this.variations.push({ size: '', thickness: '', color: '', piece_price: 0, total_pieces: 1, gst_percentage: 18 }) },
                removeVariation(index) { if(this.variations.length > 1) this.variations.splice(index, 1) }
             }">
            <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Product Specifications
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-1 uppercase font-semibold">Define sizes, thickness, colors and pricing for this product</p>
                </div>
                <button type="button" @click="addVariation()" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-lg shadow-indigo-100 transition-all flex items-center group">
                    <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Specification
                </button>
            </div>
            
            <div class="p-8 overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-4">
                    <thead>
                        <tr class="text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-4 pb-2">Size <span class="text-slate-300 font-normal italic">(e.g. 12x12)</span></th>
                            <th class="px-4 pb-2">Thickness <span class="text-slate-300 font-normal italic">(e.g. 15mm)</span></th>
                            <th class="px-4 pb-2">Color</th>
                            <th class="px-4 pb-2 w-32">Price / Pc (₹)</th>
                            <th class="px-4 pb-2 w-24">Qty/Unit</th>
                            <th class="px-4 pb-2 w-24 text-center">GST %</th>
                            <th class="px-4 pb-2 text-right">Total (Incl. GST)</th>
                            <th class="px-4 pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(v, index) in variations" :key="index">
                            <tr class="group bg-white hover:bg-slate-50 transition-colors border border-slate-100">
                                <td class="px-4 py-3 first:rounded-l-2xl border-y border-l border-slate-100 group-hover:border-indigo-100">
                                    <div class="relative">
                                        <input type="text" :name="'variations['+index+'][size]'" x-model="v.size" placeholder="12x12" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="text" :name="'variations['+index+'][thickness]'" x-model="v.thickness" placeholder="15mm" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="text" :name="'variations['+index+'][color]'" x-model="v.color" placeholder="Red" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <div class="relative">
                                        <span class="absolute left-3 top-3 text-slate-400 text-xs">₹</span>
                                        <input type="number" :name="'variations['+index+'][piece_price]'" x-model="v.piece_price" step="0.01" class="w-full pl-7 bg-white border-slate-200 rounded-xl text-sm py-2.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-slate-700">
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="number" :name="'variations['+index+'][total_pieces]'" x-model="v.total_pieces" class="w-full bg-white border-slate-200 rounded-xl text-sm py-2.5 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100">
                                    <input type="number" :name="'variations['+index+'][gst_percentage]'" x-model="v.gst_percentage" class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm py-2.5 text-center font-bold text-slate-500">
                                </td>
                                <td class="px-4 py-3 border-y border-slate-100 group-hover:border-indigo-100 text-right">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="text-[10px] text-slate-400 uppercase font-black tracking-tighter">Final Value</span>
                                        <span class="text-sm font-black text-indigo-600">
                                            ₹<span x-text="((v.piece_price * v.total_pieces) * (1 + v.gst_percentage/100)).toFixed(2)"></span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 last:rounded-r-2xl border-y border-r border-slate-100 group-hover:border-indigo-100 text-center">
                                    <button type="button" @click="removeVariation(index)" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
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
                    selectedCategory: '', 
                    selectedSubcategory: '',
                    selectedSubSubcategory: '',
                    subcategories: [], 
                    subSubcategories: [],
                    loadingSub: false,
                    loadingSubSub: false,
                    
                    fetchSubcategories() {
                        if (!this.selectedCategory) {
                            this.subcategories = [];
                            this.selectedSubcategory = '';
                            this.selectedSubSubcategory = '';
                            this.subSubcategories = [];
                            return;
                        }
                        this.loadingSub = true;
                        fetch(`/admin/ajax/subcategories/${this.selectedCategory}`)
                            .then(res => res.json())
                            .then(data => {
                                this.subcategories = data;
                                this.loadingSub = false;
                                // Only reset if not being set by a quick add
                                if (!this.selectedSubcategory) {
                                    this.selectedSubcategory = '';
                                    this.subSubcategories = [];
                                }
                            });
                    },
                    
                    fetchSubSubcategories() {
                        if (!this.selectedSubcategory) {
                            this.subSubcategories = [];
                            this.selectedSubSubcategory = '';
                            return;
                        }
                        this.loadingSubSub = true;
                        fetch(`/admin/ajax/sub-subcategories/${this.selectedSubcategory}`)
                            .then(res => res.json())
                            .then(data => {
                                this.subSubcategories = data;
                                this.loadingSubSub = false;
                            });
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
                 }">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                    <h3 class="font-bold text-slate-900 uppercase text-xs tracking-widest flex items-center">
                        <span class="w-2 h-6 bg-indigo-600 rounded-full mr-3"></span>
                        Product Classification
                    </h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Step-by-step selection</span>
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
                            <select name="subcategory_id" x-model="selectedSubcategory" @change="fetchSubSubcategories()" class="w-full rounded-xl border-slate-200 text-sm py-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer bg-white shadow-sm disabled:bg-slate-50 disabled:cursor-not-allowed" :disabled="loadingSub || !selectedCategory || subcategories.length === 0">
                                <option value="">Select Sub Category</option>
                                <template x-for="sub in subcategories" :key="sub.id">
                                    <option :value="sub.id" x-text="sub.name"></option>
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
                            <select name="sub_subcategory_id" x-model="selectedSubSubcategory" class="w-full rounded-xl border-slate-200 text-sm py-3 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer bg-white shadow-sm disabled:bg-slate-50 disabled:cursor-not-allowed" :disabled="loadingSubSub || !selectedSubcategory || subSubcategories.length === 0">
                                <option value="">Select Sub Sub Category</option>
                                <template x-for="subsub in subSubcategories" :key="subsub.id">
                                    <option :value="subsub.id" x-text="subsub.name"></option>
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

                <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer" id="is_active_toggle">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <label for="is_active_toggle" class="ml-3 text-sm font-bold text-slate-700 cursor-pointer">Publish to Storefront</label>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Live Preview Active</span>
                </div>

            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
                <h3 class="font-bold text-slate-900 border-b pb-2 uppercase text-xs text-indigo-600">Dealer Pricing (B2B)</h3>
                <div class="grid grid-cols-1 gap-2 mt-4 max-h-[200px] overflow-y-auto">
                    @foreach($dealers as $dealer)
                        <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="text-xs font-bold text-slate-700">{{ $dealer->company_name ?? $dealer->name }}</span>
                            <div class="relative w-24">
                                <input type="number" name="dealer_discounts[{{ $dealer->id }}]" placeholder="0" class="w-full pr-8 py-1 rounded border-slate-200 text-xs text-right">
                                <span class="absolute right-2 top-1.5 text-slate-400 text-[10px]">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h3 class="text-xs font-bold text-indigo-600 uppercase mb-6 border-b pb-2">Order Quantity Limits</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-4 bg-indigo-50/30 rounded-xl border border-indigo-100">
                    <h4 class="text-[10px] font-black text-indigo-400 uppercase mb-3">Global Limits</h4>
                    <div class="space-y-3">
                        <input type="number" name="min_order_qty" placeholder="Min" class="w-full rounded-lg border-indigo-100 text-sm">
                        <input type="number" name="max_order_qty" placeholder="Max" class="w-full rounded-lg border-indigo-100 text-sm">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase mb-3">B2B (Dealers)</h4>
                    <div class="space-y-3">
                        <input type="number" name="min_order_qty_b2b" placeholder="Min" class="w-full rounded-lg border-slate-200 text-sm">
                        <input type="number" name="max_order_qty_b2b" placeholder="Max" class="w-full rounded-lg border-slate-200 text-sm">
                    </div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase mb-3">B2C (Customers)</h4>
                    <div class="space-y-3">
                        <input type="number" name="min_order_qty_b2c" placeholder="Min" class="w-full rounded-lg border-slate-200 text-sm">
                        <input type="number" name="max_order_qty_b2c" placeholder="Max" class="w-full rounded-lg border-slate-200 text-sm">
                    </div>
                </div>
            </div>
        </div> -->

        <div class="flex justify-end gap-4 p-4 sticky bottom-0 bg-white/90 backdrop-blur-md border-t">
            <button type="submit" class="px-12 py-3 bg-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 transition-all hover:bg-indigo-700">
                Save & Publish Product
            </button>
        </div>
    </form>
</div>
<script>
function validateImageFiles(input) {
    const allowedExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
    const errorDiv = document.getElementById('image-error');
    errorDiv.classList.add('hidden');
    errorDiv.innerText = '';
    
    if (input.files) {
        for (let i = 0; i < input.files.length; i++) {
            const fileName = input.files[i].name;
            const ext = fileName.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(ext)) {
                errorDiv.innerText = 'Unsupported file format selected. Please choose JPEG, PNG, JPG, GIF, or WEBP images only.';
                errorDiv.classList.remove('hidden');
                input.value = ''; // clear selection
                return;
            }
        }
    }
}
</script>
@endsection