<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\ManufacturingTeam;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartialOrderFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function manufacturing_team_can_partially_complete_order_items()
    {
        // Create a manufacturing team
        $manufacturingTeam = ManufacturingTeam::factory()->create([
            'factory_name' => 'Test Manufacturing Team',
            'email' => 'manufacturing@example.com',
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
            'order_number' => 'ORD-TEST123',
            'customer_id' => $customer->id,
            'customer_type' => $customer->customer_type,
            'manufacturing_team_id' => $manufacturingTeam->id,
            'manufacturing_status' => 'processing',
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
            'dispatched_quantity' => 0,
            'price' => 100,
            'total' => 200,
        ]);

        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'product_name' => $product2->name,
            'product_sku' => $product2->sku ?? 'SKU002',
            'quantity' => 3,
            'dispatched_quantity' => 0,
            'price' => 200,
            'total' => 600,
        ]);

        // Login as manufacturing team
        $this->post('/manufacturing-team/login', [
            'email' => 'manufacturing@example.com',
            'password' => 'password',
        ]);

        // Partially complete items (complete 1 of item1 and 2 of item2)
        $response = $this->put("/manufacturing-team/orders/{$order->id}/partial-complete", [
            'completed_quantities' => [
                $item1->id => 1,
                $item2->id => 2,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Refresh models
        $item1->refresh();
        $item2->refresh();
        $order->refresh();

        // Check that items have been partially completed
        $this->assertEquals(1, $item1->dispatched_quantity);
        $this->assertEquals(1, $item1->pending_quantity);
        $this->assertEquals(2, $item2->dispatched_quantity);
        $this->assertEquals(1, $item2->pending_quantity);

        // Check that order status is still processing
        $this->assertEquals('processing', $order->manufacturing_status);
        $this->assertFalse($order->is_fully_dispatched);
        $this->assertEquals(3, $order->total_dispatched_quantity);
        $this->assertEquals(2, $order->total_pending_quantity);
    }

    /** @test */
    public function admin_can_partially_dispatch_order_items()
    {
        // Create an admin user
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
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

        // Login as admin
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Partially dispatch items (dispatch 1 more of item1 and 1 more of item2)
        $response = $this->put("/admin/orders/{$order->id}/partial-dispatch", [
            'dispatched_quantities' => [
                $item1->id => 1,
                $item2->id => 1,
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Refresh models
        $item1->refresh();
        $item2->refresh();
        $order->refresh();

        // Check that items have been partially dispatched
        $this->assertEquals(2, $item1->dispatched_quantity);
        $this->assertEquals(0, $item1->pending_quantity);
        $this->assertEquals(3, $item2->dispatched_quantity);
        $this->assertEquals(0, $item2->pending_quantity);

        // Check that order is now fully dispatched
        $this->assertTrue($order->is_fully_dispatched);
        $this->assertEquals(5, $order->total_dispatched_quantity);
        $this->assertEquals(0, $order->total_pending_quantity);

        // Check that order status is completed and dispatched timestamp is set
        $this->assertEquals('completed', $order->status);
        $this->assertNotNull($order->dispatched_at);
    }
}