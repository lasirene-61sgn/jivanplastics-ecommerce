<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckAdminViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function check_admin_view_shows_partial_dispatch_form()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password', // Don't bcrypt here, let the model handle it
        ]);

        // Create a customer
        $customer = Customer::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'customer_type' => 'dealer',
        ]);

        // Create products
        $product1 = Product::factory()->create(['name' => 'Product 1', 'price' => 100]);
        $product2 = Product::factory()->create(['name' => 'Product 2', 'price' => 200]);

        // Create an order WITHOUT using the factory callback that creates items
        $order = Order::create([
            'order_number' => 'ORD-TEST456',
            'customer_id' => $customer->id,
            'customer_type' => $customer->customer_type,
            'manufacturing_status' => 'completed',
            'subtotal' => 300,
            'tax' => 30,
            'shipping' => 20,
            'total' => 350,
            'payment_method' => 'credit_card',
            'billing_address' => '123 Test Street',
            'billing_city' => 'Test City',
            'billing_state' => 'Test State',
            'billing_zip' => '12345',
            'billing_country' => 'Test Country',
            'shipping_address' => '123 Test Street',
            'shipping_city' => 'Test City',
            'shipping_state' => 'Test State',
            'shipping_zip' => '12345',
            'shipping_country' => 'Test Country',
        ]);

        // Create order items
        $item1 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'product_name' => $product1->name,
            'product_sku' => $product1->sku ?? 'SKU001',
            'quantity' => 2,
            'dispatched_quantity' => 1, // 1 already completed by manufacturing
            'price' => 100,
            'total' => 200,
        ]);

        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'product_name' => $product2->name,
            'product_sku' => $product2->sku ?? 'SKU002',
            'quantity' => 3,
            'dispatched_quantity' => 2, // 2 already completed by manufacturing
            'price' => 200,
            'total' => 600,
        ]);

        // Try to login as admin
        $loginResponse = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        
        // Check if login was successful
        echo "Login response status: " . $loginResponse->getStatusCode() . "\n";
        $loginResponse->dumpSession();
        
        // Visit the order page
        $response = $this->get("/admin/orders/{$order->id}");
        
        // Check if the partial dispatch form is present
        $response->assertSee('Partial Dispatch');
        $response->assertSee('Dispatch Selected Items');
        
        // Check the order status
        echo "Order manufacturing_status: " . $order->manufacturing_status . "\n";
        echo "Order has_pending_items: " . ($order->has_pending_items ? 'true' : 'false') . "\n";
        echo "Order total_pending_quantity: " . $order->total_pending_quantity . "\n";
        
        // Load items and check each item
        $order->load('items');
        foreach ($order->items as $index => $item) {
            echo "Item {$index}: has_pending_items=" . ($item->has_pending_items ? 'true' : 'false') . ", pending_quantity={$item->pending_quantity}\n";
        }
    }
}