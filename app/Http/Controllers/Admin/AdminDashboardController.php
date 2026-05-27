<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use App\Models\Customer;
use App\Models\Order;
use App\Models\ManufacturingTeam;
use App\Models\SalesTeam;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalDealers = Customer::dealers()->count();
        $totalUsers = Customer::individuals()->count();
        
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $rejectedOrders = Order::where('status', 'rejected')->count();
        
        $manufacturingTeamCount = ManufacturingTeam::count();
        $salesTeamCount = SalesTeam::count();
        
        $b2bOrders = Order::where('customer_type', 'dealer')->count();
        $b2cOrders = Order::where('customer_type', 'individual')->count();
        
        $latestOrders = Order::with('customer')->latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'totalDealers',
            'totalUsers',
            'totalOrders',
            'completedOrders',
            'processingOrders',
            'rejectedOrders',
            'manufacturingTeamCount',
            'salesTeamCount',
            'b2bOrders',
            'b2cOrders',
            'latestOrders'
        ));
    }
}
