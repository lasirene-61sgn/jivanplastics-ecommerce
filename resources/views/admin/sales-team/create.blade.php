@extends('layouts.admin')

@section('title', 'Create Sales Team Member - Admin Panel')

@section('header', 'Onboard Sales Professional')

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    <nav class="flex mb-6 text-sm text-slate-500">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('admin.sales-team.index') }}" class="hover:text-indigo-600 transition-colors">Sales Team</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></li>
            <li class="font-medium text-slate-900">Add New Representative</li>
        </ol>
    </nav>

    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight italic text-indigo-600">New Sales Executive</h3>
                <p class="text-sm text-slate-500 mt-1 font-medium">Create credentials and assign business dealers to a team member.</p>
            </div>
            <div class="h-12 w-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <form action="{{ route('admin.sales-team.store') }}" method="POST" class="p-8 space-y-10">
            @csrf
            
            <div class="space-y-6">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Auth & Identity</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="name" class="text-xs font-black text-slate-700 uppercase tracking-widest">Legal Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-800 @error('name') border-rose-500 ring-rose-500/10 @enderror" required>
                        @error('name') <p class="text-xs font-bold text-rose-500 mt-1 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-xs font-black text-slate-700 uppercase tracking-widest">Business Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-800 @error('email') border-rose-500 ring-rose-500/10 @enderror" required>
                        @error('email') <p class="text-xs font-bold text-rose-500 mt-1 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-xs font-black text-slate-700 uppercase tracking-widest text-indigo-500">Secure Password *</label>
                        <input type="password" name="password" id="password" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-black text-indigo-900 @error('password') border-rose-500 ring-rose-500/10 @enderror" required>
                        @error('password') <p class="text-xs font-bold text-rose-500 mt-1 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-xs font-black text-slate-700 uppercase tracking-widest">Repeat Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-black text-slate-900" required>
                    </div>
                </div>
            </div>

            <div class="space-y-6 pt-4">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100 pb-2">Logistics & Assignment</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label for="phone" class="text-xs font-black text-slate-700 uppercase tracking-widest">Mobile Reach</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="+91 00000 00000" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-800">
                        @error('phone') <p class="text-xs font-bold text-rose-500 mt-1 italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="department" class="text-xs font-black text-slate-700 uppercase tracking-widest">Unit / Department *</label>
                        <input type="text" name="department" id="department" value="{{ old('department', 'sales') }}" class="w-full px-4 py-4 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-black text-slate-900 uppercase tracking-widest bg-slate-50 shadow-inner" required>
                        @error('department') <p class="text-xs font-bold text-rose-500 mt-1 italic">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="assigned_dealers" class="text-xs font-black text-indigo-500 uppercase tracking-[0.2em] flex items-center">
                        Assign Dealer Portfolio
                        <span class="ml-2 text-[9px] text-slate-400 normal-case font-bold tracking-normal italic">(Optional Assignment)</span>
                    </label>
                    <div class="relative group">
                        <select name="assigned_dealers[]" id="assigned_dealers" multiple class="w-full px-4 py-4 rounded-[1.5rem] border border-slate-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none bg-slate-50 font-bold text-slate-700 min-h-[180px] custom-scrollbar @error('assigned_dealers') border-rose-500 @enderror">
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}" class="py-2 px-2 border-b border-slate-100 last:border-0 hover:bg-indigo-50 cursor-pointer" {{ in_array($dealer->id, old('assigned_dealers', [])) ? 'selected' : '' }}>
                                    {{ $dealer->company_name ?? $dealer->name }} — ({{ $dealer->email }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute right-4 bottom-4 text-[9px] font-black text-slate-300 uppercase tracking-widest pointer-events-none group-hover:text-indigo-400 transition-colors">
                            Hold Cmd / Ctrl to multi-select
                        </div>
                    </div>
                    @error('assigned_dealers') <p class="text-xs font-bold text-rose-500 mt-1 italic">{{ $message }}</p> @enderror
                </div>

                <div class="pt-6 flex items-center border-t border-slate-50">
                    <label class="relative inline-flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full border border-slate-200 shadow-inner"></div>
                        <span class="ml-4 text-sm font-bold text-slate-700 group-hover:text-indigo-600 transition-colors uppercase tracking-widest">Immediate Account Activation</span>
                    </label>
                </div>
            </div>

            <div class="pt-10 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('admin.sales-team.index') }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Discard Changes
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Commit Enrollment
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-hover { background: #cbd5e1; }
</style>
@endsection