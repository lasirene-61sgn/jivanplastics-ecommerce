<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::with('admin')->latest()->paginate(20);
        $activeAdmins = Admin::whereNotNull('last_login_at')
                            ->orderBy('last_login_at', 'desc')
                            ->get();

        return view('admin.activity_logs.index', compact('logs', 'activeAdmins'));
    }
}
