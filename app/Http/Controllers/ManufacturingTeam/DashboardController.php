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
    public function index(Request $request)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        $tab = $request->query('tab', 'allocated');
        
        $baseQuery = Order::where('manufacturing_team_id', $manufacturingTeam->id);
        
        $allocatedCount = (clone $baseQuery)->where('manufacturing_status', 'allocated')->count();
        $acceptedCount = (clone $baseQuery)->where('manufacturing_status', 'processing')->count();
        $completedCount = (clone $baseQuery)->where('manufacturing_status', 'completed')->count();
        
        $query = (clone $baseQuery)->with('customer');
        
        if ($tab === 'allocated') {
            $query->where('manufacturing_status', 'allocated');
        } elseif ($tab === 'accepted') {
            $query->where('manufacturing_status', 'processing');
        } elseif ($tab === 'completed') {
            $query->where('manufacturing_status', 'completed');
        }
        
        $orders = $query->latest()->paginate(20);
        $orders->appends(['tab' => $tab]);

        $returnRequestsCount = \App\Models\ReturnRequest::whereHas('order', function($q) use ($manufacturingTeam) {
            $q->where('manufacturing_team_id', $manufacturingTeam->id);
        })->count();
        
        $returnRequests = null;
        if ($tab === 'returns') {
            $returnRequests = \App\Models\ReturnRequest::with(['order', 'orderItem.product'])
                ->whereHas('order', function($q) use ($manufacturingTeam) {
                    $q->where('manufacturing_team_id', $manufacturingTeam->id);
                })
                ->latest()
                ->paginate(20);
            $returnRequests->appends(['tab' => $tab]);
        }
        
        return view('manufacturing-team.dashboard', compact('manufacturingTeam', 'orders', 'returnRequests', 'tab', 'allocatedCount', 'acceptedCount', 'completedCount', 'returnRequestsCount'));
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
        
        // Notify admin if completed
        if (isset($updateData['manufacturing_status']) && $updateData['manufacturing_status'] === 'completed') {
            try {
                app(\App\Services\FirebaseNotificationService::class)->sendNotification(
                    'admin',
                    'Manufacturing Complete',
                    "Manufacturing for Order #{$order->order_number} is fully complete.",
                    ['icon' => 'success', 'order_id' => $order->id]
                );
            } catch (\Exception $e) {
                \Log::error("Admin Completion Notification Error: " . $e->getMessage());
            }
        }
        
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
            
            // Notify admin
            try {
                app(\App\Services\FirebaseNotificationService::class)->sendNotification(
                    'admin',
                    'Manufacturing Complete',
                    "Manufacturing for Order #{$order->order_number} is fully complete.",
                    ['icon' => 'success', 'order_id' => $order->id]
                );
            } catch (\Exception $e) {
                \Log::error("Admin Completion Notification Error: " . $e->getMessage());
            }
            
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
            
            // Notify admin if completed
            if (isset($updateData['manufacturing_status']) && $updateData['manufacturing_status'] === 'completed') {
                try {
                    app(\App\Services\FirebaseNotificationService::class)->sendNotification(
                        'admin',
                        'Manufacturing Complete',
                        "Manufacturing for Order #{$order->order_number} is fully complete.",
                        ['icon' => 'success', 'order_id' => $order->id]
                    );
                } catch (\Exception $e) {
                    \Log::error("Admin Completion Notification Error: " . $e->getMessage());
                }
            }
        }
        
        return redirect()->back()->with('success', 'Orders status updated successfully.');
    }

    /**
     * Manufacturing team submits a note requesting admin permission to edit wrongly-entered pieces.
     */
    public function requestEditPermission(Request $request, Order $order)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();

        if ($order->manufacturing_team_id != $manufacturingTeam->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if ($order->mfg_edit_permission_count >= 2) {
            return redirect()->back()->with('error', 'You have already used both edit permissions for this order. No more edit requests allowed.');
        }

        if ($order->mfg_edit_permission_granted) {
            return redirect()->back()->with('info', 'You already have an active edit permission. Please use it to correct the pieces.');
        }

        $request->validate([
            'edit_request_note' => 'required|string|max:500',
        ]);

        $order->update([
            'mfg_edit_request_note' => $request->edit_request_note,
            'mfg_edit_request_at'   => now(),
        ]);

        return redirect()->back()->with('success', 'Edit request sent to admin. Please wait for their approval before correcting your entries.');
    }

    /**
     * Manufacturing team submits corrected piece values when admin permission is active.
     * This REPLACES (not adds to) the existing manufactured/rejected pieces totals.
     */
    public function submitCorrectedPieces(Request $request, Order $order)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();

        if ($order->manufacturing_team_id != $manufacturingTeam->id) {
            abort(403, 'Unauthorized access to this order.');
        }

        if (!$order->mfg_edit_permission_granted) {
            return redirect()->back()->with('error', 'You do not have active edit permission for this order.');
        }

        $request->validate([
            'corrected_manufactured_pieces' => 'required|array',
            'corrected_manufactured_pieces.*' => 'integer|min:0',
            'corrected_rejected_pieces'     => 'nullable|array',
            'corrected_rejected_pieces.*'   => 'integer|min:0',
            'correction_reason'             => 'required|string|max:500',
        ]);

        foreach ($request->corrected_manufactured_pieces as $itemId => $manufacturedPieces) {
            $orderItem = OrderItem::where('id', $itemId)
                ->where('order_id', $order->id)
                ->first();

            if (!$orderItem || $orderItem->per_unit_pieces <= 0) {
                continue;
            }

            $rejectedPieces = isset($request->corrected_rejected_pieces[$itemId])
                ? (int) $request->corrected_rejected_pieces[$itemId]
                : 0;

            $totalPieces = $orderItem->total_pieces;

            // Validate total doesn't exceed what was ordered
            if (($manufacturedPieces + $rejectedPieces) > $totalPieces) {
                return redirect()->back()->with('error', "Total pieces for '{$orderItem->product_name}' cannot exceed ordered quantity ({$totalPieces} pcs).");
            }

            $newManufacturedUnits = $manufacturedPieces / $orderItem->per_unit_pieces;
            $newRejectedUnits     = $rejectedPieces / $orderItem->per_unit_pieces;

            $orderItem->update([
                'manufactured_pieces'   => $manufacturedPieces,
                'rejected_pieces'       => $rejectedPieces,
                'manufactured_quantity' => $newManufacturedUnits,
                'rejected_quantity'     => $newRejectedUnits,
            ]);
        }

        // Consume the permission and clear the note
        $order->update([
            'mfg_edit_permission_granted' => false,
            'mfg_edit_request_note'       => null,
            'mfg_edit_request_at'         => null,
        ]);

        return redirect()->back()->with('success', 'Pieces corrected successfully. Your correction has been saved and the edit permission has been used.');
    }

    /**
     * Update the status of a return request assigned to this manufacturing team.
     */
    public function updateReturnStatus(Request $request, \App\Models\ReturnRequest $returnRequest)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();

        // Verify the return request's order is assigned to this team
        if ($returnRequest->order->manufacturing_team_id != $manufacturingTeam->id) {
            abort(403, 'Unauthorized access to this return request.');
        }

        $request->validate([
            'status' => 'required|in:processing,completed',
        ]);

        $updateData = ['status' => $request->status];
        if ($request->status === 'completed' && is_null($returnRequest->resolved_at)) {
            $updateData['resolved_at'] = now();
        }

        $returnRequest->update($updateData);

        return redirect()->back()->with('success', 'Return request status updated to ' . ucfirst($request->status) . '.');
    }
}