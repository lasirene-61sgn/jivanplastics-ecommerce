<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalesTeam;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SalesTeamApiController extends Controller
{
    /**
     * Login Sales Team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $salesTeam = SalesTeam::where('email', $request->email)->first();

        if (!$salesTeam || !Hash::check($request->password, $salesTeam->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        if (!$salesTeam->is_active) {
            return response()->json([
                'error' => 'Account is deactivated'
            ], 403);
        }

        $token = $salesTeam->createToken('SalesTeamToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'sales_team' => $salesTeam,
            'token' => $token
        ]);
    }

    /**
     * Logout Sales Team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Refresh Sales Team member token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $salesTeam = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $salesTeam->createToken('SalesTeamToken')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'sales_team' => $salesTeam,
            'token' => $token
        ]);
    }

    /**
     * Get authenticated Sales Team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'sales_team' => $request->user()
        ]);
    }

    /**
     * Get Sales Team dashboard data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        $salesTeam = $request->user();

        $totalOrders = Order::where('sales_team_id', $salesTeam->id)->count();
        $pendingOrders = Order::where('sales_team_id', $salesTeam->id)
            ->where('status', 'pending')
            ->count();
        $completedOrders = Order::where('sales_team_id', $salesTeam->id)
            ->where('status', 'completed')
            ->count();

        $recentOrders = Order::where('sales_team_id', $salesTeam->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->with('customer')
            ->get();

        return response()->json([
            'sales_team' => $salesTeam,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'recent_orders' => $recentOrders
        ]);
    }

    /**
     * Get all orders assigned to Sales Team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request)
    {
        $salesTeam = $request->user();

        $orders = Order::where('sales_team_id', $salesTeam->id)
            ->with(['customer', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'orders' => $orders
        ]);
    }

    /**
     * Get order details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderDetails(Request $request, $orderId)
    {
        $salesTeam = $request->user();

        $order = Order::where('sales_team_id', $salesTeam->id)
            ->where('id', $orderId)
            ->with(['customer', 'items.product'])
            ->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'order' => $order
        ]);
    }

    /**
     * Update order status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrderStatus(Request $request, $orderId)
    {
        $salesTeam = $request->user();

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::where('sales_team_id', $salesTeam->id)
            ->where('id', $orderId)
            ->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    /**
     * Get all customers assigned to Sales Team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function customers(Request $request)
    {
        $salesTeam = $request->user();

        $customers = Customer::where('sales_team_id', $salesTeam->id)
            ->paginate(10);

        return response()->json([
            'customers' => $customers
        ]);
    }

    /**
     * Get customer details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $customerId
     * @return \Illuminate\Http\JsonResponse
     */
    public function customerDetails(Request $request, $customerId)
    {
        $salesTeam = $request->user();

        $customer = Customer::where('sales_team_id', $salesTeam->id)
            ->where('id', $customerId)
            ->first();

        if (!$customer) {
            return response()->json([
                'error' => 'Customer not found'
            ], 404);
        }

        return response()->json([
            'customer' => $customer
        ]);
    }

    /**
     * Get Sales Team profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $salesTeam = $request->user();

        return response()->json([
            'sales_team' => $salesTeam
        ]);
    }

    /**
     * Update Sales Team profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $salesTeam = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
        ]);

        $salesTeam->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'sales_team' => $salesTeam
        ]);
    }

    /**
     * Change Sales Team password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $salesTeam = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $salesTeam->password)) {
            return response()->json([
                'error' => 'Current password is incorrect'
            ], 400);
        }

        $salesTeam->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}