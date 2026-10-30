<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'all'); // 'all', 'b2b', 'b2c', 'admin'
        $userTypeFilter = $request->query('user_type', 'all');
        $actionFilter = $request->query('action', 'all');
        $deviceFilter = $request->query('device', 'all');
        $search = $request->query('search', '');
        $dateFilter = $request->query('date_range', 'all');

        // Query Logs with eager loaded relationships
        $logsQuery = ActivityLog::with(['admin', 'customer'])->latest();

        // If tab is specifically set to b2b, b2c, or admin, automatically filter logs if user_type is not overridden
        if ($activeTab === 'b2b' && $userTypeFilter === 'all') {
            $logsQuery->where('user_type', 'b2b');
        } elseif ($activeTab === 'b2c' && $userTypeFilter === 'all') {
            $logsQuery->where('user_type', 'b2c');
        } elseif ($activeTab === 'admin' && $userTypeFilter === 'all') {
            $logsQuery->where('user_type', 'admin');
        } elseif ($userTypeFilter !== 'all') {
            $logsQuery->where('user_type', $userTypeFilter);
        }

        // Action Filter
        if ($actionFilter !== 'all') {
            $logsQuery->where('action', $actionFilter);
        }

        // Device Filter
        if ($deviceFilter !== 'all') {
            $logsQuery->where('details->device', $deviceFilter);
        }

        // Date Filter
        if ($dateFilter === 'today') {
            $logsQuery->whereDate('created_at', today());
        } elseif ($dateFilter === 'this_week') {
            $logsQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateFilter === 'this_month') {
            $logsQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        // Search Filter
        if (!empty($search)) {
            $logsQuery->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhere('model_id', 'like', "%{$search}%")
                  ->orWhere('details->name', 'like', "%{$search}%")
                  ->orWhere('details->email', 'like', "%{$search}%")
                  ->orWhere('details->company_name', 'like', "%{$search}%")
                  ->orWhere('details->order_number', 'like', "%{$search}%")
                  ->orWhere('details->pc_name', 'like', "%{$search}%")
                  ->orWhereHas('admin', function ($aq) use ($search) {
                      $aq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $logsQuery->paginate(20, ['*'], 'logs_page')->appends($request->query());

        // B2B Customers Tracking Query (Dealers)
        $b2bQuery = Customer::dealers()
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('login_count');

        if ($request->filled('b2b_search')) {
            $term = $request->b2b_search;
            $b2bQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('company_name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        if ($request->b2b_frequency === 'regular') {
            $b2bQuery->where(function ($q) {
                $q->where('login_count', '>=', 5);
            });
        }

        $b2bCustomers = $b2bQuery->paginate(10, ['*'], 'b2b_page')->appends($request->query());

        // B2C Customers Tracking Query (Individuals)
        $b2cQuery = Customer::individuals()
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('login_count');

        if ($request->filled('b2c_search')) {
            $term = $request->b2c_search;
            $b2cQuery->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        $b2cCustomers = $b2cQuery->paginate(10, ['*'], 'b2c_page')->appends($request->query());

        // Active Admins list
        $activeAdmins = Admin::whereNotNull('last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->get();

        // Top KPI Stats
        $stats = [
            'total_logs' => ActivityLog::count(),
            'b2b_logins' => (int) Customer::dealers()->sum('login_count'),
            'b2b_active_dealers' => Customer::dealers()->whereNotNull('last_login_at')->count(),
            'b2b_orders_count' => Order::where('customer_type', 'dealer')->count(),
            'b2b_orders_total' => (float) Order::where('customer_type', 'dealer')->sum('total'),
            'b2c_logins' => (int) Customer::individuals()->sum('login_count'),
            'b2c_active_count' => Customer::individuals()->whereNotNull('last_login_at')->count(),
            'b2c_orders_count' => Order::where('customer_type', 'individual')->count(),
            'b2c_orders_total' => (float) Order::where('customer_type', 'individual')->sum('total'),
            'admin_actions' => ActivityLog::where('user_type', 'admin')->count(),
            // Device counts from activity logs
            'pc_count' => ActivityLog::where('details->device', 'PC')->count(),
            'mobile_count' => ActivityLog::where('details->device', 'Mobile')->count(),
            'tablet_count' => ActivityLog::where('details->device', 'Tablet')->count(),
        ];

        return view('admin.activity_logs.index', compact(
            'logs',
            'b2bCustomers',
            'b2cCustomers',
            'activeAdmins',
            'stats',
            'activeTab',
            'userTypeFilter',
            'actionFilter',
            'deviceFilter',
            'search',
            'dateFilter'
        ));
    }
}
