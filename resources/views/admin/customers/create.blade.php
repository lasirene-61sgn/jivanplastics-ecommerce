@extends('layouts.admin')

@section('title', 'Add Customer - Admin Panel')

@section('header', 'Customer Registration')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Create New Customer</h2>
            <p class="text-sm text-slate-500 font-medium italic">Register individuals or B2B dealers to your platform.</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl shadow-sm hover:bg-slate-50 transition-all active:scale-95">
            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Customers
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-xl font-bold text-slate-900">Account Profile</h3>
            <p class="text-sm text-slate-500 mt-1">Provide the primary contact information and account type.</p>
        </div>

        @if($errors->any())
            <div class="m-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-r-lg shadow-sm">
                <ul class="list-disc list-inside text-sm font-medium space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.customers.store') }}" method="POST" class="p-8 space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="text-sm font-bold text-slate-700 flex items-center uppercase tracking-wider">
                        Full Name <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-slate-700 flex items-center uppercase tracking-wider">
                        Email Address <span class="text-red-500 ml-1">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>
                </div>

                <div class="space-y-2">
                    <label for="phone" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+91 00000 00000" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                </div>

                <div class="space-y-2">
                    <label for="customer_type" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Customer Category *</label>
                    <select name="customer_type" id="customer_type" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none bg-white font-semibold cursor-pointer" required>
                        <option value="individual" {{ old('customer_type') == 'individual' ? 'selected' : '' }}>Individual (B2C)</option>
                        <option value="dealer" {{ old('customer_type') == 'dealer' ? 'selected' : '' }}>Dealer (B2B)</option>
                    </select>
                </div>
            </div>

            <div id="dealer_fields" class="space-y-6 pt-6 border-t border-slate-100 hidden">
                <h4 class="text-xs font-black text-indigo-500 uppercase tracking-widest">Dealer Business Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="company_name" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Registered Company Name <span class="text-red-500 ml-1">*</span></label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="Acme Corp" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none" required>
                    </div>
                    <div class="space-y-2">
                        <label for="gst_number" class="text-sm font-bold text-slate-700 uppercase tracking-wider">GST Identification Number <span class="text-red-500 ml-1">*</span></label>
                        <input type="text" id="gst_number" name="gst_number" value="{{ old('gst_number') }}" placeholder="22AAAAA0000A1Z5" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none" required>
                    </div>
                </div>
            </div>

            <div id="password_field" class="space-y-2 pt-6 border-t border-slate-100 hidden">
                <label for="password" class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center">
                    Set Portal Password
                    <span class="ml-2 text-[10px] text-slate-400 normal-case">(Required for Dealer Login)</span>
                </label>
                <input type="password" id="password" name="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
            </div>

            <div class="space-y-6 pt-6 border-t border-slate-100">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Shipping & Billing Logistics</h4>
                
                <div class="space-y-2">
                    <label for="address" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Full Street Address <span class="text-red-500 ml-1">*</span></label>
                    <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">{{ old('address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="city" class="text-sm font-bold text-slate-700 uppercase tracking-wider">City <span class="text-red-500 ml-1">*</span></label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="state" class="text-sm font-bold text-slate-700 uppercase tracking-wider">State <span class="text-red-500 ml-1">*</span></label>
                        <input type="text" id="state" name="state" value="{{ old('state') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="zip_code" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Zip Code <span class="text-red-500 ml-1">*</span></label>
                        <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="country" class="text-sm font-bold text-slate-700 uppercase tracking-wider">Country</label>
                    <input type="text" id="country" name="country" value="{{ old('country', 'India') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none">
                </div>

                <div class="pt-4 flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full border border-slate-200"></div>
                        <span class="ml-3 text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">Mark Account as Active</span>
                    </label>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="w-full sm:w-auto px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-200 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center uppercase tracking-widest">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Register Customer
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
                dealerFields.classList.remove('hidden');
                passwordField.classList.remove('hidden');
            } else {
                dealerFields.classList.add('hidden');
                passwordField.classList.add('hidden');
            }
        }

        // Initialize based on current selection
        toggleFields(customerTypeSelect.value);

        // Change listener
        customerTypeSelect.addEventListener('change', function() {
            toggleFields(this.value);
        });
    });
</script>
@endsection