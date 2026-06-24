<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use App\Models\ReturnNote;
use App\Models\ReturnNoteItem;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    /**
     * Display a listing of return requests.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $returnRequests = ReturnRequest::with(['order', 'orderItem.product', 'customer'])
            ->latest()
            ->paginate(20);
            
        return view('admin.return-requests.index', compact('returnRequests'));
    }
    
    /**
     * Display the specified return request.
     *
     * @param  \App\Models\ReturnRequest  $returnRequest
     * @return \Illuminate\View\View
     */
    public function show(ReturnRequest $returnRequest)
    {
        $returnRequest->load(['order', 'orderItem.product', 'customer']);
        
        return view('admin.return-requests.show', compact('returnRequest'));
    }
    
    /**
     * Update the status of a return request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReturnRequest  $returnRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeManufacturingReturn(Request $request, \App\Models\Order $order)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.order_item_id' => 'required|exists:order_items,id',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.pieces' => 'nullable|integer|min:0',
            'reason' => 'nullable|string|max:1000',
        ]);

        if (!$order->manufacturing_team_id) {
            return redirect()->back()->with('error', 'This order is not assigned to a manufacturing team.');
        }

        $createdCount = 0;

        foreach ($request->items as $itemData) {
            $qty = $itemData['quantity'] ?? 0;
            $pcs = $itemData['pieces'] ?? 0;

            if ($qty > 0 || $pcs > 0) {
                // Determine if item is actually checked
                if (isset($itemData['selected']) && $itemData['selected']) {
                    ReturnRequest::create([
                        'order_id' => $order->id,
                        'order_item_id' => $itemData['order_item_id'],
                        'customer_id' => $order->customer_id,
                        'reason' => $request->reason ?? 'Manufacturing Return',
                        'type' => 'return',
                        'quantity' => $qty,
                        'pieces' => $pcs,
                        'status' => 'pending',
                        'admin_notes' => 'Sent back to manufacturing team for return/correction.',
                    ]);
                    $createdCount++;
                }
            }
        }

        if ($createdCount === 0) {
            return redirect()->back()->with('error', 'Please select at least one item with a quantity or pieces greater than zero.');
        }

        return redirect()->back()->with('success', "{$createdCount} return request(s) created and sent to the manufacturing team.");
    }

    /**
     * Update the status of a return request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReturnRequest  $returnRequest
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, ReturnRequest $returnRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,processing,completed',
            'admin_notes' => 'nullable|string|max:1000',
            'dispatch_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'invoice_proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'note_type' => 'nullable|required_if:status,completed|in:credit,debit',
            'adjustment_amount' => 'nullable|numeric',
        ]);
        
        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'resolved_at' => $request->status === 'completed' ? now() : null,
        ];
        
        if ($request->hasFile('dispatch_proof_image')) {
            $data['dispatch_proof_image'] = $request->file('dispatch_proof_image')->store('dispatch_proofs', 'public');
        }

        if ($request->hasFile('invoice_proof_image')) {
            $data['invoice_proof_image'] = $request->file('invoice_proof_image')->store('return_invoices', 'public');
        }
        
        $returnRequest->update($data);

        // Auto-generate system invoice, Return Note and deduct loyalty points if completed
        if ($request->status === 'completed' && !$returnRequest->invoice_id) {
            $invoice = $this->generateReturnInvoice($returnRequest);
            $noteType = $request->input('note_type', 'credit'); // Use selected type, default to credit
            $adjustmentAmount = $request->input('adjustment_amount', 0);
            $this->generateReturnNote($returnRequest, $noteType, $adjustmentAmount); 
            $this->deductLoyaltyPointsForReturn($returnRequest, $invoice);
        }
        
        return redirect()->back()->with('success', 'Return request status updated successfully.');
    }

    /**
     * Generate a system invoice for the completed return request.
     *
     * @param  \App\Models\ReturnRequest  $returnRequest
     * @return void
     */
    private function generateReturnInvoice(ReturnRequest $returnRequest)
    {
        $order = $returnRequest->order;
        $orderItem = $returnRequest->orderItem;
        $product = $orderItem->product;
        $quantity = $returnRequest->quantity;
        $pieces = $returnRequest->pieces ?? 0;

        // Calculate pro-rated values from the original order item
        $unitPrice = $orderItem->price;
        $perQuantityPieces = $product->per_quantity_pieces > 0 ? $product->per_quantity_pieces : 1;
        $piecePrice = $product->piece_price ?? ($unitPrice / $perQuantityPieces);
        
        // For B2B orders or if piece price is 0, recalculate it based on unit price
        if ($order->customer_type === 'dealer' || $piecePrice <= 0) {
            $piecePrice = $unitPrice / $perQuantityPieces;
        }
        
        $unitTax = ($orderItem->quantity > 0) ? ($orderItem->tax / $orderItem->quantity) : 0;
        $pieceTax = $unitTax / $perQuantityPieces;

        $unitDiscount = ($orderItem->quantity > 0) ? (($orderItem->discount_amount ?? 0) / $orderItem->quantity) : 0;
        $pieceDiscount = $unitDiscount / $perQuantityPieces;

        $subtotal = ($unitPrice * $quantity) + ($piecePrice * $pieces);
        $taxTotal = ($unitTax * $quantity) + ($pieceTax * $pieces);
        $discountTotal = ($unitDiscount * $quantity) + ($pieceDiscount * $pieces);
        $total = $subtotal + $taxTotal - $discountTotal;

        // Create Invoice
        $invoiceCount = $order->invoices()->count() + 1;
        $invoiceNumber = $order->order_number . '-RET-' . str_pad($invoiceCount, 2, '0', STR_PAD_LEFT);

        $invoice = \App\Models\Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => $invoiceNumber,
            'subtotal' => $subtotal,
            'tax' => $taxTotal,
            'shipping' => 0, // Returns usually don't have shipping charges recorded this way here
            'discount_amount' => $discountTotal,
            'total' => $total,
        ]);

        // Create Invoice Item
        \App\Models\InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'order_item_id' => $orderItem->id,
            'quantity' => $quantity,
            'pieces' => $pieces, // Assuming InvoiceItem also needs pieces column? Let me check InvoiceItem model.
            'unit_price' => $unitPrice,
            'tax_amount' => $taxTotal,
            'discount_amount' => $discountTotal,
            'total' => $total,
        ]);

        // Link invoice to return request
        $returnRequest->update(['invoice_id' => $invoice->id]);

        return $invoice;
    }

    /**
     * Generate a Return Note (Credit or Debit) for a return request.
     *
     * @param  \App\Models\ReturnRequest  $returnRequest
     * @param  string  $type
     * @param  float  $adjustmentAmount
     * @return \App\Models\ReturnNote
     */
    private function generateReturnNote(ReturnRequest $returnRequest, $type = 'credit', $adjustmentAmount = 0)
    {
        $order = $returnRequest->order;
        $orderItem = $returnRequest->orderItem;
        $product = $orderItem->product;
        
        $prefix = ($type === 'credit') ? 'CN-' : 'DN-';
        $noteNumber = $prefix . strtoupper(\Illuminate\Support\Str::random(8));

        // Use same logic as invoice for amounts
        $quantity = $returnRequest->quantity;
        $pieces = $returnRequest->pieces ?? 0;
        $unitPrice = $orderItem->price;
        $perQuantityPieces = $product->per_quantity_pieces > 0 ? $product->per_quantity_pieces : 1;
        $piecePrice = $product->piece_price ?? ($unitPrice / $perQuantityPieces);
        
        // For B2B orders or if piece price is 0, recalculate it based on unit price
        if ($order->customer_type === 'dealer' || $piecePrice <= 0) {
            $piecePrice = $unitPrice / $perQuantityPieces;
        }
        
        $unitTax = ($orderItem->quantity > 0) ? ($orderItem->tax / $orderItem->quantity) : 0;
        $pieceTax = $unitTax / $perQuantityPieces;

        $unitDiscount = ($orderItem->quantity > 0) ? (($orderItem->discount_amount ?? 0) / $orderItem->quantity) : 0;
        $pieceDiscount = $unitDiscount / $perQuantityPieces;

        $subtotal = ($unitPrice * $quantity) + ($piecePrice * $pieces);
        $taxTotal = ($unitTax * $quantity) + ($pieceTax * $pieces);
        $discountTotal = ($unitDiscount * $quantity) + ($pieceDiscount * $pieces);
        
        // Apply manual adjustment to the total
        $total = $subtotal + $taxTotal - $discountTotal + $adjustmentAmount;

        // Create Return Note
        $returnNote = ReturnNote::create([
            'order_id' => $order->id,
            'return_request_id' => $returnRequest->id,
            'type' => $type,
            'note_number' => $noteNumber,
            'subtotal' => $subtotal,
            'tax' => $taxTotal,
            'discount_amount' => $discountTotal,
            'adjustment_amount' => $adjustmentAmount,
            'total' => $total,
        ]);

        // Create Return Note Item
        ReturnNoteItem::create([
            'return_note_id' => $returnNote->id,
            'order_item_id' => $orderItem->id,
            'quantity' => $quantity,
            'pieces' => $pieces,
            'unit_price' => $unitPrice,
            'tax_amount' => $taxTotal,
            'discount_amount' => $discountTotal,
            'total' => ($subtotal + $taxTotal - $discountTotal), // Item total doesn't include global note adjustment
        ]);

        return $returnNote;
    }

    /**
     * Deduct loyalty points from the customer based on the return value.
     *
     * @param  \App\Models\ReturnRequest  $returnRequest
     * @param  \App\Models\Invoice  $returnInvoice
     * @return void
     */
    private function deductLoyaltyPointsForReturn(ReturnRequest $returnRequest, \App\Models\Invoice $returnInvoice)
    {
        $order = $returnRequest->order;
        $customer = $returnRequest->customer;

        if (!$customer || $order->customer_type !== 'dealer') {
            return;
        }

        // Calculate points originally awarded for this order
        $originalPoints = floor($order->total / 1000);
        if ($order->total <= 2000) $originalPoints = 0;

        // Calculate points that SHOULD BE awarded after this return
        $newTotal = max(0, $order->total - $returnInvoice->total);
        $newPoints = floor($newTotal / 1000);
        if ($newTotal <= 2000) $newPoints = 0;

        $deduction = $originalPoints - $newPoints;

        if ($deduction > 0) {
            // We use a custom deduction to allow it to go to 0 but not negative if possible, 
            // or just use the model's method which handles the check.
            $customer->loyalty_points = max(0, $customer->loyalty_points - $deduction);
            $customer->save();
            
            // Log in admin notes
            $returnRequest->admin_notes .= "\n[System] Deducted {$deduction} loyalty points from customer.";
            $returnRequest->save();
        }
    }

    /**
     * Display the detailed Credit/Debit note for a return request.
     *
     * @param  \App\Models\ReturnRequest  $returnRequest
     * @param  \App\Models\ReturnNote  $returnNote
     * @return \Illuminate\View\View
     */
    public function showReturnNote(ReturnRequest $returnRequest, ReturnNote $returnNote)
    {
        $returnNote->load(['items.orderItem.product', 'order.customer', 'returnRequest']);
        
        return view('admin.return-requests.return-note', compact('returnRequest', 'returnNote'));
    }
}