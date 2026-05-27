<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Admin;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\DispatchImage;
use App\Models\User;

class CustomerDispatchViewTest extends TestCase
{
    use RefreshDatabase;
    
    protected $admin;
    protected $customer;
    protected $user;
    protected $order;
    protected $orderItem;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Create customer
        $this->customer = Customer::factory()->create([
            'email' => 'customer@example.com',
            'customer_type' => 'dealer',
        ]);
        
        // Create user for authentication
        $this->user = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Create product
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100,
        ]);
        
        // Create order
        $this->order = Order::factory()->create([
            'customer_id' => $this->customer->id,
            'customer_type' => 'dealer',
            'manufacturing_status' => 'completed',
            'dispatched_at' => now(),
            'status' => 'completed',
            'subtotal' => 500,
            'total' => 500,
        ]);
        
        // Create order item
        $this->orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 10,
            'dispatched_quantity' => 10,
            'price' => 100,
            'total' => 1000,
        ]);
    }
    
    /** @test */
    public function b2b_customer_can_see_dispatch_images_and_notes()
    {
        Storage::fake('public');
        
        // Create dispatch image
        $dispatchImage = DispatchImage::create([
            'order_id' => $this->order->id,
            'order_item_id' => $this->orderItem->id,
            'image_path' => 'dispatch-images/test-proof.jpg',
            'description' => '50 items completed and dispatched to dealer after 3 days',
            'uploaded_by' => 'admin',
        ]);
        
        // Login as customer
        $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);
        
        // Visit B2B order invoice page
        $response = $this->get(route('b2b.orders.invoice', $this->order));
        
        $response->assertStatus(200);
        $response->assertSee('Dispatch Information');
        $response->assertSee('Item Dispatch Proof');
        $response->assertSee('50 items completed and dispatched to dealer after 3 days');
        $response->assertSee('test-proof.jpg');
    }
    
    /** @test */
    public function b2c_customer_can_see_dispatch_images_and_notes()
    {
        Storage::fake('public');
        
        // Update customer to B2C
        $this->customer->update(['customer_type' => 'individual']);
        
        // Create dispatch image
        $dispatchImage = DispatchImage::create([
            'order_id' => $this->order->id,
            'order_item_id' => null, // Order-level dispatch
            'image_path' => 'dispatch-images/order-completed.jpg',
            'description' => 'Order completed and dispatched to customer',
            'uploaded_by' => 'admin',
        ]);
        
        // Login as customer
        $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);
        
        // Visit B2C order invoice page
        $response = $this->get(route('b2c.orders.invoice', $this->order));
        
        $response->assertStatus(200);
        $response->assertSee('Dispatch Information');
        $response->assertSee('Order Completion Proof');
        $response->assertSee('Order completed and dispatched to customer');
        $response->assertSee('order-completed.jpg');
    }
    
    /** @test */
    public function sales_team_can_see_dispatch_information_in_customer_orders()
    {
        Storage::fake('public');
        
        // Create dispatch image
        $dispatchImage = DispatchImage::create([
            'order_id' => $this->order->id,
            'order_item_id' => $this->orderItem->id,
            'image_path' => 'dispatch-images/sales-proof.jpg',
            'description' => 'Items dispatched as requested by sales team',
            'uploaded_by' => 'admin',
        ]);
        
        // Login as admin (sales team would access through admin panel)
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        
        // Visit admin order page
        $response = $this->get(route('admin.orders.show', $this->order));
        
        $response->assertStatus(200);
        $response->assertSee('Dispatch Information');
        $response->assertSee('Item Dispatch Proof');
        $response->assertSee('Items dispatched as requested by sales team');
    }
}
