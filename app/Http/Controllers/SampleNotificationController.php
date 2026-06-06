<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseNotificationService;

class SampleNotificationController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseNotificationService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * 1. Admin allocates to manufacturing team
     */
    public function allocateToManufacturing(Request $request)
    {
        // ... Your database logic to update the order status ...
        $orderId = 12345;

        // Send Notification to Manufacturing Team
        $this->firebase->sendNotification(
            topic: 'manufacturing',
            title: 'New Order Allocated',
            message: "Order #{$orderId} has been allocated to your team.",
            additionalData: ['icon' => 'info', 'order_id' => $orderId]
        );

        return response()->json(['message' => 'Allocated and notified!']);
    }

    /**
     * 2. Manufacturing team completes all products
     */
    public function completeManufacturing(Request $request)
    {
        // ... Your database logic to mark products/order as complete ...
        $orderId = 12345;

        // Send Notification to Admin Panel
        $this->firebase->sendNotification(
            topic: 'admin',
            title: 'Manufacturing Complete',
            message: "Manufacturing for Order #{$orderId} is fully complete.",
            additionalData: ['icon' => 'success', 'order_id' => $orderId]
        );

        return response()->json(['message' => 'Completed and notified!']);
    }

    /**
     * 3. Admin dispatches fully the order (B2B or B2C)
     */
    public function dispatchOrder(Request $request)
    {
        // ... Your database logic to dispatch the order ...
        $orderId = 12345;
        $type = 'B2B'; // or B2C

        // Send Notification to Admin (and possibly a 'sales' or 'customer' topic if needed)
        $this->firebase->sendNotification(
            topic: 'admin', // Or whoever needs to see the dispatch confirmation
            title: "Order Dispatched ({$type})",
            message: "Order #{$orderId} has been successfully dispatched.",
            additionalData: ['icon' => 'success', 'order_id' => $orderId, 'type' => $type]
        );

        return response()->json(['message' => 'Dispatched and notified!']);
    }
}
