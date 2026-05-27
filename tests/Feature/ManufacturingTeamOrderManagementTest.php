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

class ManufacturingTeamOrderManagementTest extends TestCase
{
    use RefreshDatabase;
    
    protected $admin;
    protected $manufacturingTeam;
    protected $customer;
    protected $order;
    protected $order2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create manufacturing team
        $this->manufacturingTeam = ManufacturingTeam::factory()->create([
            'email' => 'manufacturing@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Create customer
        $this->customer = Customer::factory()->create([
            'email' => 'customer@example.com',
        ]);

        // Create product
        $product = Product::factory()->create();

        // Create orders
        $this->order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'customer_type' => 'individual',
            'total' => 100.00,
        ]);
        
        $this->order2 = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'customer_type' => 'individual',
            'total' => 150.00,
        ]);

        // Create order items
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 1,
            'price' => 100.00,
            'total' => 100.00,
        ]);
        
        OrderItem::factory()->create([
            'order_id' => $this->order2->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => 1,
            'price' => 150.00,
            'total' => 150.00,
        ]);
    }

    /** @test */
    public function admin_can_allocate_order_to_manufacturing_team()
    {
        // Login as admin
        $response = $this->post(route('admin.login'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Allocate order to manufacturing team
        $response = $this->post(route('admin.orders.allocate'), [
            'order_ids' => [$this->order->id],
            'manufacturing_team_id' => $this->manufacturingTeam->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that order was allocated
        $this->order->refresh();
        $this->assertEquals($this->manufacturingTeam->id, $this->order->manufacturing_team_id);
        $this->assertEquals('allocated', $this->order->manufacturing_status);
        $this->assertNotNull($this->order->allocated_at);
    }

    /** @test */
    public function manufacturing_team_can_update_order_status()
    {
        // Allocate order to manufacturing team first
        $this->order->update([
            'manufacturing_team_id' => $this->manufacturingTeam->id,
            'manufacturing_status' => 'allocated',
        ]);

        // Login as manufacturing team
        $response = $this->post(route('manufacturing-team.login'), [
            'email' => 'manufacturing@example.com',
            'password' => 'password',
        ]);

        // Update order status to processing
        $response = $this->put(route('manufacturing-team.orders.update-status', $this->order), [
            'manufacturing_status' => 'processing',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that order status was updated
        $this->order->refresh();
        $this->assertEquals('processing', $this->order->manufacturing_status);
        $this->assertNotNull($this->order->allocated_at);
    }

    /** @test */
    public function manufacturing_team_can_complete_order()
    {
        // Allocate order to manufacturing team first
        $this->order->update([
            'manufacturing_team_id' => $this->manufacturingTeam->id,
            'manufacturing_status' => 'processing',
        ]);

        // Login as manufacturing team
        $response = $this->post(route('manufacturing-team.login'), [
            'email' => 'manufacturing@example.com',
            'password' => 'password',
        ]);

        // Update order status to completed
        $response = $this->put(route('manufacturing-team.orders.update-status', $this->order), [
            'manufacturing_status' => 'completed',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that order status was updated
        $this->order->refresh();
        $this->assertEquals('completed', $this->order->manufacturing_status);
        $this->assertNotNull($this->order->completed_at);
    }

    /** @test */
    public function admin_can_mark_order_as_dispatched()
    {
        // Allocate and complete order first
        $this->order->update([
            'manufacturing_team_id' => $this->manufacturingTeam->id,
            'manufacturing_status' => 'completed',
            'completed_at' => now(),
        ]);

        // Login as admin
        $response = $this->post(route('admin.login'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Mark order as dispatched
        $response = $this->post(route('admin.orders.dispatch', $this->order));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that order was marked as dispatched
        $this->order->refresh();
        $this->assertNotNull($this->order->dispatched_at);
        $this->assertEquals('completed', $this->order->status);
    }
    
    /** @test */
    public function manufacturing_team_can_bulk_accept_orders()
    {
        // Allocate orders to manufacturing team first
        $this->order->update([
            'manufacturing_team_id' => $this->manufacturingTeam->id,
            'manufacturing_status' => 'allocated',
        ]);
        
        $this->order2->update([
            'manufacturing_team_id' => $this->manufacturingTeam->id,
            'manufacturing_status' => 'allocated',
        ]);

        // Login as manufacturing team
        $response = $this->post(route('manufacturing-team.login'), [
            'email' => 'manufacturing@example.com',
            'password' => 'password',
        ]);

        // Bulk accept orders
        $response = $this->post(route('manufacturing-team.orders.bulk-accept'), [
            'order_ids' => [$this->order->id, $this->order2->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that orders status was updated
        $this->order->refresh();
        $this->order2->refresh();
        $this->assertEquals('processing', $this->order->manufacturing_status);
        $this->assertEquals('processing', $this->order2->manufacturing_status);
        $this->assertNotNull($this->order->allocated_at);
        $this->assertNotNull($this->order2->allocated_at);
    }
    
    /** @test */
    public function manufacturing_team_can_bulk_update_order_status()
    {
        // Allocate and accept orders first
        $this->order->update([
            'manufacturing_team_id' => $this->manufacturingTeam->id,
            'manufacturing_status' => 'processing',
            'allocated_at' => now(),
        ]);
        
        $this->order2->update([
            'manufacturing_team_id' => $this->manufacturingTeam->id,
            'manufacturing_status' => 'processing',
            'allocated_at' => now(),
        ]);

        // Login as manufacturing team
        $response = $this->post(route('manufacturing-team.login'), [
            'email' => 'manufacturing@example.com',
            'password' => 'password',
        ]);

        // Bulk update orders to completed
        $response = $this->post(route('manufacturing-team.orders.bulk-update'), [
            'order_ids' => [$this->order->id, $this->order2->id],
            'manufacturing_status' => 'completed',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check that orders status was updated
        $this->order->refresh();
        $this->order2->refresh();
        $this->assertEquals('completed', $this->order->manufacturing_status);
        $this->assertEquals('completed', $this->order2->manufacturing_status);
        $this->assertNotNull($this->order->completed_at);
        $this->assertNotNull($this->order2->completed_at);
    }
}