<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Console\Command;

class TestOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test order for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customer = Customer::first();
        $product = Product::first();
        
        if (!$customer || !$product) {
            $this->error('No customer or product found');
            return;
        }
        
        $order = Order::create([
            'order_number' => 'TEST-001',
            'customer_id' => $customer->id,
            'customer_type' => 'individual',
            'subtotal' => $product->price,
            'tax' => 0,
            'shipping' => 0,
            'total' => $product->price,
            'status' => 'pending',
            'payment_method' => 'cod',
            'billing_address' => 'Test Address',
            'billing_city' => 'Test City',
            'billing_state' => 'Test State',
            'billing_zip' => '123456',
            'billing_country' => 'India',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Test City',
            'shipping_state' => 'Test State',
            'shipping_zip' => '123456',
            'shipping_country' => 'India',
        ]);
        
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => 'TEST-SKU',
            'quantity' => 1,
            'price' => $product->price,
            'total' => $product->price,
        ]);
        
        $this->info('Test order created successfully');
    }
}