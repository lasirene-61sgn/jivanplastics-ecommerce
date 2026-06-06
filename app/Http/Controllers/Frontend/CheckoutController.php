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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Display the checkout page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }
        
        $cart = session()->get('cart', []);
        
        // Check if cart is empty
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        // Get customer
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('cart.index')->with('error', 'Customer information not found.');
        }
        
        // Calculate totals with GST
        $subtotal = 0;
        $totalGst = 0;
        
        foreach ($cart as $cartKey => $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;
            
            // Get product to calculate GST
            $productId = $item['product_id'] ?? $cartKey;
            $product = Product::find($productId);
            if ($product && $product->gst_percentage > 0) {
                $gstAmount = $itemTotal * $product->gst_percentage / 100;
                $totalGst += $gstAmount;
            }
        }
        
        $shipping = 0; // Free shipping
        $total = $subtotal + $shipping + $totalGst;
        
        return view('frontend.checkout.index', compact('cart', 'customer', 'subtotal', 'totalGst', 'shipping', 'total'));
    }
    
    /**
     * Process the checkout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function process(Request $request)
    {
        // Validate the request
        $request->validate([
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_zip' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'use_same_address' => 'boolean',
            'shipping_address' => 'required_if:use_same_address,0|string|max:255',
            'shipping_city' => 'required_if:use_same_address,0|string|max:100',
            'shipping_state' => 'required_if:use_same_address,0|string|max:100',
            'shipping_zip' => 'required_if:use_same_address,0|string|max:20',
            'shipping_country' => 'required_if:use_same_address,0|string|max:100',
            'payment_method' => 'required|string|in:cod,bank_transfer',
        ], [
            'shipping_address.required_if' => 'The shipping address field is required when not using the same address.',
            'shipping_city.required_if' => 'The shipping city field is required when not using the same address.',
            'shipping_state.required_if' => 'The shipping state field is required when not using the same address.',
            'shipping_zip.required_if' => 'The shipping zip field is required when not using the same address.',
            'shipping_country.required_if' => 'The shipping country field is required when not using the same address.',
        ]);
        
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to checkout.');
        }
        
        $cart = session()->get('cart', []);
        
        // Check if cart is empty
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        // Get customer
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        if (!$customer) {
            return redirect()->route('cart.index')->with('error', 'Customer information not found.');
        }

        // Check if payment method is COD and if it's allowed
        if ($request->payment_method === 'cod' && !$customer->is_cod_allowed) {
            return redirect()->back()->with('error', 'Cash on Delivery is not available for your account. Please use Bank Transfer.');
        }
        
        // Calculate totals with GST
        $subtotal = 0;
        $totalGst = 0;
        $items = [];
        
        foreach ($cart as $cartKey => $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;
            
            // Get product to calculate GST and Original Prices
            $productId = $item['product_id'] ?? $cartKey;
            $product = Product::find($productId);
            $gstAmount = 0;
            $originalPrice = $item['price'];
            $discountAmount = 0;
            
            // 1. Find the variation first
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

            // 2. Calculate prices based on variation
            if ($product) {
                // Determine the original base price (GST-exclusive)
                $gstInclusiveOriginal = $variation ? $variation->total_price : $product->price;
                $gstPct = $variation ? $variation->gst_percentage : ($product->gst_percentage ?? 0);
                $originalBasePrice = $gstInclusiveOriginal / (1 + ($gstPct / 100));
                
                $isPieces = isset($item['is_pieces']) && $item['is_pieces'];
                $tempPerUnitPieces = $variation ? $variation->total_pieces : ($product->per_quantity_pieces ?? 1);
                
                if ($isPieces && $tempPerUnitPieces > 0) {
                    $originalBasePrice = $originalBasePrice / $tempPerUnitPieces;
                }

                $originalPrice = $originalBasePrice;
                
                if ($gstPct > 0) {
                    $gstAmount = $itemTotal * $gstPct / 100;
                    $totalGst += $gstAmount;
                }
                
                // Calculate discount based on base prices
                $discountAmount = max(0, ($originalBasePrice - $item['price']) * $item['quantity']);
            }
            
            $isPieces = isset($item['is_pieces']) && $item['is_pieces'];
            $perUnitPieces = $variation ? $variation->total_pieces : ($product->per_quantity_pieces ?? 1);
            $totalPieces = $perUnitPieces * $item['quantity'];
            $piecePrice = $variation ? $variation->piece_price : ($product->piece_price ?? 0);
            
            if ($isPieces) {
                $perUnitPieces = 1;
                $totalPieces = $item['quantity'];
                $piecePrice = $item['price']; // Since piece price is essentially the actual price when ordering pieces
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
                'discount_amount' => $discountAmount / $item['quantity'], // Store per-unit discount
                'size' => $item['size'] ?? null,
                'thickness' => $item['thickness'] ?? null,
                'color' => $item['color'] ?? null,
            ];
        }
        
        $shipping = 0; // Free shipping
        $total = $subtotal + $shipping + $totalGst;

        // Calculate Bank Transfer Discount
        $btDiscountAmount = 0;
        if ($request->payment_method === 'bank_transfer' && $customer->bank_transfer_discount > 0) {
            $btDiscountAmount = ($total * $customer->bank_transfer_discount) / 100;
            $total -= $btDiscountAmount;
        }
        
        // Calculate Order-wide totals for immutable storage
        $originalSubtotal = array_reduce($items, function($carry, $item) {
            return $carry + ($item['original_price'] * $item['quantity']);
        }, 0);
        $totalOrderDiscount = array_reduce($items, function($carry, $item) {
            return $carry + ($item['discount_amount'] * $item['quantity']);
        }, 0);

        // Create order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => $customer->id,
            'customer_type' => $customer->customer_type,
            'subtotal' => $subtotal,
            'original_subtotal' => $originalSubtotal,
            'discount_amount' => $totalOrderDiscount,
            'tax' => $totalGst, // Using tax field for GST
            'shipping' => $shipping,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'billing_address' => $request->billing_address,
            'billing_city' => $request->billing_city,
            'billing_state' => $request->billing_state,
            'billing_zip' => $request->billing_zip,
            'billing_country' => $request->billing_country,
            'shipping_address' => $request->use_same_address ? $request->billing_address : $request->shipping_address,
            'shipping_city' => $request->use_same_address ? $request->billing_city : $request->shipping_city,
            'shipping_state' => $request->use_same_address ? $request->billing_state : $request->shipping_state,
            'shipping_zip' => $request->use_same_address ? $request->billing_zip : $request->shipping_zip,
            'shipping_country' => $request->use_same_address ? $request->billing_country : $request->shipping_country,
            'bank_transfer_discount_amount' => $btDiscountAmount,
        ]);
        
        // Create order items
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
        
        // --- START OF NEW NOTIFICATION LOGIC ---
        try {
            // 1. Send Automatic Email to Dealer
            // Make sure you have created the OrderPlaced mailable: php artisan make:mail OrderPlaced
            Mail::to($customer->email)->send(new OrderPlacedMail($order));

            // 2. Send Automatic WhatsApp to Dealer
            // We use a helper function to keep the logic clean
            $this->sendWhatsAppNotification($customer->phone, $order);

        } catch (\Exception $e) {
            // We log the error so the order processing doesn't stop if the API is down
            \Log::error("B2B Order Notification Error: " . $e->getMessage());
        }
        // --- END OF NEW NOTIFICATION LOGIC ---

        // --- START OF FIREBASE NOTIFICATION LOGIC ---
        try {
            app(\App\Services\FirebaseNotificationService::class)->sendNotification(
                'admin', // Topic name
                'New Order Placed!',
                "A new order (#{$order->order_number}) has been placed.",
                ['icon' => 'success', 'order_id' => $order->id]
            );
        } catch (\Exception $e) {
            \Log::error("Firebase Notification Error: " . $e->getMessage());
        }
        // --- END OF FIREBASE NOTIFICATION LOGIC ---

        // Clear the cart
        session()->forget('cart');
        
        return redirect()->route('checkout.success', $order)->with('success', 'Your order has been placed successfully!');
    }

    /**
     * This is the function you are missing. 
     * It handles the connection to the WhatsApp API.
     */
    private function sendWhatsAppNotification($phone, $order)
    {
        // 1. Get credentials from your .env file
        $apiUrl = env('WHATSAPP_API_URL');
        $apiKey = env('WHATSAPP_API_KEY');
        $templateName = env('WHATSAPP_TEMPLATE_NAME');

        if (empty($apiUrl) || empty($apiKey)) {
            \Log::warning("WhatsApp API credentials are not set in .env. Skipping WhatsApp notification.");
            return false;
        }

        // 2. Make the API Call
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
                            ['type' => 'text', 'text' => $order->order_number], // Variable 1 in template
                            ['type' => 'text', 'text' => $order->total],        // Variable 2 in template
                        ]
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Display the checkout success page.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function success(Order $order)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your order.');
        }
        
        // Get customer
        $user = Auth::user();
        $customer = Customer::where('email', $user->email)->first();
        
        // Ensure the order belongs to the current customer
        if ($order->customer_id != $customer->id) {
            return redirect()->route('home')->with('error', 'You are not authorized to view this order.');
        }
        
        return view('frontend.checkout.success', compact('order'));
    }
}