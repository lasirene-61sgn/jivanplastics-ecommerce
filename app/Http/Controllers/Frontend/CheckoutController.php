<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\OrderPlacedMail;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Resolve target level and percentage for B2B discounts
     */
    private function resolveB2BDiscountDetails($product, $customer)
    {
        if (!$customer || $customer->customer_type !== 'dealer' || !$product) {
            return null;
        }

        // 1. Check Product level override
        if (!empty($product->b2b_discount_percentage) && $product->b2b_discount_percentage > 0) {
            return [
                'type' => 'Product',
                'name' => $product->name,
                'percentage' => (float) $product->b2b_discount_percentage
            ];
        }

        // 2. Check Subcategory level override
        if ($product->subcategory && !empty($product->subcategory->b2b_discount_percentage) && $product->subcategory->b2b_discount_percentage > 0) {
            return [
                'type' => 'Subcategory',
                'name' => $product->subcategory->name,
                'percentage' => (float) $product->subcategory->b2b_discount_percentage
            ];
        }

        // 3. Check Category level override
        if ($product->category && !empty($product->category->b2b_discount_percentage) && $product->category->b2b_discount_percentage > 0) {
            return [
                'type' => 'Category',
                'name' => $product->category->name,
                'percentage' => (float) $product->category->b2b_discount_percentage
            ];
        }

        // 4. Fallback: No default discount
        return null;
    }

    /**
     * Display the checkout page.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return redirect()->route('cart.index')->with('error', 'Customer information not found.');
        }

        $subtotal = 0;
        $totalGst = 0;
        $totalDealerDiscount = 0;
        $b2bDiscountAmount = 0;
        $b2bBreakdown = [];

        foreach ($cart as $cartKey => $item) {
            $productId = $item['product_id'] ?? $cartKey;
            $product = Product::with(['category', 'subcategory'])->find($productId);

            $unitToPay = $item['price'];
            $unitBase = $item['original_price'] ?? ($product ? $product->price : $item['price']);
            
            $lineBase = $unitBase * $item['quantity'];
            $lineDisc = $unitToPay * $item['quantity'];
            
            $subtotal += $lineBase;
            $totalDealerDiscount += max(0, $lineBase - $lineDisc);

            // Variation lookup
            $variation = null;
            if ($product) {
                $variation = $product->variations()
                    ->where(function($q) use ($item) {
                        $val = $item['size'] ?? null;
                        if (empty($val)) $q->whereNull('size')->orWhere('size', '');
                        else $q->where('size', $val);
                    })
                    ->where(function($q) use ($item) {
                        $val = $item['thickness'] ?? null;
                        if (empty($val)) $q->whereNull('thickness')->orWhere('thickness', '');
                        else $q->where('thickness', $val);
                    })
                    ->where(function($q) use ($item) {
                        $val = $item['color'] ?? null;
                        if (empty($val)) $q->whereNull('color')->orWhere('color', '');
                        else $q->where('color', $val);
                    })
                    ->first();
            }

            // GST calculation
            $gstPct = $item['gst_percentage'] ?? ($variation ? $variation->gst_percentage : ($product->gst_percentage ?? 0));
            $lineGst = $lineDisc * ($gstPct / 100);
            $totalGst += $lineGst;

            // B2B Discount resolution (if not already reflected in dealer net price)
            if ($customer->customer_type !== 'dealer') {
                $b2bRule = $this->resolveB2BDiscountDetails($product, $customer);
                if ($b2bRule && $b2bRule['percentage'] > 0) {
                    $itemDiscount = ($lineDisc * $b2bRule['percentage']) / 100;
                    $b2bDiscountAmount += $itemDiscount;

                    $key = $b2bRule['type'] . ': ' . $b2bRule['name'] . ' (' . $b2bRule['percentage'] . '%)';
                    if (!isset($b2bBreakdown[$key])) {
                        $b2bBreakdown[$key] = 0;
                    }
                    $b2bBreakdown[$key] += $itemDiscount;
                }
            }
        }

        $shipping = 0;
        $netSubtotal = $subtotal - $totalDealerDiscount;
        $total = $netSubtotal + $totalGst - $b2bDiscountAmount;

        return view('frontend.checkout.index', compact(
            'cart',
            'customer',
            'subtotal',
            'netSubtotal',
            'totalDealerDiscount',
            'totalGst',
            'shipping',
            'b2bDiscountAmount',
            'b2bBreakdown',
            'total'
        ));
    }

    /**
     * Process the checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'use_same_address' => 'nullable|boolean',
            'shipping_address' => 'required_if:use_same_address,0|nullable|string|max:255',
            'shipping_city' => 'required_if:use_same_address,0|nullable|string|max:100',
            'shipping_state' => 'required_if:use_same_address,0|nullable|string|max:100',
            'shipping_zip' => 'required_if:use_same_address,0|nullable|string|max:20',
            'shipping_country' => 'required_if:use_same_address,0|nullable|string|max:100',
            'payment_method' => 'required|string|in:cod,bank_transfer',
        ], [
            'shipping_address.required_if' => 'Shipping address is required when different from billing.',
            'shipping_city.required_if' => 'Shipping city is required when different from billing.',
            'shipping_state.required_if' => 'Shipping state is required when different from billing.',
            'shipping_zip.required_if' => 'Shipping ZIP code is required when different from billing.',
            'shipping_country.required_if' => 'Shipping country is required when different from billing.',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return redirect()->route('cart.index')->with('error', 'Customer information not found.');
        }

        if ($request->payment_method === 'cod' && !$customer->is_cod_allowed) {
            $paymentLabel = ($customer->customer_type === 'dealer') ? 'Draft Payment' : 'Cash on Delivery';
            return redirect()->back()->with('error', $paymentLabel . ' is not allowed for your account. Please use Bank Transfer / Online.');
        }

        $subtotal = 0;
        $totalGst = 0;
        $b2bDiscountAmount = 0;
        $items = [];

        foreach ($cart as $cartKey => $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;

            $productId = $item['product_id'] ?? $cartKey;
            $product = Product::with(['category', 'subcategory'])->find($productId);

            $gstAmount = 0;
            $originalPrice = $item['price'];
            $discountAmount = 0;

            $variation = null;
            if ($product) {
                $variation = $product->variations()
                    ->where(function($q) use ($item) {
                        $val = $item['size'] ?? null;
                        if (empty($val)) $q->whereNull('size')->orWhere('size', '');
                        else $q->where('size', $val);
                    })
                    ->where(function($q) use ($item) {
                        $val = $item['thickness'] ?? null;
                        if (empty($val)) $q->whereNull('thickness')->orWhere('thickness', '');
                        else $q->where('thickness', $val);
                    })
                    ->where(function($q) use ($item) {
                        $val = $item['color'] ?? null;
                        if (empty($val)) $q->whereNull('color')->orWhere('color', '');
                        else $q->where('color', $val);
                    })
                    ->first();
            }

            if ($product) {
                $gstInclusiveOriginal = $variation ? $variation->total_price : $product->price;
                $gstPct = $item['gst_percentage'] ?? ($variation ? $variation->gst_percentage : ($product->gst_percentage ?? 0));
                $originalBasePrice = $gstInclusiveOriginal / (1 + ($gstPct / 100));

                $isPieces = isset($item['is_pieces']) && $item['is_pieces'];
                $tempPerUnitPieces = $variation ? $variation->total_pieces : ($product->per_quantity_pieces ?? 1);

                if ($isPieces && $tempPerUnitPieces > 0) {
                    $originalBasePrice = $originalBasePrice / $tempPerUnitPieces;
                }

                $originalPrice = $originalBasePrice;

                if ($gstPct > 0) {
                    $gstAmount = $itemTotal * ($gstPct / 100);
                    $totalGst += $gstAmount;
                }

                $itemOriginalPrice = $item['original_price'] ?? $originalBasePrice;
                $originalPrice = $itemOriginalPrice;
                $discountAmount = max(0, ($itemOriginalPrice - $item['price']) * $item['quantity']);
            }

            $isPieces = isset($item['is_pieces']) && $item['is_pieces'];
            $perUnitPieces = $variation ? $variation->total_pieces : ($product->per_quantity_pieces ?? 1);
            $totalPieces = $perUnitPieces * $item['quantity'];
            $piecePrice = $variation ? $variation->piece_price : ($product->piece_price ?? 0);

            if ($isPieces) {
                $perUnitPieces = 1;
                $totalPieces = $item['quantity'];
                $piecePrice = $item['price'];
            }

            if ($customer->customer_type !== 'dealer') {
                $b2bRule = $this->resolveB2BDiscountDetails($product, $customer);
                if ($b2bRule && $b2bRule['percentage'] > 0) {
                    $b2bDiscountAmount += ($itemTotal * $b2bRule['percentage']) / 100;
                }
            }

            $items[] = [
                'product_id' => $productId,
                'product_name' => $item['name'],
                'product_sku' => $product->sku ?? null,
                'quantity' => $item['quantity'],
                'per_unit_pieces' => $perUnitPieces,
                'total_pieces' => $totalPieces,
                'price' => $item['price'],
                'piece_price' => $piecePrice,
                'total' => $itemTotal,
                'gst_amount' => $gstAmount,
                'original_price' => $originalPrice,
                'discount_amount' => $discountAmount / max($item['quantity'], 1),
                'size' => $item['size'] ?? null,
                'thickness' => $item['thickness'] ?? null,
                'color' => $item['color'] ?? null,
            ];
        }

        $shipping = 0;
        $total = $subtotal + $shipping + $totalGst - $b2bDiscountAmount;

        $onlineDiscountPercent = (float) ($customer->online_discount > 0 ? $customer->online_discount : ($customer->bank_transfer_discount ?? 0));
        $draftDiscountPercent = (float) ($customer->draft_discount ?? 0);

        $onlineDiscountAmount = 0;
        $draftDiscountAmount = 0;

        // Calculate Payment Discounts on Net Subtotal (WITHOUT GST)
        if ($request->payment_method === 'bank_transfer' && $onlineDiscountPercent > 0) {
            $onlineDiscountAmount = ($subtotal * $onlineDiscountPercent) / 100;
            $total -= $onlineDiscountAmount;
        } elseif ($request->payment_method === 'cod' && $draftDiscountPercent > 0) {
            $draftDiscountAmount = ($subtotal * $draftDiscountPercent) / 100;
            $total -= $draftDiscountAmount;
        }

        $originalSubtotal = array_reduce($items, function($carry, $item) {
            return $carry + ($item['original_price'] * $item['quantity']);
        }, 0);
        $totalOrderDiscount = array_reduce($items, function($carry, $item) {
            return $carry + ($item['discount_amount'] * $item['quantity']);
        }, 0);

        $useSameAddress = $request->boolean('use_same_address');

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => $customer->id,
            'customer_type' => $customer->customer_type,
            'subtotal' => $subtotal,
            'original_subtotal' => $originalSubtotal,
            'discount_amount' => $totalOrderDiscount,
            'tax' => $totalGst,
            'shipping' => $shipping,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'billing_address' => $request->billing_address,
            'billing_city' => $request->billing_city,
            'billing_state' => $request->billing_state,
            'billing_zip' => $request->billing_zip,
            'billing_country' => $request->billing_country,
            'shipping_address' => $useSameAddress ? $request->billing_address : $request->shipping_address,
            'shipping_city' => $useSameAddress ? $request->billing_city : $request->shipping_city,
            'shipping_state' => $useSameAddress ? $request->billing_state : $request->shipping_state,
            'shipping_zip' => $useSameAddress ? $request->billing_zip : $request->shipping_zip,
            'shipping_country' => $useSameAddress ? $request->billing_country : $request->shipping_country,
            'b2b_discount_amount' => $b2bDiscountAmount,
            'bank_transfer_discount_amount' => $onlineDiscountAmount,
            'online_discount_amount' => $onlineDiscountAmount,
            'draft_discount_amount' => $draftDiscountAmount,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'product_sku' => $item['product_sku'],
                'quantity' => $item['quantity'],
                'per_unit_pieces' => $item['per_unit_pieces'],
                'total_pieces' => $item['total_pieces'],
                'price' => $item['price'],
                'piece_price' => $item['piece_price'],
                'tax' => $item['gst_amount'],
                'total' => $item['total'],
                'original_price' => $item['original_price'],
                'discount_amount' => $item['discount_amount'],
                'size' => $item['size'],
                'thickness' => $item['thickness'],
                'color' => $item['color'],
            ]);
        }

        try {
            Mail::to($customer->email)->send(new OrderPlacedMail($order));
            $this->sendWhatsAppNotification($customer->phone, $order);
        } catch (\Exception $e) {
            Log::error("B2B Order Notification Error: " . $e->getMessage());
        }

        try {
            app(\App\Services\FirebaseNotificationService::class)->sendNotification(
                'admin',
                'New Order Placed!',
                "A new order (#{$order->order_number}) has been placed.",
                ['icon' => 'success', 'order_id' => $order->id]
            );
        } catch (\Exception $e) {
            Log::error("Firebase Notification Error: " . $e->getMessage());
        }

        session()->forget('cart');

        return redirect()->route('checkout.success', $order)->with('success', 'Your order has been placed successfully!');
    }

    private function sendWhatsAppNotification($phone, $order)
    {
        $apiUrl = env('WHATSAPP_API_URL');
        $apiKey = env('WHATSAPP_API_KEY');
        $templateName = env('WHATSAPP_TEMPLATE_NAME');

        if (empty($apiUrl) || empty($apiKey)) {
            Log::warning("WhatsApp API credentials missing.");
            return false;
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => 'en'],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $order->order_number],
                            ['type' => 'text', 'text' => $order->total],
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function success(Order $order)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your order.');
        }

        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();

        if ($order->customer_id != $customer->id) {
            return redirect()->route('home')->with('error', 'You are not authorized to view this order.');
        }

        return view('frontend.checkout.success', compact('order'));
    }
}