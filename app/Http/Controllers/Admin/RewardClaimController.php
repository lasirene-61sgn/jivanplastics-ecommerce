<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardClaim;
use Illuminate\Http\Request;

class RewardClaimController extends Controller
{
    /**
     * Display a listing of reward claims.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $claims = RewardClaim::with(['customer', 'reward'])->latest()->paginate(20);
        return view('admin.reward-claims.index', compact('claims'));
    }

    /**
     * Display the specified reward claim.
     *
     * @param  \App\Models\RewardClaim  $claim
     * @return \Illuminate\View\View
     */
    public function show(RewardClaim $claim)
    {
        $claim->load(['customer', 'reward']);
        return view('admin.reward-claims.show', compact('claim'));
    }

    /**
     * Update the status of a reward claim.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RewardClaim  $claim
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, RewardClaim $claim)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,fulfilled',
            'admin_notes' => 'nullable|string|max:1000',
            'dispatch_proof_image' => 'nullable|image|max:2048',
            'invoice_proof_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_at' => in_array($request->status, ['approved', 'rejected', 'fulfilled']) ? now() : null,
        ];

        if ($request->hasFile('dispatch_proof_image')) {
            $data['dispatch_proof_image'] = $request->file('dispatch_proof_image')->store('reward_claims/dispatch', 'public');
        }

        if ($request->hasFile('invoice_proof_image')) {
            $data['invoice_proof_image'] = $request->file('invoice_proof_image')->store('reward_claims/invoices', 'public');
        }

        $claim->update($data);

        // Auto-generate invoice if fulfilled
        if ($request->status === 'fulfilled' && !$claim->invoice_id) {
            $this->generateRewardInvoice($claim);
        }

        return redirect()->back()->with('success', 'Reward claim status updated successfully.');
    }

    /**
     * Display the system-generated invoice for the reward claim.
     *
     * @param  \App\Models\RewardClaim  $claim
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showInvoice(RewardClaim $claim)
    {
        if (!$claim->invoice_id) {
            return redirect()->back()->with('error', 'No invoice generated for this claim.');
        }

        $invoice = $claim->invoice()->with(['items.orderItem.product'])->first();
        $order = null; // No order for reward claims

        return view('admin.orders.invoice', compact('order', 'invoice'));
    }

    /**
     * Generate a system invoice for the fulfilled reward claim.
     *
     * @param  \App\Models\RewardClaim  $claim
     * @return void
     */
    private function generateRewardInvoice(RewardClaim $claim)
    {
        $reward = $claim->reward;
        
        // Skip if not a product reward or no product linked
        if ($reward->type !== 'product' || !$reward->product_id) {
            return;
        }

        $product = $reward->product;
        $unitPrice = $reward->price ?? $product->price;
        
        // For rewards, subtotal is shown but discount is 100%
        $subtotal = $unitPrice;
        $discountTotal = $unitPrice; 
        $taxTotal = 0; // Usually rewards are tax-inclusive or tax-exempt in this context
        $total = 0;

        $invoiceNumber = 'REW-' . str_pad($claim->id, 6, '0', STR_PAD_LEFT);

        $invoice = \App\Models\Invoice::create([
            'order_id' => null, // Making this nullable allowed us to create it here
            'invoice_number' => $invoiceNumber,
            'subtotal' => $subtotal,
            'tax' => $taxTotal,
            'shipping' => 0,
            'discount_amount' => $discountTotal,
            'total' => $total,
        ]);

        \App\Models\InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'order_item_id' => null,
            'quantity' => 1,
            'unit_price' => $unitPrice,
            'tax_amount' => $taxTotal,
            'discount_amount' => $discountTotal,
            'total' => $total,
        ]);

        $claim->update(['invoice_id' => $invoice->id]);
    }
}