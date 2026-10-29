<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ManufacturingTeam;
use App\Models\DispatchImage;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Order::with('customer', 'manufacturingTeam');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['pending', 'processing', 'completed', 'cancelled', 'accepted', 'rejected'])) {
                $query->where('status', $status);
            } elseif (in_array($status, ['allocated'])) {
                $query->where('manufacturing_status', $status);
            }
        }

        if ($request->filled('category_id') || $request->filled('subcategory_id') || $request->filled('sub_subcategory_id')) {
            $query->whereHas('items.product', function($q) use ($request) {
                if ($request->filled('category_id')) {
                    $q->where('category_id', $request->category_id);
                }
                if ($request->filled('subcategory_id')) {
                    $q->where('subcategory_id', $request->subcategory_id);
                }
                if ($request->filled('sub_subcategory_id')) {
                    $q->where('sub_subcategory_id', $request->sub_subcategory_id);
                }
            });
        }

        $perPage = $request->input('per_page', 20);
        $orders = $query->latest()->paginate($perPage)->appends($request->all());

        $categories = \App\Models\Category::all();
        $subcategories = [];
        $subSubcategories = [];
        if ($request->filled('category_id')) {
            $subcategories = \App\Models\Subcategory::where('category_id', $request->category_id)->get();
        }
        if ($request->filled('subcategory_id')) {
            $subSubcategories = \App\Models\SubSubcategory::where('subcategory_id', $request->subcategory_id)->get();
        }

        $manufacturingTeams = ManufacturingTeam::where('is_active', true)->get();

        return view('admin.orders.index', compact('orders', 'categories', 'subcategories', 'subSubcategories', 'manufacturingTeams'));
    }

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load('items.product', 'customer', 'manufacturingTeam', 'returnRequests.orderItem', 'returnRequests.invoice', 'productionLogs.orderItem', 'invoices.items.orderItem');
        $manufacturingTeams = ManufacturingTeam::where('is_active', true)->get();
        
        return view('admin.orders.show', compact('order', 'manufacturingTeams'));
    }
    
    /**
     * Display the invoice for a specific order.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\View\View
     */
    public function showInvoice(Order $order, Invoice $invoice)
    {
        $invoice->load(['items.orderItem.product', 'order.customer']);
        
        return view('admin.orders.invoice', compact('order', 'invoice'));
    }

    /**
     * Update the status of the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateOrderStatusRequest $request, Order $order)
    {
        $status = $request->validated('status');

        $updateData = [
            'status' => $status,
        ];

        // Track timestamps dynamically based on status
        if ($status === 'accepted') {
            $updateData['accepted_at'] = now();
            // Clear rejected_at if previously rejected and now accepted
            $updateData['rejected_at'] = null;
            // Also accept all pending items
            $order->items()->where('status', 'pending')->update([
                'status' => 'accepted',
            ]);
        } elseif ($status === 'rejected') {
            $updateData['rejected_at'] = now();
            // Clear accepted_at if previously accepted and now rejected
            $updateData['accepted_at'] = null;
            if ($request->filled('rejection_reason')) {
                $updateData['rejection_reason'] = $request->rejection_reason;
            }
            // Also reject all non-rejected items
            foreach ($order->items()->where('status', '!=', 'rejected')->get() as $item) {
                $item->update([
                    'status' => 'rejected',
                    'rejected_quantity' => $item->quantity,
                    'rejected_pieces' => $item->total_pieces,
                    'rejection_reason' => $request->rejection_reason ?? 'Order rejected by Admin',
                ]);
            }
        } elseif ($status === 'completed') {
            $updateData['completed_at'] = $order->completed_at ?? now();
        }

        $order->update($updateData);

        $statusLabel = ucfirst($status);
        return redirect()->back()->with('success', "Order #{$order->order_number} has been {$statusLabel} successfully.");
    }
    
    /**
     * Allocate order(s) to a manufacturing team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function allocateToManufacturingTeam(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'manufacturing_team_id' => 'required|exists:manufacturing_teams,id',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        
        foreach ($orders as $order) {
            // Guard: Order must be accepted and have at least one accepted item
            if (in_array($order->status, ['pending', 'rejected', 'cancelled']) || !$order->has_accepted_items) {
                return redirect()->back()->with('error', "Order #{$order->order_number} cannot be allocated until items are accepted by admin.");
            }
            $order->update([
                'manufacturing_team_id' => $request->manufacturing_team_id,
                'manufacturing_status' => 'allocated',
                'allocated_at' => now(),
            ]);

            try {
                // Send push notification to the specific manufacturing team
                $firebaseService = app(\App\Services\FirebaseNotificationService::class);
                $topic = 'manufacturing_' . $request->manufacturing_team_id;
                
                $firebaseService->sendNotification(
                    $topic,
                    'New Order Allocated',
                    "Order #{$order->order_number} has been allocated to your team.",
                    ['icon' => 'info', 'order_id' => $order->id]
                );
                
                // Also send to general manufacturing topic for fallback
                $firebaseService->sendNotification(
                    'manufacturing',
                    'New Order Allocated',
                    "Order #{$order->order_number} has been allocated.",
                    ['icon' => 'info', 'order_id' => $order->id, 'team_id' => $request->manufacturing_team_id]
                );
            } catch (\Exception $e) {
                \Log::error("Failed to send allocation push notification: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Orders allocated to manufacturing team successfully.');
    }
    
    /**
     * Update manufacturing status of an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateManufacturingStatus(Request $request, Order $order)
    {
        $request->validate([
            'manufacturing_status' => 'required|in:pending,allocated,processing,completed,rejected',
        ]);

        $updateData = [
            'manufacturing_status' => $request->manufacturing_status,
        ];

        // Set timestamps based on status
        switch ($request->manufacturing_status) {
            case 'processing':
                $updateData['allocated_at'] = $updateData['allocated_at'] ?? now();
                break;
            case 'completed':
                $updateData['completed_at'] = now();
                break;
            case 'rejected':
                $updateData['manufacturing_team_id'] = null;
                break;
        }

        $order->update($updateData);

        return redirect()->back()->with('success', 'Manufacturing status updated successfully.');
    }
    
    /**
     * Partially dispatch items in an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Partially dispatch items in an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function partialDispatch(Request $request, Order $order)
    {
        $request->validate([
            'dispatched_quantities' => 'nullable|array',
            'dispatched_quantities.*' => 'nullable|integer|min:0',
            'dispatched_pieces' => 'nullable|array',
            'dispatched_pieces.*' => 'nullable|integer|min:0',
            'order_dispatch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order_dispatch_description' => 'nullable|string|max:500',
        ]);

        $quantities = $request->dispatched_quantities ?? [];
        if ($request->has('dispatched_pieces')) {
            foreach ($request->dispatched_pieces as $itemId => $pieces) {
                if ($pieces !== null && $pieces !== '') {
                    $orderItem = OrderItem::find($itemId);
                    if ($orderItem && $orderItem->per_unit_pieces > 0) {
                        $quantities[$itemId] = $pieces / $orderItem->per_unit_pieces;
                    }
                }
            }
        }

        return DB::transaction(function () use ($request, $order, $quantities) {
            $totalDispatched = 0;
            $actionsPerformed = false; 
            $invoiceItems = []; // Track items for invoice creation
            
            foreach ($quantities as $itemId => $dispatchedQuantity) {
                if ($dispatchedQuantity === null || $dispatchedQuantity === '') {
                    continue; 
                }
                
                $orderItem = OrderItem::where('id', $itemId)
                    ->where('order_id', $order->id)
                    ->lockForUpdate()
                    ->first();
                    
                if ($orderItem) {
                    // Calculate how much we can actually dispatch
                    $maxCanDispatch = $orderItem->dispatch_pending_quantity;
                    $actualDispatched = min((float)$dispatchedQuantity, $maxCanDispatch);
                    
                    if ($actualDispatched > 0) {
                        $orderItem->update([
                            'dispatched_quantity' => $orderItem->dispatched_quantity + $actualDispatched,
                        ]);
                        $totalDispatched += $actualDispatched;
                        $orderItem->refresh();
                        $actionsPerformed = true;
                        
                        // Add to invoice items list
                        $invoiceItems[] = [
                            'order_item' => $orderItem,
                            'quantity' => $actualDispatched
                        ];
                    }
                }
            }

            // Handle order-level image
            if ($request->hasFile('order_dispatch_image')) {
                $imagePath = $request->file('order_dispatch_image')->store('dispatch-images', 'public');
                DispatchImage::create([
                    'order_id' => $order->id,
                    'image_path' => $imagePath,
                    'description' => $request->order_dispatch_description ?? null,
                    'uploaded_by' => 'admin',
                ]);
                $actionsPerformed = true;
            }

            // Refresh everything
            $order->refresh();
            
            if (!$actionsPerformed && $totalDispatched <= 0) {
                return redirect()->back()->with('info', 'No changes were made.');
            }

            // Create Invoice if items were dispatched
            if (!empty($invoiceItems)) {
                $this->createInvoice($order, $invoiceItems, $request->shipping_fee ?? 0, $request->other_charges ?? 0);
            }

            // Logic for completion
            if ($order->total_pending_quantity <= 0) {
                $order->update([
                    'dispatched_at' => now(),
                    'status' => 'completed',
                    'manufacturing_status' => 'completed',
                    'completed_at' => $order->completed_at ?? now(),
                ]);
                return redirect()->back()->with('success', 'All items dispatched! Order completed and Invoice generated.');
            }

            return redirect()->back()->with('success', "Processed successfully. $totalDispatched new items dispatched and Invoice generated.");
        });
    }
    
    /**
     * Mark order as dispatched.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    /**
     * Mark order as dispatched.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsDispatched(Request $request, Order $order)
    {
        $request->validate([
            'dispatch_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dispatch_description' => 'nullable|string|max:500',
        ]);
        
        return DB::transaction(function () use ($request, $order) {
            // Calculate items to be dispatched (everything pending)
            $invoiceItems = [];
            foreach ($order->items as $item) {
                // Refresh item and lock it for update to prevent race conditions
                $item = OrderItem::where('id', $item->id)->lockForUpdate()->first();
                $pending = $item->dispatch_pending_quantity;
                if ($pending > 0) {
                    $invoiceItems[] = [
                        'order_item' => $item,
                        'quantity' => $pending
                    ];
                    
                    // Update item dispatch status
                    $item->update([
                        'dispatched_quantity' => $item->quantity
                    ]);
                }
            }
            
            $order->update([
                'dispatched_at' => now(),
                'status' => 'completed',
            ]);
            
            // Create Invoice for the remaining items
            if (!empty($invoiceItems)) {
                $order->load('items');
                $this->createInvoice($order, $invoiceItems, $request->shipping_fee ?? 0, $request->other_charges ?? 0);
            }
            
            // Handle image upload
            if ($request->hasFile('dispatch_image')) {
                $image = $request->file('dispatch_image');
                $imagePath = $image->store('dispatch-images', 'public');
                
                DispatchImage::create([
                    'order_id' => $order->id,
                    'order_item_id' => null,
                    'image_path' => $imagePath,
                    'description' => $request->dispatch_description ?? null,
                    'uploaded_by' => 'admin',
                ]);
            }
            
            return redirect()->back()->with('success', 'Order fully dispatched successfully and Invoice generated.');
        });
    }

    /**
     * Create a new invoice for the order.
     *
     * @param Order $order
     * @param array $items Array of ['order_item' => OrderItem, 'quantity' => int]
     * @param float $manualShipping
     * @param float $manualOtherCharges
     * @return Invoice
     */
    private function createInvoice(Order $order, array $items, $manualShipping = 0, $manualOtherCharges = 0)
    {
        // 1. Calculate totals
        $subtotal = 0;
        $taxTotal = 0;
        $discountTotal = 0;
        
        foreach ($items as $itemData) {
            $orderItem = $itemData['order_item'];
            $quantity = $itemData['quantity'];
            
            // Calculate item amounts
            $unitPrice = $orderItem->original_price ?? $orderItem->price; // Use original price for subtotal calculation
            
            $unitTax = 0;
            if ($orderItem->quantity > 0) {
                $unitTax = $orderItem->tax / $orderItem->quantity;
            }
            
            $itemDiscount = 0;
             if ($orderItem->quantity > 0) {
                $itemDiscount = ($orderItem->discount_amount ?? 0) * $quantity;
            }
            
            $lineTotal = ($unitPrice * $quantity) + ($unitTax * $quantity) - $itemDiscount;
            
            $subtotal += ($unitPrice * $quantity);
            $taxTotal += ($unitTax * $quantity);
            $discountTotal += $itemDiscount;
        }
        
        $btDiscountAmount = 0;
        $onlineDiscountAmount = 0;
        $draftDiscountAmount = 0;
        if ($order->is_fully_dispatched) {
            $onlineDiscountAmount = $order->online_discount_amount > 0 ? $order->online_discount_amount : ($order->bank_transfer_discount_amount ?? 0);
            $btDiscountAmount = $onlineDiscountAmount;
            $draftDiscountAmount = $order->draft_discount_amount ?? 0;
        }

        $total = $subtotal + $taxTotal + $manualShipping + $manualOtherCharges - $discountTotal - $onlineDiscountAmount - $draftDiscountAmount;
        
        // 2. Create Invoice
        $invoiceCount = $order->invoices()->count() + 1;
        $invoiceNumber = $order->order_number . '-INV-' . str_pad($invoiceCount, 2, '0', STR_PAD_LEFT);
        
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $invoiceNumber,
            'subtotal' => $subtotal,
            'tax' => $taxTotal,
            'shipping' => $manualShipping,
            'discount_amount' => $discountTotal,
            'bank_transfer_discount_amount' => $btDiscountAmount,
            'online_discount_amount' => $onlineDiscountAmount,
            'draft_discount_amount' => $draftDiscountAmount,
            'other_charges' => $manualOtherCharges,
            'total' => $total,
            'dispatched_at' => now(),
        ]);
        
        // Update order totals to include these manual fees if needed? 
        // Actually, let's update order record as well so it stays consistent.
        if ($manualShipping > 0 || $manualOtherCharges > 0) {
            $order->update([
                'shipping' => $order->shipping + $manualShipping,
                'other_charges' => $order->other_charges + $manualOtherCharges,
                'total' => $order->total + $manualShipping + $manualOtherCharges
            ]);
        }
        
        // 3. Create Invoice Items
        foreach ($items as $itemData) {
            $orderItem = $itemData['order_item'];
            $quantity = $itemData['quantity'];
            
            $unitPrice = $orderItem->original_price ?? $orderItem->price;
            $unitTax = ($orderItem->quantity > 0) ? ($orderItem->tax / $orderItem->quantity) : 0;
            $unitDiscount = $orderItem->discount_amount ?? 0;
            
            $lineTotal = ($unitPrice * $quantity) + ($unitTax * $quantity) - ($unitDiscount * $quantity);
            
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'order_item_id' => $orderItem->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_amount' => $unitTax * $quantity,
                'discount_amount' => $unitDiscount * $quantity,
                'total' => $lineTotal,
            ]);
        }
        
        return $invoice;
    }

    /**
     * Grant manufacturing team permission to correct pieces for an order.
     * Admin can grant this a maximum of 2 times per order.
     */
    public function grantEditPermission(Order $order)
    {
        if ($order->mfg_edit_permission_count >= 2) {
            return redirect()->back()->with('error', 'Edit permission has already been granted 2 times for this order. No more grants allowed.');
        }

        if ($order->mfg_edit_permission_granted) {
            return redirect()->back()->with('info', 'Edit permission is already active. Waiting for manufacturing team to submit their correction.');
        }

        $order->update([
            'mfg_edit_permission_granted' => true,
            'mfg_edit_permission_count'   => $order->mfg_edit_permission_count + 1,
        ]);

        return redirect()->back()->with('success', 'Edit permission granted to manufacturing team. (' . $order->mfg_edit_permission_count . '/2 used)');
    }

    /**
     * Update individual order item status (accept / reject).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @param  \App\Models\OrderItem  $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateItemStatus(Request $request, Order $order, OrderItem $item)
    {
        $request->validate([
            'status' => 'required|string|in:accepted,rejected',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if ($item->order_id !== $order->id) {
            abort(404, 'Item does not belong to this order.');
        }

        $newStatus = $request->input('status');

        if ($newStatus === 'accepted') {
            $item->update([
                'status' => 'accepted',
                'rejected_quantity' => 0,
                'rejected_pieces' => 0,
                'rejection_reason' => null,
            ]);
        } elseif ($newStatus === 'rejected') {
            $item->update([
                'status' => 'rejected',
                'rejected_quantity' => $item->quantity,
                'rejected_pieces' => $item->total_pieces,
                'rejection_reason' => $request->input('rejection_reason') ?? 'Rejected by Admin',
            ]);
        }

        // Sync order status based on all items
        $order->syncStatusWithItems();

        return redirect()->back()->with('success', "Item '{$item->product_name}' marked as {$newStatus} successfully.");
    }

    /**
     * Bulk update order items status (accept / reject).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkUpdateItemStatus(Request $request, Order $order)
    {
        $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'exists:order_items,id',
            'action' => 'required|string|in:accept,reject',
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $action = $request->input('action');
        $itemIds = $request->input('item_ids');

        $items = $order->items()->whereIn('id', $itemIds)->get();

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'No valid items selected for update.');
        }

        foreach ($items as $item) {
            if ($action === 'accept') {
                $item->update([
                    'status' => 'accepted',
                    'rejected_quantity' => 0,
                    'rejected_pieces' => 0,
                    'rejection_reason' => null,
                ]);
            } elseif ($action === 'reject') {
                $item->update([
                    'status' => 'rejected',
                    'rejected_quantity' => $item->quantity,
                    'rejected_pieces' => $item->total_pieces,
                    'rejection_reason' => $request->input('rejection_reason') ?? 'Rejected by Admin',
                ]);
            }
        }

        // Sync order status based on all items
        $order->syncStatusWithItems();

        $actionLabel = $action === 'accept' ? 'accepted' : 'rejected';
        return redirect()->back()->with('success', count($items) . " item(s) have been {$actionLabel} successfully.");
    }
}