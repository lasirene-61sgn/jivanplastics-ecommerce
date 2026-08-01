@extends('layouts.admin')

@section('title', 'Add Manufacturing Team')

@section('header', 'Register New Factory')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <nav class="flex mb-6 text-sm text-slate-500">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.manufacturing-teams.index') }}" class="hover:text-indigo-600 transition-colors">Manufacturing Teams</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
            <li class="font-medium text-slate-900">Add Team</li>
        </ol>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-xl font-bold text-slate-900">Team Credentials & Info</h3>
            <p class="text-sm text-slate-500 mt-1">Setup factory details and access credentials for the manufacturing unit.</p>
        </div>

        <form action="{{ route('admin.manufacturing-teams.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="factory_name" class="text-sm font-semibold text-slate-700">Factory Name *</label>
                    <input type="text" name="factory_name" id="factory_name" value="{{ old('factory_name') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" required>
                    @error('factory_name') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="contact_person" class="text-sm font-semibold text-slate-700">Contact Person *</label>
                    <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" required>
                    @error('contact_person') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-slate-700">Official Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" required>
                    @error('email') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="phone" class="text-sm font-semibold text-slate-700">Phone Number *</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" required>
                    @error('phone') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Password Input -->
                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-slate-700">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <!-- Eye Icon -->
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon -->
                            <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.02 10.02 0 013.98-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.123 3.123L2.22 2.22l19.56 19.56" />
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password Input -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-slate-700">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
                        <button type="button" id="togglePasswordConfirmation" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <!-- Eye Icon -->
                            <svg id="eyeIconConfirmation" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Eye Slash Icon -->
                            <svg id="eyeSlashIconConfirmation" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.02 10.02 0 013.98-.863c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m-3.123 3.123L2.22 2.22l19.56 19.56" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label for="address" class="text-sm font-semibold text-slate-700">Full Address *</label>
                <textarea name="address" id="address" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all" required>{{ old('address') }}</textarea>
                @error('address') <p class="text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label for="manufacturing_unit_type" class="text-sm font-semibold text-slate-700">Unit Type (Optional)</label>
                <input type="text" name="manufacturing_unit_type" id="manufacturing_unit_type" value="{{ old('manufacturing_unit_type') }}" placeholder="e.g. Leather Processing, Assembly" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all">
            </div>

            <div class="pt-4 flex items-center">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    <span class="ml-3 text-sm font-bold text-slate-700">Active Account</span>
                </label>
            </div>

            <div class="pt-8 border-t border-slate-100 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.manufacturing-teams.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    Register Manufacturing Team
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function setupPasswordToggle(btnId, inputId, eyeId, eyeSlashId) {
            const button = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            const eyeSlash = document.getElementById(eyeSlashId);

            if (button && input && eye && eyeSlash) {
                button.addEventListener('click', function () {
                    const isPassword = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPassword ? 'text' : 'password');

                    eye.classList.toggle('hidden', isPassword);
                    eyeSlash.classList.toggle('hidden', !isPassword);
                });
            }
        }

        // Toggle for Password
        setupPasswordToggle('togglePassword', 'password', 'eyeIcon', 'eyeSlashIcon');

        // Toggle for Confirm Password
        setupPasswordToggle('togglePasswordConfirmation', 'password_confirmation', 'eyeIconConfirmation', 'eyeSlashIconConfirmation');
    });
</script>
@endsection