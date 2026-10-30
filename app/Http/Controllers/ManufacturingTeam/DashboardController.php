<?php

namespace App\Http\Controllers\ManufacturingTeam;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderProductionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the manufacturing team dashboard.
     */
    public function index(Request $request)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();
        
        $tab = $request->query('tab', 'allocated');
        
        $statusCounts = Order::where('manufacturing_team_id', $manufacturingTeam->id)
            ->select(
                DB::raw("COUNT(CASE WHEN manufacturing_status = 'allocated' THEN 1 END) as allocated_count"),
                DB::raw("COUNT(CASE WHEN manufacturing_status = 'processing' THEN 1 END) as processing_count"),
                DB::raw("COUNT(CASE WHEN manufacturing_status = 'completed' THEN 1 END) as completed_count")
            )
            ->first();
            
        $allocatedCount = $statusCounts->allocated_count ?? 0;
        $acceptedCount = $statusCounts->processing_count ?? 0;
        $completedCount = $statusCounts->completed_count ?? 0;
        
        $baseQuery = Order::where('manufacturing_team_id', $manufacturingTeam->id);
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

        $returnRequestsCount = \App\Models\ReturnRequest::where(function($q) use ($manufacturingTeam) {
            $q->where('manufacturing_team_id', $manufacturingTeam->id)
              ->orWhereHas('order', function($q2) use ($manufacturingTeam) {
                  $q2->where('manufacturing_team_id', $manufacturingTeam->id);
              });
        })->count();
        
        $returnRequests = null;
        if ($tab === 'returns') {
            $perPage = request()->get('per_page', 20);
            if (!in_array($perPage, [10, 20, 30, 100])) {
                $perPage = 20;
            }
            $returnRequests = \App\Models\ReturnRequest::with(['order', 'orderItem.product', 'product'])
                ->where(function($q) use ($manufacturingTeam) {
                    $q->where('manufacturing_team_id', $manufacturingTeam->id)
                      ->orWhereHas('order', function($q2) use ($manufacturingTeam) {
                          $q2->where('manufacturing_team_id', $manufacturingTeam->id);
                      });
                })
                ->latest()
                ->paginate($perPage);
            $returnRequests->appends(['tab' => $tab, 'per_page' => $perPage]);
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
        
        $order->load(['customer', 'productionLogs.orderItem', 'productionLogs.manufacturingTeam']);
        // Only accepted items are allocated to go to the manufacturing team
        $order->setRelation('items', $order->items()->where('status', 'accepted')->with('product')->get());
        
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
        
        // Validation: Prevent marking as completed if items are still pending (only accepted items allocated)
        if ($request->manufacturing_status === 'completed') {
            $pendingQuantity = $order->items()->where('status', 'accepted')->get()->sum(function($item) {
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
                $updateData['mfg_accepted_at'] = $order->mfg_accepted_at ?? now();
                $updateData['allocated_at'] = $order->allocated_at ?? now();
                $updateData['status'] = 'under_process';
                if ($request->has('tentative_dispatch_date')) {
                    $updateData['tentative_dispatch_date'] = $request->tentative_dispatch_date;
                }
                break;
            case 'completed':
                $updateData['mfg_completed_at'] = now();
                $updateData['completed_at'] = $order->completed_at ?? now();
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
            'rejection_reasons.*' => 'nullable|string|max:500',
        ]);
        
        // Strict pre-validation: check that totals do not exceed pending pieces
        // and ensure rejection reasons are provided when pieces are rejected.
        $hasAnyInput = false;
        foreach ($request->completed_pieces as $itemId => $completedPieces) {
            $completedPieces = (int) $completedPieces;
            $rejectedPieces = isset($request->rejected_pieces[$itemId]) ? (int) $request->rejected_pieces[$itemId] : 0;
            $rejectionReason = isset($request->rejection_reasons[$itemId]) ? trim($request->rejection_reasons[$itemId]) : '';

            if ($completedPieces > 0 || $rejectedPieces > 0) {
                $hasAnyInput = true;
            }

            $orderItem = OrderItem::where('id', $itemId)
                ->where('order_id', $order->id)
                ->where('status', 'accepted')
                ->first();

            if (!$orderItem) {
                continue;
            }

            $canProcessPieces = $orderItem->manufacturing_pending_pieces;
            $totalToProcessPieces = $completedPieces + $rejectedPieces;

            // Validate that completed + rejected does not exceed pending pieces
            if ($totalToProcessPieces > $canProcessPieces) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "For '{$orderItem->product_name}', Total processed pieces (Completed: {$completedPieces} + Rejected: {$rejectedPieces} = {$totalToProcessPieces} pcs) cannot exceed pending pieces ({$canProcessPieces} pcs). Please check the completed and rejected counts.");
            }

            // Require rejection reason when rejected pieces are entered
            if ($rejectedPieces > 0 && empty($rejectionReason)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Please provide a rejection message/reason for '{$orderItem->product_name}' because {$rejectedPieces} pcs are marked as rejected.");
            }
        }

        if (!$hasAnyInput) {
            return redirect()->back()->with('error', 'Please enter at least 1 completed or rejected piece to submit progress.');
        }

        return DB::transaction(function () use ($request, $order, $manufacturingTeam) {
            $totalCompletedPieces = 0;
            $totalRejectedPieces = 0;
            
            // Update manufactured and rejected quantities for each item
            foreach ($request->completed_pieces as $itemId => $completedPieces) {
                $orderItem = OrderItem::where('id', $itemId)
                    ->where('order_id', $order->id)
                    ->where('status', 'accepted')
                    ->lockForUpdate()
                    ->first();
                    
                if ($orderItem && $orderItem->per_unit_pieces > 0) {
                    $actualCompletedPieces = (int) $completedPieces;
                    $actualRejectedPieces = isset($request->rejected_pieces[$itemId]) ? (int) $request->rejected_pieces[$itemId] : 0;
                    $rejectionReason = isset($request->rejection_reasons[$itemId]) ? trim($request->rejection_reasons[$itemId]) : null;
                    
                    if ($actualCompletedPieces > 0 || $actualRejectedPieces > 0) {
                        $newManufacturedPieces = $orderItem->manufactured_pieces + $actualCompletedPieces;
                        $newRejectedPieces = $orderItem->rejected_pieces + $actualRejectedPieces;
                        
                        // Convert pieces back to units for database consistency
                        $newManufacturedUnits = $newManufacturedPieces / $orderItem->per_unit_pieces;
                        $newRejectedUnits = $newRejectedPieces / $orderItem->per_unit_pieces;

                        // Combine rejection reasons if previous exists
                        $combinedRejectionReason = $orderItem->rejection_reason;
                        if ($actualRejectedPieces > 0 && !empty($rejectionReason)) {
                            $combinedRejectionReason = $orderItem->rejection_reason
                                ? $orderItem->rejection_reason . ' | ' . $rejectionReason
                                : $rejectionReason;
                        }

                        $orderItem->update([
                            'manufactured_quantity' => $newManufacturedUnits,
                            'rejected_quantity' => $newRejectedUnits,
                            'manufactured_pieces' => $newManufacturedPieces,
                            'rejected_pieces' => $newRejectedPieces,
                            'rejection_reason' => $combinedRejectionReason,
                        ]);
                        
                        // Record production log for chronology timeline
                        OrderProductionLog::create([
                            'order_id' => $order->id,
                            'order_item_id' => $orderItem->id,
                            'manufacturing_team_id' => $manufacturingTeam ? $manufacturingTeam->id : $order->manufacturing_team_id,
                            'pieces_completed' => $actualCompletedPieces,
                            'pieces_rejected' => $actualRejectedPieces,
                            'quantity_completed' => $orderItem->per_unit_pieces > 0 ? ($actualCompletedPieces / $orderItem->per_unit_pieces) : $actualCompletedPieces,
                            'quantity_rejected' => $orderItem->per_unit_pieces > 0 ? ($actualRejectedPieces / $orderItem->per_unit_pieces) : $actualRejectedPieces,
                            'rejection_reason' => $actualRejectedPieces > 0 ? $rejectionReason : null,
                        ]);

                        $totalCompletedPieces += $actualCompletedPieces;
                        $totalRejectedPieces += $actualRejectedPieces;
                    }
                }
            }
            
            // Check if everything is manufactured/rejected (using pieces since we are tracking pieces now)
            // Re-fetch items to get the updated values
            $order->refresh();
            $stillPendingPieces = $order->items()->where('status', 'accepted')->get()->sum(function($item) {
                return $item->manufacturing_pending_pieces;
            });

            if ($stillPendingPieces <= 0) {
                $order->update([
                    'manufacturing_status' => 'completed',
                    'mfg_completed_at' => now(),
                    'completed_at' => $order->completed_at ?? now(),
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
        });
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
                'mfg_accepted_at' => $order->mfg_accepted_at ?? now(),
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
                    $updateData['mfg_accepted_at'] = $order->mfg_accepted_at ?? now();
                    $updateData['allocated_at'] = $order->allocated_at ?? now();
                    $updateData['status'] = 'under_process';
                    break;
                case 'completed':
                    // Check pending quantity for this order (only accepted items allocated)
                    $pendingQuantity = $order->items()->where('status', 'accepted')->get()->sum(function($item) {
                        return $item->manufacturing_pending_quantity;
                    });
                    
                    if ($pendingQuantity > 0) {
                        continue 2; // Skip this order update
                    }
                    $updateData['mfg_completed_at'] = now();
                    $updateData['completed_at'] = $order->completed_at ?? now();
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
            'corrected_rejection_reasons'   => 'nullable|array',
            'corrected_rejection_reasons.*' => 'nullable|string|max:500',
            'correction_reason'             => 'required|string|max:500',
        ]);

        foreach ($request->corrected_manufactured_pieces as $itemId => $manufacturedPieces) {
            $orderItem = OrderItem::where('id', $itemId)
                ->where('order_id', $order->id)
                ->where('status', 'accepted')
                ->first();

            if (!$orderItem || $orderItem->per_unit_pieces <= 0) {
                continue;
            }

            $manufacturedPieces = (int) $manufacturedPieces;
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

            $itemRejectionReason = isset($request->corrected_rejection_reasons[$itemId]) ? trim($request->corrected_rejection_reasons[$itemId]) : null;

            $updateData = [
                'manufactured_pieces'   => $manufacturedPieces,
                'rejected_pieces'       => $rejectedPieces,
                'manufactured_quantity' => $newManufacturedUnits,
                'rejected_quantity'     => $newRejectedUnits,
            ];

            if ($rejectedPieces > 0) {
                $updateData['rejection_reason'] = !empty($itemRejectionReason) 
                    ? $itemRejectionReason 
                    : ($orderItem->rejection_reason ?: $request->correction_reason);
            }

            $orderItem->update($updateData);
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

    /**
     * Display the dedicated return requests page.
     */
    public function returnsIndex(Request $request)
    {
        $manufacturingTeam = Auth::guard('manufacturing-team')->user();

        $perPage = $request->get('per_page', 20);
        if (!in_array($perPage, [10, 20, 30, 100])) {
            $perPage = 20;
        }

        $returnRequests = \App\Models\ReturnRequest::with(['order', 'orderItem.product', 'product'])
            ->where(function($q) use ($manufacturingTeam) {
                $q->where('manufacturing_team_id', $manufacturingTeam->id)
                  ->orWhereHas('order', function($q2) use ($manufacturingTeam) {
                      $q2->where('manufacturing_team_id', $manufacturingTeam->id);
                  });
            })
            ->latest()
            ->paginate($perPage);

        $returnRequests->appends(['per_page' => $perPage]);

        $returnRequestsCount = $returnRequests->total();

        return view('manufacturing-team.return-requests.index', compact('manufacturingTeam', 'returnRequests', 'returnRequestsCount', 'perPage'));
    }

    /**
     * Show the form for creating a new return request.
     */
    public function createReturnRequest()
    {
        $manufacturingTeam = \Illuminate\Support\Facades\Auth::guard('manufacturing-team')->user();
        $customers = \App\Models\Customer::orderBy('name')->get(['id', 'name', 'phone', 'customer_type']);
        $products = \App\Models\Product::orderBy('name')->get(['id', 'name']);
        return view('manufacturing-team.return-requests.create', compact('customers', 'products', 'manufacturingTeam'));
    }

    /**
     * Store a newly created return request in storage.
     */
    public function storeReturnRequest(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.selected' => 'nullable',
            'items.*.product_id' => 'required_with:items.*.selected|exists:products,id',
            'reason' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $createdCount = 0;

        foreach ($request->items as $item) {
            if (!isset($item['selected']) || $item['selected'] != 1) {
                continue;
            }
            
            $quantity = isset($item['quantity']) && $item['quantity'] !== '' ? (int)$item['quantity'] : 0;
            $pieces = isset($item['pieces']) && $item['pieces'] !== '' ? (int)$item['pieces'] : 0;

            if ($quantity <= 0 && $pieces <= 0) {
                continue;
            }

            $requestNumber = 'REQ-' . strtoupper(\Illuminate\Support\Str::random(8));

            \App\Models\ReturnRequest::create([
                'request_number' => $requestNumber,
                'manufacturing_team_id' => \Illuminate\Support\Facades\Auth::guard('manufacturing-team')->id(),
                'product_id' => $item['product_id'],
                'customer_id' => $request->customer_id,
                'reason' => $request->reason,
                'description' => $request->description ?? '',
                'type' => 'return',
                'quantity' => $quantity,
                'pieces' => $pieces,
                'status' => 'pending'
            ]);
            
            $createdCount++;
        }

        if ($createdCount === 0) {
            return redirect()->back()->with('error', 'Please provide valid return quantities or pieces for the selected products.');
        }

        return redirect()->route('manufacturing-team.returns.index')->with('success', 'Return request created successfully.');
    }
}