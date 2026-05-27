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

class DispatchImageUploadTest extends TestCase
{
    use RefreshDatabase;
    
    protected $admin;
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
        $customer = Customer::factory()->create();
        
        // Create product
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 100,
        ]);
        
        // Create order
        $this->order = Order::factory()->create([
            'customer_id' => $customer->id,
            'customer_type' => 'dealer',
            'manufacturing_status' => 'completed',
            'subtotal' => 500,
            'total' => 500,
        ]);
        
        // Create order item
        $this->orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 10,
            'dispatched_quantity' => 5, // 5 already dispatched
            'price' => 100,
            'total' => 1000,
        ]);
    }
    
    /** @test */
    public function admin_can_upload_image_during_partial_dispatch()
    {
        Storage::fake('public');
        
        // Login as admin
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        
        // Create a fake image
        $image = UploadedFile::fake()->image('dispatch-proof.jpg');
        
        // Perform partial dispatch with image upload
        $response = $this->put(route('admin.orders.partial-dispatch', $this->order), [
            'dispatched_quantities' => [$this->orderItem->id => 3], // Dispatch 3 more items
            'dispatch_images' => [$this->orderItem->id => $image],
            'dispatch_descriptions' => [$this->orderItem->id => 'Completed 3 items for dealer'],
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Assert the image was stored
        Storage::disk('public')->assertExists('dispatch-images/' . $image->hashName());
        
        // Assert DispatchImage record was created
        $this->assertDatabaseHas('dispatch_images', [
            'order_id' => $this->order->id,
            'order_item_id' => $this->orderItem->id,
            'description' => 'Completed 3 items for dealer',
            'uploaded_by' => 'admin',
        ]);
        
        // Check that dispatched quantity was updated
        $this->orderItem->refresh();
        $this->assertEquals(8, $this->orderItem->dispatched_quantity); // 5 + 3
    }
    
    /** @test */
    public function admin_can_upload_image_during_complete_dispatch()
    {
        Storage::fake('public');
        
        // Login as admin
        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);
        
        // Create a fake image
        $image = UploadedFile::fake()->image('complete-dispatch.jpg');
        
        // Mark entire order as dispatched with image
        $response = $this->post(route('admin.orders.dispatch', $this->order), [
            'dispatch_image' => $image,
            'dispatch_description' => 'All items completed and dispatched to dealer after 3 days',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Assert the image was stored
        Storage::disk('public')->assertExists('dispatch-images/' . $image->hashName());
        
        // Assert DispatchImage record was created
        $this->assertDatabaseHas('dispatch_images', [
            'order_id' => $this->order->id,
            'order_item_id' => null, // Order-level dispatch
            'description' => 'All items completed and dispatched to dealer after 3 days',
            'uploaded_by' => 'admin',
        ]);
        
        // Check that order was marked as dispatched
        $this->order->refresh();
        $this->assertNotNull($this->order->dispatched_at);
        $this->assertEquals('completed', $this->order->status);
    }
    
    /** @test */
    public function dispatch_image_has_correct_url_attribute()
    {
        Storage::fake('public');
        
        // Create a dispatch image record
        $dispatchImage = DispatchImage::create([
            'order_id' => $this->order->id,
            'order_item_id' => $this->orderItem->id,
            'image_path' => 'dispatch-images/test-image.jpg',
            'description' => 'Test dispatch',
            'uploaded_by' => 'admin',
        ]);
        
        // Check that the image URL is generated correctly
        $this->assertStringContainsString('dispatch-images/test-image.jpg', $dispatchImage->image_url);
    }
}
