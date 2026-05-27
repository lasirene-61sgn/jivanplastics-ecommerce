@extends('layouts.admin')

@section('title', 'Edit Customer - Admin Panel')

@section('header', 'Edit Profile')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="flex items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 truncate max-w-[200px] sm:max-w-full">Edit: {{ $customer->name }}</h2>
            <p class="text-sm text-slate-500 italic">Modify account status or technical details.</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Cancel & Return
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        @if($errors->any())
            <div class="m-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm font-medium">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.customers.update', $customer) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" required>
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" required>
                </div>

                <div class="space-y-2" id="password_field">
                    <label for="password" class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center justify-between">
                        Set New Password
                        <span class="text-[10px] text-slate-400 italic normal-case">Leave blank to keep current</span>
                    </label>
                    <input type="password" id="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label for="phone" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                </div>

                <div class="space-y-2">
                    <label for="customer_type" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Category *</label>
                    <select name="customer_type" id="customer_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-white font-semibold cursor-pointer" required>
                        <option value="individual" {{ (old('customer_type', $customer->customer_type) == 'individual') ? 'selected' : '' }}>Individual (B2C)</option>
                        <option value="dealer" {{ (old('customer_type', $customer->customer_type) == 'dealer') ? 'selected' : '' }}>Dealer (B2B)</option>
                    </select>
                </div>
            </div>

            <div id="dealer_fields" class="space-y-6 pt-6 border-t border-slate-100 transition-all duration-300">
                <h4 class="text-xs font-black text-indigo-500 uppercase tracking-widest flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Business Profile
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="company_name" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $customer->company_name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="gst_number" class="text-sm font-bold text-slate-700 uppercase tracking-wider">GST Number</label>
                        <input type="text" id="gst_number" name="gst_number" value="{{ old('gst_number', $customer->gst_number) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none font-mono">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <div class="space-y-2">
                        <label for="is_cod_allowed" class="text-sm font-bold text-slate-700 uppercase tracking-wider">COD Allowed</label>
                        <select name="is_cod_allowed" id="is_cod_allowed" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-white font-semibold cursor-pointer">
                            <option value="1" {{ (old('is_cod_allowed', $customer->is_cod_allowed) == 1) ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ (old('is_cod_allowed', $customer->is_cod_allowed) == 0) ? 'selected' : '' }}>No (Bank Transfer Only)</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="bank_transfer_discount" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Bank Transfer Discount (%)</label>
                        <input type="number" step="0.01" id="bank_transfer_discount" name="bank_transfer_discount" value="{{ old('bank_transfer_discount', $customer->bank_transfer_discount) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="space-y-6 pt-6 border-t border-slate-100">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Address & Region</h4>
                
                <div class="space-y-2">
                    <label for="address" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Street Address</label>
                    <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="city" class="text-sm font-bold text-slate-700 uppercase tracking-wider">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $customer->city) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="state" class="text-sm font-bold text-slate-700 uppercase tracking-wider">State</label>
                        <input type="text" id="state" name="state" value="{{ old('state', $customer->state) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="zip_code" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Zip Code</label>
                        <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', $customer->zip_code) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="country" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Country</label>
                    <input type="text" id="country" name="country" value="{{ old('country', $customer->country) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                </div>

                <div class="pt-4 flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full border border-slate-200 shadow-inner"></div>
                        <span class="ml-3 text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">Customer Account Status: Active</span>
                    </label>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center uppercase tracking-widest">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Commit Profile Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerTypeSelect = document.getElementById('customer_type');
        const dealerFields = document.getElementById('dealer_fields');
        const passwordField = document.getElementById('password_field');
        
        function toggleFields(type) {
            if (type === 'dealer') {
                dealerFields.style.display = 'block';
                passwordField.style.display = 'block';
            } else {
                dealerFields.style.display = 'none';
                passwordField.style.display = 'none';
            }
        }

        // Run on load
        toggleFields(customerTypeSelect.value);

        // Run on change
        customerTypeSelect.addEventListener('change', function() {
            toggleFields(this.value);
        });
    });
</script>
@endsection