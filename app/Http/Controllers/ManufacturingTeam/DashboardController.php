<?php

namespace App\Http\Controllers\ManufacturingTeam;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the manufacturing team dashboard.
     */
    public function index()
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        // Get orders allocated to this manufacturing team
        $orders = Order::where('manufacturing_team_id', $manufacturingTeam->id)
            ->with('customer')
            ->latest()
            ->paginate(20);
        
        return view('manufacturing-team.dashboard', compact('manufacturingTeam', 'orders'));
    }
    
    /**
     * Display the specified order.
     */
    public function showOrder(Order $order)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        // Ensure the order belongs to this manufacturing team
        if ($order->manufacturing_team_id != $manufacturingTeam->id) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        $order->load('items.product', 'customer');
        
        return view('manufacturing-team.orders.show', compact('order'));
    }
    
    /**
     * Update the manufacturing status of an order.
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        // Ensure the order belongs to this manufacturing team
        if ($order->manufacturing_team_id != $manufacturingTeam->id) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        $request->validate([
            'manufacturing_status' => 'required|in:processing,completed,rejected',
            'tentative_dispatch_date' => 'nullable|date|after_or_equal:today',
        ]);
        
        // Validation: Prevent marking as completed if items are still pending
        if ($request->manufacturing_status === 'completed') {
            $pendingQuantity = $order->items->sum(function($item) {
                return $item->manufacturing_pending_quantity;
            });
            
            if ($pendingQuantity > 0) {
                return redirect()->back()->with('error', 'Cannot mark as Completed. There are ' . $pendingQuantity . ' units pending manufacturing/rejection logging.');
            }
        }
        
        $updateData = [
            'manufacturing_status' => $request->manufacturing_status,
        ];
        
        // Set timestamps based on status
        switch ($request->manufacturing_status) {
            case 'processing':
                if (!$request->tentative_dispatch_date && !$order->tentative_dispatch_date) {
                    return redirect()->back()->with('error', 'Tentative dispatch date is required when accepting an order.');
                }
                $updateData['allocated_at'] = $updateData['allocated_at'] ?? now();
                $updateData['status'] = 'under_process';
                if ($request->has('tentative_dispatch_date')) {
                    $updateData['tentative_dispatch_date'] = $request->tentative_dispatch_date;
                }
                break;
            case 'completed':
                $updateData['completed_at'] = now();
                break;
            case 'rejected':
                $updateData['manufacturing_team_id'] = null;
                $updateData['status'] = 'rejected';
                break;
        }
        
        $order->update($updateData);
        
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
    
    /**
     * Partially complete items in an order.
     */
    public function partialComplete(Request $request, Order $order)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        // Ensure the order belongs to this manufacturing team
        if ($order->manufacturing_team_id != $manufacturingTeam->id) {
            abort(403, 'Unauthorized access to this order.');
        }
        
        $request->validate([
            'completed_pieces' => 'required|array',
            'completed_pieces.*' => 'integer|min:0',
            'rejected_pieces' => 'nullable|array',
            'rejected_pieces.*' => 'integer|min:0',
            'rejection_reasons' => 'nullable|array',
            'rejection_reasons.*' => 'nullable|string',
        ]);
        
        $totalCompletedPieces = 0;
        $totalRejectedPieces = 0;
        
        // Update manufactured and rejected quantities for each item
        foreach ($request->completed_pieces as $itemId => $completedPieces) {
            $orderItem = OrderItem::where('id', $itemId)
                ->where('order_id', $order->id)
                ->first();
                
            if ($orderItem && $orderItem->per_unit_pieces > 0) {
                $rejectedPieces = isset($request->rejected_pieces[$itemId]) ? (int)$request->rejected_pieces[$itemId] : 0;
                $rejectionReason = isset($request->rejection_reasons[$itemId]) ? $request->rejection_reasons[$itemId] : null;

                $canProcessPieces = $orderItem->manufacturing_pending_pieces;
                $totalToProcessPieces = $completedPieces + $rejectedPieces;
                
                // Clamp inputs to pending pieces
                if ($totalToProcessPieces > $canProcessPieces && $canProcessPieces > 0) {
                     $actualCompletedPieces = min($completedPieces, $canProcessPieces);
                     $remainingPieces = $canProcessPieces - $actualCompletedPieces;
                     $actualRejectedPieces = min($rejectedPieces, $remainingPieces);
                } else {
                     $actualCompletedPieces = $completedPieces;
                     $actualRejectedPieces = $rejectedPieces;
                }
                
                if ($actualCompletedPieces > 0 || $actualRejectedPieces > 0) {
                    $newManufacturedPieces = $orderItem->manufactured_pieces + $actualCompletedPieces;
                    $newRejectedPieces = $orderItem->rejected_pieces + $actualRejectedPieces;
                    
                    // Convert pieces back to units for database consistency
                    $newManufacturedUnits = $newManufacturedPieces / $orderItem->per_unit_pieces;
                    $newRejectedUnits = $newRejectedPieces / $orderItem->per_unit_pieces;

                    $orderItem->update([
                        'manufactured_quantity' => $newManufacturedUnits,
                        'rejected_quantity' => $newRejectedUnits,
                        'manufactured_pieces' => $newManufacturedPieces,
                        'rejected_pieces' => $newRejectedPieces,
                        'rejection_reason' => $rejectionReason ? ($orderItem->rejection_reason ? $orderItem->rejection_reason . '; ' . $rejectionReason : $rejectionReason) : $orderItem->rejection_reason,
                    ]);
                    
                    $totalCompletedPieces += $actualCompletedPieces;
                    $totalRejectedPieces += $actualRejectedPieces;
                }
            }
        }
        
        // Check if everything is manufactured/rejected (using pieces since we are tracking pieces now)
        $stillPendingPieces = $order->items->sum(function($item) {
            return $item->manufacturing_pending_pieces;
        });

        if ($stillPendingPieces <= 0) {
            $order->update([
                'manufacturing_status' => 'completed',
                'completed_at' => now(),
            ]);
            
            return redirect()->back()->with('success', 'All pieces processed. Order marked as Manufacturing Completed.');
        } elseif ($totalCompletedPieces > 0 || $totalRejectedPieces > 0) {
            return redirect()->back()->with('success', $totalCompletedPieces . ' pieces marked as manufactured, ' . $totalRejectedPieces . ' pieces rejected. ' . $stillPendingPieces . ' pieces remain.');
        } else {
            return redirect()->back()->with('info', 'No manufacturing progress was logged.');
        }
    }
    
    /**
     * Bulk accept orders by manufacturing team.
     */
    public function bulkAcceptOrders(Request $request)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'tentative_dispatch_date' => 'required|date|after_or_equal:today',
        ]);
        
        // Verify that all orders belong to this manufacturing team
        $orders = Order::whereIn('id', $request->order_ids)
            ->where('manufacturing_team_id', $manufacturingTeam->id)
            ->get();
        
        // Update status to processing for all orders
        foreach ($orders as $order) {
            $order->update([
                'manufacturing_status' => 'processing',
                'allocated_at' => $order->allocated_at ?? now(),
                'status' => 'under_process',
                'tentative_dispatch_date' => $request->tentative_dispatch_date,
            ]);
        }
        
        return redirect()->back()->with('success', 'Orders accepted and marked as processing successfully.');
    }
    
    /**
     * Bulk update status for multiple orders.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'manufacturing_status' => 'required|in:processing,completed,rejected',
        ]);
        
        // Verify that all orders belong to this manufacturing team
        $orders = Order::whereIn('id', $request->order_ids)
            ->where('manufacturing_team_id', $manufacturingTeam->id)
            ->get();
        
        // Update status for all orders
        foreach ($orders as $order) {
            $updateData = [
                'manufacturing_status' => $request->manufacturing_status,
            ];
            
            // Set timestamps based on status
            switch ($request->manufacturing_status) {
                case 'processing':
                    $updateData['allocated_at'] = $order->allocated_at ?? now();
                    $updateData['status'] = 'under_process';
                    break;
                case 'completed':
                    // Check pending quantity for this order
                    $pendingQuantity = $order->items->sum(function($item) {
                        return $item->manufacturing_pending_quantity;
                    });
                    
                    if ($pendingQuantity > 0) {
                        continue 2; // Skip this order update
                    }
                    $updateData['completed_at'] = now();
                    break;
                case 'rejected':
                    $updateData['manufacturing_team_id'] = null;
                    $updateData['status'] = 'rejected';
                    break;
            }
            
            $order->update($updateData);
        }
        
        return redirect()->back()->with('success', 'Orders status updated successfully.');
    }
}