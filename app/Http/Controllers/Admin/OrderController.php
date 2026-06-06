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

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::with('customer', 'manufacturingTeam')->latest()->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load('items.product', 'customer', 'manufacturingTeam', 'returnRequests.orderItem', 'returnRequests.invoice');
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
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,accepted,rejected',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Order status updated successfully.');
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

        $totalDispatched = 0;
        $actionsPerformed = false; 
        $invoiceItems = []; // Track items for invoice creation
        
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
        
        foreach ($quantities as $itemId => $dispatchedQuantity) {
            if ($dispatchedQuantity === null || $dispatchedQuantity === '') {
                continue; 
            }
            
            $orderItem = OrderItem::where('id', $itemId)
                ->where('order_id', $order->id)
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
        
        // Calculate items to be dispatched (everything pending)
        $invoiceItems = [];
        foreach ($order->items as $item) {
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

        return redirect()->back()->with('success', 'Order marked as dispatched and final Invoice generated successfully.');
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
            $unitPrice = $orderItem->price; // This is unit price (assuming price in DB is per unit)
            // Note: In OrderItem, usually 'price' is unit price, 'total' is line total.
            // Let's assume price is unit price.
            
            // Calculate tax per unit (pro-rated from order tax?? No, tax should be on item)
            // If tax is order level, we need to approximate. 
            // Let's look at OrderItem model. It has 'tax' field? 
            // The migration for order_items has 'tax'. 
            $itemTax = $orderItem->tax / $orderItem->quantity * $quantity; // Pro-rate existing tax?
            // Actually OrderItem has 'tax' which is total tax for the line. 
            // So unit tax = $orderItem->tax / $orderItem->quantity.
            
            $unitTax = 0;
            if ($orderItem->quantity > 0) {
                $unitTax = $orderItem->tax / $orderItem->quantity;
            }
            
            $itemDiscount = 0;
             if ($orderItem->quantity > 0) {
                $itemDiscount = ($orderItem->discount_amount ?? 0) / $orderItem->quantity * $quantity;
            }
            
            $lineTotal = ($unitPrice * $quantity) + ($unitTax * $quantity) - $itemDiscount;
            
            $subtotal += ($unitPrice * $quantity);
            $taxTotal += ($unitTax * $quantity);
            $discountTotal += $itemDiscount;
        }
        
        $btDiscountAmount = 0;
        if ($order->is_fully_dispatched && $order->bank_transfer_discount_amount > 0) {
            $btDiscountAmount = $order->bank_transfer_discount_amount;
        }

        $total = $subtotal + $taxTotal + $manualShipping + $manualOtherCharges - $discountTotal - $btDiscountAmount;
        
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
            'other_charges' => $manualOtherCharges,
            'total' => $total,
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
            
            $unitPrice = $orderItem->price;
            $unitTax = ($orderItem->quantity > 0) ? ($orderItem->tax / $orderItem->quantity) : 0;
            $unitDiscount = ($orderItem->quantity > 0) ? (($orderItem->discount_amount ?? 0) / $orderItem->quantity) : 0;
            
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
}