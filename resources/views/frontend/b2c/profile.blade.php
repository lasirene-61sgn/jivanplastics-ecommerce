@extends('frontend.b2c.layouts.app')

@section('title', 'My Profile - V4 Kitchen Partner')

@section('content')
<div class="space-y-8">

    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-black text-slate-900">My Profile</h1>
        <p class="text-slate-400 text-sm mt-1">Manage your personal information and address</p>
    </div>

    {{-- Avatar Card --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 flex items-center gap-6">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-rose-500 to-red-700 flex items-center justify-center text-white text-3xl font-black uppercase shadow-lg shadow-rose-200">
            {{ substr($customer->name, 0, 1) }}
        </div>
        <div>
            <h2 class="text-xl font-black text-slate-900">{{ $customer->name }}</h2>
            <p class="text-slate-400 text-sm mt-0.5">{{ $customer->email }}</p>
            @if($customer->phone)
            <p class="text-slate-500 text-sm mt-1 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                {{ $customer->phone }}
            </p>
            @endif
        </div>
    </div>

    <form action="{{ route('b2c.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Account Information --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 mb-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-500 mb-6 pb-4 border-b border-slate-100">Account Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $customer->name) }}" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all @error('name') border-rose-400 @enderror">
                    @error('name')
                    <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $customer->email) }}" required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all @error('email') border-rose-400 @enderror">
                    @error('email')
                    <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all @error('phone') border-rose-400 @enderror">
                    @error('phone')
                    <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Address Information --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 mb-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-slate-500 mb-6 pb-4 border-b border-slate-100">Address Information</h3>
            <div class="space-y-5">
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Street Address</label>
                    <textarea id="address" name="address" rows="3"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all resize-none @error('address') border-rose-400 @enderror">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                    <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $customer->city) }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all @error('city') border-rose-400 @enderror">
                        @error('city')
                        <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="state" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">State</label>
                        <input type="text" id="state" name="state" value="{{ old('state', $customer->state) }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all @error('state') border-rose-400 @enderror">
                        @error('state')
                        <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="zip_code" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">ZIP Code</label>
                        <input type="text" id="zip_code" name="zip_code" value="{{ old('zip_code', $customer->zip_code) }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent transition-all @error('zip_code') border-rose-400 @enderror">
                        @error('zip_code')
                        <p class="text-rose-500 text-xs font-medium mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 px-8 py-3 rounded-xl bg-gradient-to-r from-rose-600 to-red-700 text-white text-sm font-bold uppercase tracking-wide hover:shadow-lg hover:shadow-rose-200 transition-all duration-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5 13l4 4L19 7"></path>
                </svg>
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection