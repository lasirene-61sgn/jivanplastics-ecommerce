<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManufacturingTeam;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManufacturingTeamApiController extends Controller
{
    /**
     * Login Manufacturing Team member.
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

        $manufacturingTeam = ManufacturingTeam::where('email', $request->email)->first();

        if (!$manufacturingTeam || !Hash::check($request->password, $manufacturingTeam->password)) {
            return response()->json([
                'error' => 'Invalid credentials'
            ], 401);
        }

        if (!$manufacturingTeam->is_active) {
            return response()->json([
                'error' => 'Account is deactivated'
            ], 403);
        }

        $token = $manufacturingTeam->createToken('ManufacturingTeamToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'manufacturing_team' => $manufacturingTeam,
            'token' => $token
        ]);
    }

    /**
     * Logout Manufacturing Team member.
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
     * Refresh Manufacturing Team member token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $manufacturingTeam = $request->user();
        $request->user()->currentAccessToken()->delete();
        $token = $manufacturingTeam->createToken('ManufacturingTeamToken')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed successfully',
            'manufacturing_team' => $manufacturingTeam,
            'token' => $token
        ]);
    }

    /**
     * Get authenticated Manufacturing Team member.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json([
            'manufacturing_team' => $request->user()
        ]);
    }

    /**
     * Get Manufacturing Team dashboard data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard(Request $request)
    {
        $manufacturingTeam = $request->user();

        $totalOrders = Order::where('manufacturing_team_id', $manufacturingTeam->id)->count();
        $pendingOrders = Order::where('manufacturing_team_id', $manufacturingTeam->id)
            ->where('status', 'pending')
            ->count();
        $processingOrders = Order::where('manufacturing_team_id', $manufacturingTeam->id)
            ->where('status', 'processing')
            ->count();

        $recentOrders = Order::where('manufacturing_team_id', $manufacturingTeam->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->with('customer')
            ->get();

        return response()->json([
            'manufacturing_team' => $manufacturingTeam,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
            'recent_orders' => $recentOrders
        ]);
    }

    /**
     * Get all orders assigned to Manufacturing Team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders(Request $request)
    {
        $manufacturingTeam = $request->user();

        $orders = Order::where('manufacturing_team_id', $manufacturingTeam->id)
            ->with(['customer', 'items.product'])
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
        $manufacturingTeam = $request->user();

        $order = Order::where('manufacturing_team_id', $manufacturingTeam->id)
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
        $manufacturingTeam = $request->user();

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order = Order::where('manufacturing_team_id', $manufacturingTeam->id)
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
     * Get all products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function products(Request $request)
    {
        $products = Product::with(['category', 'subcategory', 'subSubcategory', 'images'])
            ->paginate(15);

        return response()->json([
            'products' => $products
        ]);
    }

    /**
     * Get product details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function productDetails(Request $request, $productId)
    {
        $product = Product::with(['category', 'subcategory', 'subSubcategory', 'images'])->find($productId);

        if (!$product) {
            return response()->json([
                'error' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Get Manufacturing Team profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $manufacturingTeam = $request->user();

        return response()->json([
            'manufacturing_team' => $manufacturingTeam
        ]);
    }

    /**
     * Update Manufacturing Team profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $manufacturingTeam = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
        ]);

        $manufacturingTeam->update($request->only(['name', 'phone', 'address']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'manufacturing_team' => $manufacturingTeam
        ]);
    }

    /**
     * Change Manufacturing Team password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $manufacturingTeam = $request->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $manufacturingTeam->password)) {
            return response()->json([
                'error' => 'Current password is incorrect'
            ], 400);
        }

        $manufacturingTeam->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}