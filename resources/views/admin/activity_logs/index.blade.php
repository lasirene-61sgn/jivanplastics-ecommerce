@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">System Activity Logs</h2>
    </div>

    <!-- Active Admins Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            Recent Logins & Active Sessions
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($activeAdmins as $admin)
            <div class="p-4 rounded-xl border {{ $admin->id === auth('admin')->id() ? 'border-indigo-200 bg-indigo-50/50' : 'border-slate-100 bg-slate-50' }}">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-slate-800">{{ $admin->name }}</span>
                    @if($admin->id === auth('admin')->id())
                        <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-black rounded uppercase">You</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500 space-y-1">
                    <p><span class="font-semibold text-slate-600">Last Login:</span> {{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Unknown' }}</p>
                    <p><span class="font-semibold text-slate-600">Current IP:</span> <span class="font-mono bg-white px-1 py-0.5 rounded border border-slate-200">{{ $admin->current_login_ip ?? 'Unknown' }}</span></p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Action History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <th class="px-6 py-4">Time</th>
                        <th class="px-6 py-4">Admin</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Item Type</th>
                        <th class="px-6 py-4">Details</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-xs font-semibold text-slate-700">{{ $log->created_at->format('M d, Y h:i A') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                        {{ substr($log->admin->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-700">{{ $log->admin->name ?? 'System/Deleted User' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $actionColors = [
                                        'created' => 'bg-green-100 text-green-700',
                                        'updated' => 'bg-blue-100 text-blue-700',
                                        'deleted' => 'bg-red-100 text-red-700'
                                    ];
                                    $colorClass = $actionColors[$log->action] ?? 'bg-slate-100 text-slate-700';
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $colorClass }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold text-slate-600">{{ $log->model_type }}</span>
                                @if($log->model_id)
                                    <span class="text-[10px] text-slate-400">#{{ $log->model_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->details && isset($log->details['name']))
                                    <span class="text-xs font-bold text-slate-800">{{ $log->details['name'] }}</span>
                                @else
                                    <span class="text-xs italic text-slate-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded border border-slate-200 block mb-1">
                                    {{ $log->ip_address ?? 'Unknown' }}
                                </span>
                                @if($log->details && isset($log->details['device']))
                                    <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                                        @if($log->details['device'] == 'Mobile')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        @elseif($log->details['device'] == 'Tablet')
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        @else
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        @endif
                                        <span>{{ $log->details['device'] }} ({{ $log->details['browser'] ?? 'Unknown' }})</span>
                                    </div>
                                @endif
                                @if($log->details && isset($log->details['pc_name']))
                                    <div class="text-[9px] text-slate-400 mt-0.5 truncate max-w-[150px]" title="{{ $log->details['pc_name'] }}">
                                        Host: {{ $log->details['pc_name'] }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-sm">
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($logs->hasPages())
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
