@extends('frontend.b2b.layouts.app')

@section('title', 'Partner Profile - V4 Kitchen Partner')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Header -->
    <div class="text-center">
        <h1 class="text-3xl font-black text-slate-900 tracking-tight text-uppercase">Member <span class="text-red-600">Profile</span></h1>
        <p class="text-slate-500 font-medium mt-1">Manage your dealership credentials and business details.</p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 text-sm font-bold animate-in fade-in slide-in-from-top-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('b2b.profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Account Card -->
        <div class="bg-white border border-slate-100 rounded-[2.5rem] p-8 md:p-12 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-4 mb-10 border-b border-slate-50 pb-6">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Account Access</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Primary Contact Details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Authorized Representative</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                    @error('name')<p class="text-[10px] font-bold text-red-500 mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Business Email</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" required
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                    @error('email')<p class="text-[10px] font-bold text-red-500 mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Direct Hotline</label>
                    <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                    @error('phone')<p class="text-[10px] font-bold text-red-500 mt-1 pl-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Business Card -->
        <div class="bg-white border border-slate-100 rounded-[2.5rem] p-8 md:p-12 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-4 mb-10 border-b border-slate-50 pb-6">
                <div class="w-12 h-12 rounded-2xl bg-red-600 flex items-center justify-center text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Legal Entity</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Business Registration Details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Registered Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $customer->company_name) }}" required
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                    @error('company_name')<p class="text-[10px] font-bold text-red-500 mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">GST Identification Number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $customer->gst_number) }}"
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                    @error('gst_number')<p class="text-[10px] font-bold text-red-500 mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Primary Warehouse Address</label>
                    <textarea name="address" rows="3" required
                              class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">{{ old('address', $customer->address) }}</textarea>
                    @error('address')<p class="text-[10px] font-bold text-red-500 mt-1 pl-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">City</label>
                    <input type="text" name="city" value="{{ old('city', $customer->city) }}" required
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">State</label>
                    <input type="text" name="state" value="{{ old('state', $customer->state) }}" required
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Zip / Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode', $customer->pincode) }}" required
                           class="w-full px-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-red-500/10 placeholder-slate-300 transition-all">
                </div>
            </div>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full md:w-auto px-12 py-5 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-600 transition-all shadow-2xl shadow-slate-300 active:scale-95">
                Save Profile Changes
            </button>
        </div>
    </form>
</div>
@endsection