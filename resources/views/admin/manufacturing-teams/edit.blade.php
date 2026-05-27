@extends('layouts.admin')

@section('title', 'Edit Manufacturing Team')

@section('header', 'Edit Team Details')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <nav class="flex mb-6 text-sm text-slate-500">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.manufacturing-teams.index') }}" class="hover:text-indigo-600 transition-colors">Manufacturing Teams</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
            <li class="font-medium text-slate-900 italic">{{ $manufacturingTeam->factory_name }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-xl font-bold text-slate-900">Update Team Information</h3>
            <p class="text-sm text-slate-500 mt-1 italic text-indigo-600">Modify profile or reset password.</p>
        </div>

        <form action="{{ route('admin.manufacturing-teams.update', $manufacturingTeam) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="factory_name" class="text-sm font-semibold text-slate-700">Factory Name *</label>
                    <input type="text" name="factory_name" id="factory_name" value="{{ old('factory_name', $manufacturingTeam->factory_name) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>
                </div>

                <div class="space-y-2">
                    <label for="contact_person" class="text-sm font-semibold text-slate-700">Contact Person *</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $manufacturingTeam->contact_person) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-slate-700">Email Address *</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $manufacturingTeam->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>
                </div>

                <div class="space-y-2">
                    <label for="phone" class="text-sm font-semibold text-slate-700">Phone *</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $manufacturingTeam->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-slate-700 italic">Reset Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="password" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-slate-700">Confirm Reset Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                </div>
            </div>

            <div class="space-y-2">
                <label for="address" class="text-sm font-semibold text-slate-700">Address *</label>
                <textarea name="address" id="address" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none" required>{{ old('address', $manufacturingTeam->address) }}</textarea>
            </div>

            <div class="space-y-2 text-xs text-slate-400">
                <label for="manufacturing_unit_type" class="text-sm font-semibold text-slate-700 block">Unit Type</label>
                <input type="text" name="manufacturing_unit_type" id="manufacturing_unit_type" value="{{ old('manufacturing_unit_type', $manufacturingTeam->manufacturing_unit_type) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
            </div>

            <div class="pt-4 flex items-center">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" {{ old('is_active', $manufacturingTeam->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ml-3 text-sm font-bold text-slate-700">Account Status Active</span>
                </label>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.manufacturing-teams.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    Save Updates
                </button>
            </div>
        </form>
    </div>
</div>
@endsection